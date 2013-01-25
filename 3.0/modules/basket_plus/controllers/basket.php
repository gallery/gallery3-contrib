<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
 /*
 * 2011-05-01 Added function view_all_orders()
 */
class Basket_Controller extends Controller {

  public function view_basket($pp="") {

    $template = new Theme_View("page.html", "basket");

    $basket = Session_Basket::get();
    if (isset($pp)){
      if ($pp=="nopp"){
        $basket->disablepp();
      }
      elseif ($pp=="ppon"){
        $basket->enablepp();
      }
    }

    $view = new View("view_basket.html");
    $view->basket = $basket;

    $template->content = $view;

    print $template;
  }

  public function preview($id) {
    $item = ORM::factory("item", $id);

    print "<img src='".$item->resize_url()."'></img>";
  }

  public function view_orders() {
  //get all incomplete orders and show them in a view
    self::check_view_orders();
    $template = new Theme_View("page.html", "basket");

    $incomplete_orders = ORM::factory("order")->where('status',"<",20)->find_all();

    $view = new View("view_orders.html");

    $view->orders = $incomplete_orders;

    $template->content = $view;

    print $template;
  }
  
  public function view_all_orders() {
  //get all orders and show them in a view
    self::check_view_orders();
    $template = new Theme_View("page.html", "basket");

    $all_orders = ORM::factory("order")->find_all();

    $view = new View("view_orders.html");

    $view->orders = $all_orders;

    $template->content = $view;

    print $template;
  }

  public function view_ipn($orderid){
    self::check_view_orders();

    $template = new Theme_View("page.html", "basket");

    $order = ORM::factory("order")->where("id","=",$orderid)->find();
    $ipn_messages = ORM::factory("ipn_message")->where("key","=",$orderid)->find_all();
    //$ipn_messages = ORM::factory("ipn_message")->find_all();

    $view = new View("view_ipn.html");

    $view->order = $order;
    $view->ipn_messages = $ipn_messages;

    $template->content = $view;

    print $template;
  }

  public function check_view_orders() {
    if (!basket::can_view_orders()){
      die("Invalid access.");
    }
  }

  public function print_order($id){

    access::verify_csrf();
    self::check_view_orders();

    $prefix = basket::getOrderPrefix();
    $length = strlen($prefix);
    if (strlen($id)>$length ){
      if ($prefix === strtolower(substr($id,0,$length ))){
        $id  = substr($id,$length);
      }
    }
    $order = ORM::factory("order", $id);
    $view = new View("print_order.html");

    if ($order->loaded()){
      $view->order = str_replace(array("\r\n", "\n", "\r"),"<br/>",$order->text);
    }else{
      $view->order = "Order ".$id." not found.";
    }
    print $view;
  }

  public function show_order($id){

    access::verify_csrf();
    self::check_view_orders();

    $prefix = basket::getOrderPrefix();
    $length = strlen($prefix);
    if (strlen($id)>$length ){
      if ($prefix === strtolower(substr($id,0,$length ))){
        $id  = substr($id,$length);
      }
    }

    $order = ORM::factory("order", $id);

    if ($order->loaded()){
      $view = new View("view_order.html");
      $view->order = $order;
      print $view;
    }else{
      print "Order ".$id." not found.";
    }
  }

  public function show_order_logs($id){
  //get all order logs of order $id and show them in a simple view
    self::check_view_orders();

    $order_log = ORM::factory("order_log")->where('id',"=",$id)->find_all();

    $view = new View("view_order_logs.html");

    $view->order_logs = $order_log;
    print $view;

    print $template;
  }

  public function show_ipn($id){
    access::verify_csrf();
    self::check_view_orders();
      $ipn_message = ORM::factory("ipn_message", $id);

      if ($ipn_message->loaded()){
        print $ipn_message->text;
      }else{
        print "IPN Message ".$id." not found.";
      }
  }
  
  public function send_order_copy($id){
    access::verify_csrf();
    self::check_view_orders();
    $order = ORM::factory("order", $id);

    if ($order->loaded()){
  //Send order copy 
      basket::send_invoice_copy($order);
      order_log::log($order,order_log::COPY_SENT);
    }
    url::redirect("basket/view_orders");
  }

