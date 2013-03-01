<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
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
class Basket_Controller extends Controller {

  public function temp(){
    $db = Database::instance();
    $db->query("ALTER TABLE {orders} ADD COLUMN `method` int(9) DEFAULT 0;");
  }
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
    self::check_view_orders();
    $template = new Theme_View("page.html", "basket");

    $incomplete_orders = ORM::factory("order")->where('status',"<",20)->find_all();

    $view = new View("view_orders.html");

    $view->orders = $incomplete_orders;

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

  public function confirm_order_delivery($id){
    access::verify_csrf();
    self::check_view_orders();
      $order = ORM::factory("order", $id);

      if ($order->loaded()){
        if ($order->status == 2)
        {
          $order->status = 20;
          $order->save();
        }
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
          $order->save();
        }
      }
      url::redirect("basket/view_orders");
  }

  private function getCheckoutForm(){
    $form = new Forge("basket/confirm", "", "post", array("id" => "checkout", "name" =>"checkout"));
    $group = $form->group("contact")->label(t("Contact Details"));
    $group->input("fullname")->label(t("Name"))->id("fullname");
    $group->input("house")->label(t("House Number / Name"))->id("house");
    $group->input("street")->label(t("Street"))->id("street");
    $group->input("suburb")->label(t("Suburb"))->id("suburb");
    $group->input("town")->label(t("Town or City"))->id("town");
    $group->input("postcode")->label(t("Postcode"))->id("postcode");
    $group->input("email")->label(t("E-Mail Address"))->id("email");
    $group->input("phone")->label(t("Telephone Number"))->id("phone");
    $group->hidden("paypal")->id("paypal");

    return $form;
  }

  public function checkout () {

    $template = new Theme_View("page.html", "basket");

    $view = new View("checkout.html");

    $basket = Session_Basket::get();

    $form = self::getCheckoutForm();
    $form->contact->fullname->value($basket->name);
    $form->contact->house->value($basket->house);
    $form->contact->street->value($basket->street);
    $form->contact->suburb->value($basket->suburb);
    $form->contact->town->value($basket->town);
    $form->contact->postcode->value($basket->postcode);
    $form->contact->email->value($basket->email);
    $form->contact->phone->value($basket->phone);
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

      $basket->name = $form->contact->fullname->value;
      $basket->house = $form->contact->house->value;
      $basket->street = $form->contact->street->value;
      $basket->suburb = $form->contact->suburb->value;
      $basket->town = $form->contact->town->value;
      $basket->postcode = $form->contact->postcode->value;
      $basket->email = $form->contact->email->value;
      $basket->phone = $form->contact->phone->value;

      $paypal=$form->contact->paypal->value=="true";
      $template = new Theme_View("page.html", "basket");

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
      }else
      {
        $form = new Forge("basket/complete", "", "post", array("id" => "confirm", "name" =>"confirm"));
        $view = new View("confirm_order.html");
        $view->basket = $basket;
        $template->content = $view;
        $view->form = $form;
        print $template;
      }
    }
    else
    {
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
    $view->total_cost = $order->cost;

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
