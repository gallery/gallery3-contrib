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
/** Controller functions
 * 2011-05-01 Added function view_all_orders()
 */
class Basket_Plus_Controller extends Controller {

//============================================================
// Basket functions, viewing, adding and removing items
//============================================================

  public function add_to_basket(){
    access::verify_csrf();
    if (!isset($_POST['id'])){
      die("no id");
    }
    $form = self::getAddToBasketForm($_POST['id']);
    $valid = $form->validate();
    if ($valid){
      $basket = Session_Basket::getOrCreate();
      $basket->add($form->add_to_basket->id->value,
			$form->add_to_basket->product->value,
			$form->add_to_basket->quantity->value);
      $item = ORM::factory("item", $form->add_to_basket->id->value);
      Session::instance()->set("redirect_home", $item->parent_id);
      print json::reply(array("result" => "success"));
    }
    else {
      log_error("invalid form!");
    }
  }

  public function add_to_basket_ajax($id) {
    $view = new View("add_to_basket_ajax.html");
    // get the item to add
    $item = ORM::factory("item", $id);
    if (!$item->loaded()){
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

  private function getAddToBasketForm($id){
    $form = new Forge("basket_plus/add_to_basket", "", "post", array("id" => "gAddToBasketForm"));
    $group = $form->group("add_to_basket")->label(t("Add To Basket"));
    $group->hidden("id");
    $group->dropdown("product")
        ->label(t("Product"))
        ->options(bp_product::getProductArray($id));
    $group->input("quantity")->label(t("Quantity"))->id("gQuantity");
    $group->submit("")->value(t("Add"));

    return $form;
  }

  public function remove_item($key) {
    $basket = Session_Basket::getOrCreate();
    $basket->remove($key);
    url::redirect("basket_plus/view_basket");
  }

  public function view_basket($pickup = "") {
    $template = new Theme_View("page.html", "basket_plus", "basket");
    $basket = Session_Basket::get();
    if (isset($pickup)){
			if ($pickup == "pickup"){
				$basket->enable_pickup();
			}
			elseif ($pickup == "nopickup"){
				$basket->disable_pickup();
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

//============================================================
// Basket workflow functions, called from basket and order wizard
//============================================================

/*
 * USED IN: basket view
 * DESCRIPTION: starts the first step of the order process
 */
// Called when user clicks button 'Proceed to checkout' in the basket
  public function checkout() {
    $template = new Theme_View("page.html", "basket_plus", "checkout");
    $view = new View("checkout.html");
    $basket = Session_Basket::get();
		
    $form = self::getCheckoutForm($basket);
		//fill the form with values previously entered
    $form->contact->title->value($basket->title);
    $form->contact->initials->value($basket->initials);
    $form->contact->insertion->value($basket->insertion);
    $form->contact->fname->value($basket->fname);
    $form->contact->street->value($basket->street);
    $form->contact->house->value($basket->house);
    $form->contact->postalcode->value($basket->postalcode);
    $form->contact->suburb->value($basket->suburb);
    $form->contact->town->value($basket->town);
    $form->contact->province->value($basket->province);
    $form->contact->country->value($basket->country);
    $form->contact->email->value($basket->email);
    $form->contact->phone->value($basket->phone);
    $form->contact->order_ref1->value($basket->order_ref1);
    $form->contact->order_ref2->value($basket->order_ref2);
    $form->contact->comments->value($basket->comments);
    $form->contact->agreeterms->checked($basket->agreeterms);
		$form->contact->paypal->checked($basket->paypal);
    $view->form = $form;
		$view->page_type = "basket";
    $template->content = $view;
		
    print $template;
  }

	// Called by function checkout
  private function getCheckoutForm($basket) {
//  	$basket = Session_Basket::get();
    //labels for fields
		$input1_lbl = t("Title");
		$input2_lbl = t("Initials/First name");
		$input3_lbl = t("Insertion");
		$input4_lbl = t("Name");
		$input5_lbl = t("Street");
		$input6_lbl = t("House Number / Name");
		$input7_lbl = t("Suburb");
		$input8_lbl = t("Postal code");
		$input9_lbl = t("Town or City");
		$input10_lbl = t("E-mail Address");
		$input11_lbl = t("Telephone Number");
		$input12_lbl = t("Order reference");
		$input13_lbl = t("Order reference 2");
		$input14_lbl = t("Additional comments");
		$input15_lbl = t("I agree with the General Terms");
		$input16_lbl = t("Use PayPal for payment");
		$input17_lbl = t("State or Province");
		$input18_lbl = t("Country");

		//get user-defined labels for order reference with pickup
		$is_group = basket_plus::getUserIsGroup();
		if ($is_group){
			$user = identity::active_user();
			//get user_basket setting
			$user_basket = ORM::factory("bp_user_basket")->where("id", "=", $user->id)->find();
			$extra_order_info_lbl = $user_basket->extra_order_info_lbl;
			if ($extra_order_info_lbl <> ""){
				$input12_lbl = $extra_order_info_lbl;
			}
			$extra_order_info_lbl2 = $user_basket->extra_order_info_lbl2;
			if ($extra_order_info_lbl2 <> ""){
				$input13_lbl = $extra_order_info_lbl2;
			}
		}
		
	//NOTE: mandatory field checks in JavaScript in the view 'checkout_html.php'
    //labels for mandatory fields
		$input2_lbl .= "*"; 	//initials
		$input4_lbl .= "*";		//name
		$input10_lbl .= "*";	//email
		$input11_lbl .= "*";	//phone
		$input15_lbl .= "*";	//general terms
		
    //labels for mandatory fields with p&p (mail)
		$pickup = $basket->pickup;
		$postage = $basket->postage_cost();
    if ((!$pickup) && ($postage > 0)){
			$input5_lbl .= "*"; //street
			$input6_lbl .= "*";	//house
			$input8_lbl .= "*";	//postalcode
			$input9_lbl .= "*";	//city
    }
    //labels for mandatory fields with pickup
		elseif (($pickup) && ($postage > 0)){
			$input12_lbl .= "*";
			$input13_lbl .= "*";
		}
		//create the form and add fields
		//NOTE: the order below determines the order on the form
    $form = new Forge("basket_plus/confirm", "", "post", array("id" => "checkout", "name" =>"checkout"));
    $group = $form->group("contact")->label(t("Contact Details"));
    $group->input("title")->label($input1_lbl)->id("title");
    $group->input("initials")->label($input2_lbl)->id("initials");
    $group->input("insertion")->label($input3_lbl)->id("insertion");
    $group->input("fname")->label($input4_lbl)->id("fname");
    $group->input("street")->label($input5_lbl)->id("street");
    $group->input("house")->label($input6_lbl)->id("house");
		//show field only when configured
		if (basket_plus::getBasketVar(USE_ADDRESS_SUBURB)){
			$group->input("suburb")->label($input7_lbl)->id("suburb");
		} 
		else{
			$group->hidden("suburb")->id("suburb");
		}
    $group->input("postalcode")->label($input8_lbl)->id("postalcode");
    $group->input("town")->label($input9_lbl)->id("town");
		//show field only when configured
		if (basket_plus::getBasketVar(USE_ADDRESS_PROVINCE)){
			$group->input("province")->label($input17_lbl)->id("province");
		} 
		else{
			$group->hidden("province")->id("province");
		}
		//show field only when configured
		if (basket_plus::getBasketVar(USE_ADDRESS_COUNTRY)){
			$group->input("country")->label($input18_lbl)->id("country");
		} 
		else{
			$group->hidden("country")->id("country");
		}    
    $group->input("email")->label($input10_lbl)->id("email");
    $group->input("phone")->label($input11_lbl)->id("phone");

    //show reference fields only with pickup 
    if (($pickup) && ($postage > 0)){
			$group->input("order_ref1")->label($input12_lbl)->id("order_ref1");
			$group->input("order_ref2")->label($input13_lbl)->id("order_ref2");
    }
		else{ 
			$group->hidden("order_ref1")->label($input12_lbl)->id("order_ref1");
			$group->hidden("order_ref2")->label($input13_lbl)->id("order_ref2");
    }
		$group->input("comments")->label($input14_lbl)->id("comments");
    $group->checkbox("agreeterms")->label($input15_lbl)->id("agreeterms");

		//show field only when configured
		if (basket_plus::getBasketVar(USE_PAYPAL)){
			$group->checkbox("paypal")->label($input16_lbl)->id("paypal");
		} 
		else{
			$group->hidden("paypal")->id("paypal");
		}
    return $form;
  }

/*
 * USED IN: view 'checkout', button 'Confirm order' in order wizard (step 1 of 3)
 * DESCRIPTION: confirms the order as second step of the order process
 */
  public function confirm () {
    access::verify_csrf();
		$basket = Session_Basket::get();
    $form = $this->getCheckoutForm($basket);
    $valid = $form->validate();

    if ($valid){      
      if (!isset($basket->contents ) || count($basket->contents) == 0) {
        self::view_basket();
        return;
      }
      // save form values
      $basket->title = $form->contact->title->value;
      $basket->initials = $form->contact->initials->value;
      $basket->insertion = $form->contact->insertion->value;
      $basket->fname = $form->contact->fname->value;
      $basket->street = $form->contact->street->value;
      $basket->house = $form->contact->house->value;
      $basket->postalcode = $form->contact->postalcode->value;
      $basket->suburb = $form->contact->suburb->value;
      $basket->town = $form->contact->town->value;
      $basket->province = $form->contact->province->value;
      $basket->country = $form->contact->country->value;
      $basket->email = $form->contact->email->value;
      $basket->phone = $form->contact->phone->value;
			$basket->order_ref1 = $form->contact->order_ref1->value;
			$basket->order_ref2 = $form->contact->order_ref2->value;
      $basket->comments = $form->contact->comments->value;
      $basket->agreeterms = $form->contact->agreeterms->value;
			$basket->paypal = $form->contact->paypal->value;

      $template = new Theme_View("page.html", "basket", "confirm");
			$form = new Forge("basket_plus/complete", "", "post", array("id" => "confirm", "name" =>"confirm"));
			$view = new View("confirm_order.html");
			$view->basket = $basket;
			$view->page_type = "basket";
			$template->content = $view;
			$view->form = $form;
			print $template;
    }
    else {
      die("Invalid confirmation!");
    }
  }

/*
 * USED IN: button 'Complete' in order wizard (step 2)
 * DESCRIPTION: completes the order as final step of the order process
 */
 public function complete () {
    access::verify_csrf();
    $basket = Session_Basket::get();
    if (!isset($basket->contents ) || count($basket->contents) == 0) {
      self::view_basket();
      return;
    }
		$paypal = $basket->paypal;
		if ($paypal){ //paypal payment
      $template = new Theme_View("page.html", "basket", "confirm");
			// create a preliminary order
			$order = basket_plus::createOrder($basket, Bp_Order_Model::PAYMENT_PAYPAL);
			bp_order_log::log($order,Bp_Order_Log_Model::ORDERED);
			// send e-mails to customer and internal order handling
			basket_plus::send_order($order);
			// paypal stuff
			$paypal_payment = new Paypal();
			$view = new View("paypal_redirect.html");
			// here the functions to call after paypal processing are defined; 
			// function 'process' redirects to the PayPal site (see library Paypal)
			$view ->form = $paypal_payment->process($basket,
				url::site("basket_plus/paypal_complete/$order->id", "http"),
				url::site("basket_plus/paypal_cancel/$order->id", "http"),
				url::site("basket_plus/paypal_ipn/$order->id", "http"));
			$template->content = $view;
			print $template;
    }
		else{ //offline payment
				// create order
			$order = basket_plus::createOrder($basket, Bp_Order_Model::PAYMENT_OFFLINE);
			bp_order_log::log($order,Bp_Order_Log_Model::ORDERED);
			$basket->clear();
			// send e-mails to customer and internal order handling
			basket_plus::send_order($order);
			// show page Order completed
			$this->_complete($order);
		}
  }

/*
 * USED IN: function complete()
 * DESCRIPTION: shows the Order completed page
 */
  private function _complete($order){
    $template = new Theme_View("page.html", "basket", "complete");
    $view = new View("order_complete.html");
    $ordernumber = basket_plus::getBasketVar(ORDER_PREFIX).$order->id;
    $view->ordernumber = $ordernumber;
    $view->order = $order;
    $view->webshop = basket_plus::getBasketVar(WEBSHOP);
    $view->email = basket_plus::getBasketVar(EMAIL_ORDER);
    $template->content = $view;
    print $template;
  }

/*
 * USED IN: order workflow admin functions: view 'view_order', 'paypal_cancel'
 * DESCRIPTION: Changes order status to CANCELLED
 */
  public function cancel_order($id) {
		$order = ORM::factory("bp_order", $id);
		if ($order->loaded()){
			$order->status = Bp_Order_Model::CANCELLED;
			$order->save();
			bp_order_log::log($order,Bp_Order_Log_Model::CANCELLED);
		}
  }
	
//============================================================
// Order view functions
//============================================================

/*
 * USED IN: Views with 'Show orders' button and in the Basket sub-menu
 * DESCRIPTION: Shows a list of incomplete orders (not paid, not delivered)
 */
  public function view_orders() {
  //get all incomplete orders and show them in a view
    self::check_view_orders();
    $template = new Theme_View("page.html", "basket", "orders");
    $incomplete_orders = ORM::factory("bp_order")->where('status',"<",Bp_Order_Model::DELIVERED)->find_all();
    $view = new View("view_orders.html");
    $view->orders = $incomplete_orders;
		$view->page_type = "orders";
    $template->content = $view;
    print $template;
  }
  
/*
 * USED IN: Basket sub-menu
 * DESCRIPTION: Shows a list of all orders
 */
  public function view_all_orders() {
  //get all orders and show them in a view
    self::check_view_orders();
    $template = new Theme_View("page.html", "basket", "all_orders");
    $all_orders = ORM::factory("bp_order")->find_all();
    $view = new View("view_orders.html");
    $view->orders = $all_orders;
		$view->page_type = "orders";
    $template->content = $view;
    print $template;
  }

/*
 * USED IN: various order workflow admin functions
 * DESCRIPTION: Checks if the user is allowed to view orders
 */
  public function check_view_orders() {
    if (!basket_plus::can_view_orders()){
      die("Invalid access.");
    }
  }

	// UNUSED FUNCTION; retained from original basket module
  public function print_order($id) {
    access::verify_csrf();
    self::check_view_orders();
    $prefix = basket_plus::getBasketVar(ORDER_PREFIX);
    $length = strlen($prefix);
    if (strlen($id)>$length ){
      if ($prefix === strtolower(substr($id,0,$length ))){
        $id  = substr($id,$length);
      }
    }
    $order = ORM::factory("bp_order", $id);
    $view = new View("print_order.html");

    if ($order->loaded()){
      $view->order = str_replace(array("\r\n", "\n", "\r"),"<br/>",$order->text);
    }
		else {
      $view->order = "Order ".$id." not found.";
    }
    print $view;
  }

/*
 * USED IN: order workflow admin functions: view 'view_order'
 * DESCRIPTION: Shows internal order details
 */
  public function show_order($id) {

    access::verify_csrf();
    self::check_view_orders();

    $prefix = basket_plus::getBasketVar(ORDER_PREFIX);
    $length = strlen($prefix);
    if (strlen($id)>$length ){
			if ($prefix === strtolower(substr($id,0,$length ))){
				$id  = substr($id,$length);
			}
    }
    $order = ORM::factory("bp_order", $id);

    if ($order->loaded()){
			$view = new View("view_order.html");
			$view->order = $order;
			print $view;
    }
		else{
			print "Order ".$id." not found.";
    }
  }

/*
 * USED IN: order workflow admin functions: view 'view_order'
 * DESCRIPTION: Shows order log entries of an order
 */
  public function show_order_logs($id) {
  //get all order logs of order $id and show them in a simple view
    self::check_view_orders();
    $order_log = ORM::factory("bp_order_log")->where('id',"=",$id)->find_all();
    $view = new View("view_order_logs.html");
    $view->order_logs = $order_log;
    print $view;
  }

//============================================================
// Order workflow admin functions, called from view view_order
//============================================================

/*
 * USED IN: order workflow admin functions: view 'view_order'
 * DESCRIPTION: Sends a copy of the order confirmation e-mail  
 */
  public function send_order_copy($id) {
    access::verify_csrf();
    self::check_view_orders();
    $order = ORM::factory("bp_order", $id);

    if ($order->loaded()){
		//Send order copy 
			basket_plus::send_order_copy($order);
			bp_order_log::log($order,Bp_Order_Log_Model::COPY_SENT);
    }
    url::redirect("basket_plus/view_orders");
  }

/*
 * USED IN: order workflow admin functions: view 'view_order'
 * DESCRIPTION: Sends a payment confirmation e-mail and changes order status
 */
  public function confirm_order_payment($id) {
    access::verify_csrf();
    self::check_view_orders();
    $order = ORM::factory("bp_order", $id);
    if ($order->loaded()){
      if ($order->status == Bp_Order_Model::WAITING_PAYMENT){
        $order->status = Bp_Order_Model::PAYMENT_CONFIRMED;
				bp_order_log::log($order,Bp_Order_Log_Model::PAID);
      }
      elseif ($order->status == Bp_Order_Model::DELIVERED_NOTPAID) {
        $order->status = Bp_Order_Model::DELIVERED;
				bp_order_log::log($order,Bp_Order_Log_Model::PAID);
      }
      $order->save();
		//Send payment confirmation 
			basket_plus::send_payment_confirmation($order);
    }
    url::redirect("basket_plus/view_orders");
  }

/*
 * USED IN: order workflow admin functions: view 'view_order'
 * DESCRIPTION: Sends a payment reminder e-mail 
 */
  public function remind_order_payment($id) {
    access::verify_csrf();
    self::check_view_orders();
    $order = ORM::factory("bp_order", $id);
    if ($order->loaded()){
			if ($order->status == Bp_Order_Model::WAITING_PAYMENT){
				//Send payment reminder 
				bp_order_log::log($order,Bp_Order_Log_Model::LATE_PAYMENT);
				basket_plus::send_payment_reminder($order);
			}
    }
    url::redirect("basket_plus/view_orders");
  }

/*
 * USED IN: order workflow admin functions: view 'view_order'
 * DESCRIPTION: Sends a delivery confirmation e-mail and changes order status
 * 							Supports paid and non-paid order
 */
  public function confirm_order_delivery($id) {
    access::verify_csrf();
    self::check_view_orders();
      $order = ORM::factory("bp_order", $id);
      if ($order->loaded()){
        if ($order->status == Bp_Order_Model::PAYMENT_CONFIRMED){
          $order->status = Bp_Order_Model::DELIVERED;
          bp_order_log::log($order,Bp_Order_Log_Model::DELIVERED);
        }
        elseif ($order->status == Bp_Order_Model::WAITING_PAYMENT){
          $order->status = Bp_Order_Model::DELIVERED_NOTPAID;
          bp_order_log::log($order,Bp_Order_Log_Model::DELIVERED_NOTPAID);
        }
				$order->save();
				// Send delivery confirmation 
				basket_plus::send_delivery_confirmation($order);
      }
      url::redirect("basket_plus/view_orders");
  }

/*
 * USED IN: order workflow admin functions: view 'view_order'
 * DESCRIPTION: Sends a delay notification e-mail 
 */
  public function notify_order_delayed($id) {
    access::verify_csrf();
    self::check_view_orders();
    $order = ORM::factory("bp_order", $id);
    if ($order->loaded()){
			if ($order->status == Bp_Order_Model::PAYMENT_CONFIRMED){
				//Send delay notification 
				bp_order_log::log($order,Bp_Order_Log_Model::DELAYED);
				basket_plus::send_delay_notification($order);
			}
    }
    url::redirect("basket_plus/view_orders");
  }

/*
 * USED IN: order workflow admin functions: view 'view_order'
 * DESCRIPTION: Sends a cancellation e-mail and changes order status
 */
  public function confirm_order_cancelled($id) {
    access::verify_csrf();
    self::check_view_orders();
		self::cancel_order($id);
		// Send cancellation confirmation 
		basket_plus::send_cancellation_confirmation($order);
		
		url::redirect("basket_plus/view_orders");
  }

//============================================================
// Paypal functions functions
//============================================================

	// UNUSED FUNCTION; retained from original basket module
  function paypal_ipn($id){
      $order = ORM::factory("bp_order")->where("id","=",$id)->find();
      if ($order->loaded()){
        $paypal = new Paypal();
        if ($paypal->validate_ipn($id)){
          if ($paypal->ipn_data['payment_status'] == "Completed"){
            $order->status = Bp_Order_Model::PAYMENT_CONFIRMED;
            bp_order_log::log($order,Bp_Order_Log_Model::ORDERED);
            // send e-mails to customer and internal order handling
            basket_plus::send_order($order);
            $order->save();
          }
          return;
        }
        print "invalid access. tut tut!";
      }
      return;
  }

	// called when the paypal payment is successful
  public function paypal_complete($id) {
    $order = ORM::factory("bp_order")->where("id","=",$id)->find();
		$order->status = Bp_Order_Model::PAYMENT_CONFIRMED;
		$order->save();
		bp_order_log::log($order,Bp_Order_Log_Model::PAID);
		// Send payment confirmation 
		basket_plus::send_payment_confirmation($order);
		// house keeping
    $basket = Session_Basket::get();
    $basket->clear();
    $this->_complete($order);
  }

	// called when user cancels the paypal payment
	// @TODO DOES NOT WORK WITH Internet Explorer: user logged out and basket emptied
  public function paypal_cancel($id){
		// cancel the order
		self::cancel_order($id); 
		$basket = Session_Basket::get();
		url::redirect("basket_plus/view_basket");
  }

	// UNUSED FUNCTION; retained from original basket module
  public function view_ipn($orderid){
    self::check_view_orders();
    $template = new Theme_View("page.html", "basket", "ipn");
    $order = ORM::factory("bp_order")->where("id","=",$orderid)->find();
    $ipn_messages = ORM::factory("bp_ipn_message")->where("key","=",$orderid)->find_all();
    //$ipn_messages = ORM::factory("bp_ipn_message")->find_all();
    $view = new View("view_ipn.html");
    $view->order = $order;
    $view->ipn_messages = $ipn_messages;
    $template->content = $view;
    print $template;
  }

	// UNUSED FUNCTION; retained from original basket module
  public function show_ipn($id){
    access::verify_csrf();
    self::check_view_orders();
    $ipn_message = ORM::factory("bp_ipn_message", $id);
    if ($ipn_message->loaded()){
        print $ipn_message->text;
    }
		else{
        print "IPN Message ".$id." not found.";
    }
  }
  
}