  public function confirm_order_payment($id){
    access::verify_csrf();
    self::check_view_orders();
    $order = ORM::factory("order", $id);

    if ($order->loaded()){
      if ($order->status == 1)
      {
        $order->status = 2;
	order_log::log($order,order_log::PAID);
      }
      elseif ($order->status == 10)
      {
        $order->status = 20;
	order_log::log($order,order_log::PAID);
      }
      $order->save();
  //Send payment confirmation 
      basket::send_payment_confirmation($order);
    }
    url::redirect("basket/view_orders");
  }

  public function remind_order_payment($id){
    access::verify_csrf();
    self::check_view_orders();
    $order = ORM::factory("order", $id);

    if ($order->loaded()){
      if ($order->status == 1)
      {
  //Send payment reminder 
        order_log::log($order,order_log::LATE_PAYMENT);
        basket::send_payment_reminder($order);
      }
    }
    url::redirect("basket/view_orders");
  }

  public function confirm_order_delivery($id){
    access::verify_csrf();
    self::check_view_orders();
      $order = ORM::factory("order", $id);

      if ($order->loaded()){
        if ($order->status == 2)
        {
          $order->status = 20;
          order_log::log($order,order_log::DELIVERED);
        }
        if ($order->status == 1)
        {
          $order->status = 10;
          order_log::log($order,order_log::DELIVERED_NOTPAID);
        }
			// Send delivery confirmation 
				basket::send_delivery_confirmation($order);
				$order->save();
      }
      url::redirect("basket/view_orders");
  }

  public function confirm_order_delivery_not_paid($id){
    access::verify_csrf();
    self::check_view_orders();
      $order = ORM::factory("order", $id);

      if ($order->loaded()){
      }
      url::redirect("basket/view_orders");
  }

  public function confirm_order_cancelled($id){
    access::verify_csrf();
    self::check_view_orders();
      $order = ORM::factory("order", $id);

      if ($order->loaded()){
          $order->status = 99;
          order_log::log($order,order_log::CANCELLED);
			// Send cancellation confirmation 
          basket::send_cancellation_confirmation($order);
          $order->save();
      }
      url::redirect("basket/view_orders");
  }

  private function getCheckoutForm(){
  
    $basket = Session_Basket::get();
    $ppon = $basket->ispp();
    $postage = $basket->postage_cost();

    //labels for fields
    $input1_lbl=t("Title");
    $input2_lbl=t("Initials/First name")."*";
    $input3_lbl=t("Insertion");
    $input4_lbl=t("Name")."*";
    $input5_lbl=t("Street");
    $input6_lbl=t("House Number / Name");
    $input7_lbl=t("Suburb");
    $input8_lbl=t("Postcode");
    $input9_lbl=t("Town or City");
    $input10_lbl=t("E-mail Address")."*";
    $input11_lbl=t("Telephone Number")."*";
    $input12_lbl=t("Child's Name");
    $input13_lbl=t("Child's Group");
    $input14_lbl=t("Additional comments");
    $input15_lbl=t("I agree with the General Terms")."*";
    //labels for mandatory fields with p&p
    if (($ppon) && ($postage > 0)){
      $input5_lbl=$input5_lbl."*";
      $input6_lbl=$input6_lbl."*";
      $input8_lbl=$input8_lbl."*";
      $input9_lbl=$input9_lbl."*";
    }
    //labels for mandatory fields with pickup
    elseif ($postage > 0){
      $input12_lbl=$input12_lbl."*";
      $input13_lbl=$input13_lbl."*";
    }
    $form = new Forge("basket/confirm", "", "post", array("id" => "checkout", "name" =>"checkout"));
    $group = $form->group("contact")->label(t("Contact Details"));
    $group->input("title")->label($input1_lbl)->id("title");
    $group->input("initials")->label($input2_lbl)->id("initials");
    $group->input("insertion")->label($input3_lbl)->id("insertion");
    $group->input("fullname")->label($input4_lbl)->id("fullname");
    $group->input("street")->label($input5_lbl)->id("street");
    $group->input("house")->label($input6_lbl)->id("house");
    $group->hidden("suburb")->label($input7_lbl)->id("suburb");
    $group->input("postcode")->label($input8_lbl)->id("postcode");
    $group->input("town")->label($input9_lbl)->id("town");
    $group->input("email")->label($input10_lbl)->id("email");
    $group->input("phone")->label($input11_lbl)->id("phone");
    //show child fields only with pickup DISABLED
    if ((!$ppon) && ($postage > 1000)){
      $group->input("childname")->label($input12_lbl)->id("childname");
      $group->input("childgroup")->label($input13_lbl)->id("childgroup");
    }
    else{
      $group->hidden("childname")->label($input12_lbl)->id("childname");
      $group->hidden("childgroup")->label($input13_lbl)->id("childgroup");
    }
    $group->input("comments")->label($input14_lbl)->id("comments");
    $group->checkbox("agreeterms")->label($input15_lbl)->id("agreeterms");
    $group->hidden("paypal")->id("paypal");

    return $form;
  }

  public function checkout () {

    $template = new Theme_View("page.html", "basket");

    $view = new View("checkout.html");

    $basket = Session_Basket::get();

    /* changed order for nl_NL */
    $form = self::getCheckoutForm();
    $form->contact->title->value($basket->title);
    $form->contact->initials->value($basket->initials);
    $form->contact->insertion->value($basket->insertion);
    $form->contact->fullname->value($basket->name);
    $form->contact->street->value($basket->street);
    $form->contact->house->value($basket->house);
    $form->contact->postcode->value($basket->postcode);
    $form->contact->town->value($basket->town);
    $form->contact->suburb->value($basket->suburb);
    $form->contact->email->value($basket->email);
    $form->contact->phone->value($basket->phone);
    $form->contact->childname->value($basket->childname);
    $form->contact->childgroup->value($basket->childgroup);
    $form->contact->comments->value($basket->comments);
    $form->contact->agreeterms->value($basket->agreeterms);
    $view->form = $form;

    $template->content = $view;

    print $template;
  }

  public function confirm () {
    access::verify_csrf();

    $form = $this->getCheckoutForm();

    $valid = $form->validate();

    if ($valid){
      $basket = Session_Basket::get();

      if (!isset($basket->contents ) || count($basket->contents) == 0) {
        self::view_basket();
        return;
      }

      /* changed order for nl_NL */
      $basket->title = $form->contact->title->value;
      $basket->initials = $form->contact->initials->value;
      $basket->insertion = $form->contact->insertion->value;
      $basket->name = $form->contact->fullname->value;
      $basket->street = $form->contact->street->value;
      $basket->house = $form->contact->house->value;
      $basket->postcode = $form->contact->postcode->value;
      $basket->town = $form->contact->town->value;
      $basket->suburb = $form->contact->suburb->value;
      $basket->email = $form->contact->email->value;
      $basket->phone = $form->contact->phone->value;
      $basket->childname = $form->contact->childname->value;
      $basket->childgroup = $form->contact->childgroup->value;
      $basket->comments = $form->contact->comments->value;
      $basket->agreeterms=$form->contact->agreeterms->value;

      $paypal=$form->contact->paypal->value=="true";
      $template = new Theme_View("page.html", "basket");

// NOT USED ===============================
      if ($paypal){
        // create a prelimary order
        $order = basket::createOrder($basket, Order_Model::PAYMENT_PAYPAL);
        $paypal = new Paypal();

        // create the order first
        $view = new View("paypal_redirect.html");
        $view ->form = $paypal->process($basket,
          url::site("basket/paypal_complete/$order->id", "http"),
          url::site("basket/paypal_cancel/$order->id", "http"),
          url::site("basket/paypal_ipn/$order->id", "http"));
        $template->content = $view;
        print $template;

        // redirect to paypal
// NOT USED END ===============================
      }
      else{
        $form = new Forge("basket/complete", "", "post", array("id" => "confirm", "name" =>"confirm"));
        $view = new View("confirm_order.html");
        $view->basket = $basket;
        $template->content = $view;
        $view->form = $form;
        print $template;
      }
    }
    else{
      die("Invalid confirmation!");
    }
  }

  function paypal_ipn($id){
      $order = ORM::factory("order")->where("id","=",$id)->find();
      if ($order->loaded()){

        $paypal = new Paypal();

        if ($paypal->validate_ipn($id)){
          if ($paypal->ipn_data['payment_status'] == "Completed"){

            $order->status = Order_Model::PAYMENT_CONFIRMED;

            order_log::log($order,order_log::ORDERED);
            // send e-mails
            basket::send_order($order);
            basket::send_invoice($order);

            $order->save();
          }
          return;
        }
        print "invalid access. tut tut!";
      }
      return;
  }

  public function paypal_complete($id) {
    $order = ORM::factory("order")->where("id","=",$id)->find();
    $basket = Session_Basket::get();
    $basket->clear();
    $this->_complete($order);
  }

  public function paypal_cancel($id){
    $order = ORM::factory("order")->where("id","=",$id)->find();

    if ($order->loaded()){
      $order->delete();
    }

    $this->checkout();
  }

  public function complete () {
    access::verify_csrf();

    $basket = Session_Basket::get();

    if (!isset($basket->contents ) || count($basket->contents) == 0) {
      self::view_basket();
      return;
    }

    // create order
    $order = basket::createOrder($basket, Order_Model::PAYMENT_OFFLINE);
    $basket->clear();

    order_log::log($order,order_log::ORDERED);
    // send e-mails
    basket::send_order($order);
    basket::send_invoice($order);

    $this->_complete($order);
  }

  private function _complete($order){
    $template = new Theme_View("page.html", "basket");
    $view = new View("order_complete.html");
    $ordernumber = basket::getOrderPrefix().$order->id;
    $view->ordernumber = $ordernumber;
    $view->order = $order;
    $view->webshop = basket::getWebshop();
    $view->email = basket::getEmailAddress();
    
    $template->content = $view;

    print $template;
  }

  private function getAddToBasketForm($id){

    $form = new Forge("basket/add_to_basket", "", "post", array("id" => "gAddToBasketForm"));
    $group = $form->group("add_to_basket")->label(t("Add To Basket"));
    $group->hidden("id");
    $group->dropdown("product")
        ->label(t("Product"))
        ->options(product::getProductArray($id));
    $group->input("quantity")->label(t("Quantity"))->id("gQuantity");
    $group->submit("")->value(t("Add"));
    //$group->submit("proceedToCheckout")->value(t("Proceed To Checkout"));

    return $form;
  }

  public function add_to_basket(){

    access::verify_csrf();
    if (!isset($_POST['id']))
    {
      die("no id");
    }
    $form = self::getAddToBasketForm($_POST['id']);
    $valid = $form->validate();

    if ($valid){
    $basket = Session_Basket::getOrCreate();
    $basket->add(
      $form->add_to_basket->id->value,
      $form->add_to_basket->product->value,
      $form->add_to_basket->quantity->value);

      $item = ORM::factory("item", $form->add_to_basket->id->value);

      Session::instance()->set("redirect_home", $item->parent_id);

    print json::reply(array("result" => "success"));
    }
    else
    {
      log_error("invalid form!");
    }
  }

  public function add_to_basket_ajax($id) {

    $view = new View("add_to_basket_ajax.html");

    // get the item to add
    $item = ORM::factory("item", $id);
    if (!$item->loaded())
    {
      //TODO
      die("Not loaded id");
    }

    // get the basket to add to
    $form = self::getAddToBasketForm($id);
    $form->add_to_basket->id->value($id);
    $form->add_to_basket->quantity->value(1);

    $view->form = $form;
    $view->item = $item;

    print $view;
  }

  public function remove_item($key) {

    $basket = Session_Basket::getOrCreate();
    $basket->remove($key);
    url::redirect("basket/view_basket");
  }

}
