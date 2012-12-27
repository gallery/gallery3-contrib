<?php
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
 
 /* Extended with 
 2011-01-31 Dutch e-mail text
 2011-02-28 function formatMoneyForMail
 2011-04-07 order_bankaccount, 
 2011-04-25 e-mails for payment and delivery confirmation, 
  added method values to discriminate between mail, email and pickup. 
 2011-11-17 E-mails for delivey w/o payment, payment after delivery and cancellation 
 2012-01-04 Pickup location added
 */
class basket_Core {

  static $currencies = array(
    "AUD" => "Australian Dollars",
    "CAD" => "Canadian Dollars",
    "EUR" => "Euros",
    "GBP" => "Pounds Sterling",
    "JPY" => "Yen",
    "USD" => "U.S. Dollars",
    "NZD" => "New Zealand Dollar",
    "CHF" => "Swiss Franc",
    "HKD" => "Hong Kong Dollar",
    "SGD" => "Singapore Dollar",
    "SEK" => "Swedish Krona",
    "DKK" => "Danish Krone",
    "PLN" => "Polish Zloty",
    "NOK" => "Norwegian Krone",
    "HUF" => "Hungarian Forint",
    "CZK" => "Czech Koruna",
    "ILS" => "Israeli Shekel",
    "MXN" => "Mexican Peso");

  static $format= array(
    "AUD" => "$",
    "CAD" => "$",
    "EUR" => "€",
    "GBP" => "£",
    "JPY" => "¥",
    "USD" => "$",
    "NZD" => "$",
    "CHF" => "",
    "HKD" => "$",
    "SGD" => "$",
    "SEK" => "",
    "DKK" => "",
    "PLN" => "",
    "NOK" => "",
    "HUF" => "",
    "CZK" => "",
    "ILS" => "",
    "MXN" => "",
    "none" => "");

  static $formatweb= array(
    "AUD" => "$",
    "CAD" => "$",
    "EUR" => "&euro;",
    "GBP" => "&pound;",
    "JPY" => "&yen;",
    "USD" => "$",
    "NZD" => "$",
    "CHF" => "",
    "HKD" => "$",
    "SGD" => "$",
    "SEK" => "",
    "DKK" => "",
    "PLN" => "",
    "NOK" => "",
    "HUF" => "",
    "CZK" => "",
    "ILS" => "",
    "MXN" => "");

  static public function can_view_orders()
  {
    if (identity::active_user()->admin){
      return true;
    }

    print identity::active_user();
    foreach (identity::active_user()->groups() as $group){
      if ($group->name == 'shop'){
        return true;
      }
    }

    return false;
  }

  static function get_configure_form() {
    $form = new Forge("admin/configure", "", "post", array("id" => "g-configure-form"));
    $group = $form->group("configure")->label(t("Configure Basket"));
    $group->input("webshop")->label(t("Webshop"))->id("g-webshop");
    $group->input("website")->label(t("Website"))->id("g-order-website");
    $group->input("email")->label(t("Offline Paying Email Address"))->id("g-order-email-address");
    $group->dropdown("currency")
    ->label(t("Currency"))
    ->options(self::$currencies);

    $group->checkbox("side_bar")->label(t("Use only side bar"))->id("g-side-bar-only");

    $group->checkbox("paypal")->label(t("Use Paypal"))->id("g-paypal");
    $group->input("paypal_account")->label(t("Paypal E-Mail Address"))->id("g-paypal-address");
    $group->checkbox("allow_pickup")->label(t("Allow Product Pickup"))->id("g-allow-pickup");
    $group->input("pickup_location")->label(t("Pickup Location"))->id("g-pickup-location");
    $group->input("order_prefix")->label(t("Order Number Prefix"))->id("g-order-prefix");
    $group->input("order_bankaccount")->label(t("Order Bank Account"))->id("g-order_bankaccount");
    $group->input("order_accountowner")->label(t("Order Account Owner"))->id("g-order_accountowner");
    $group->submit("")->value(t("Save"));
    return $form;
  }

  static function get_template_form() {
    $form = new Forge("admin/configure/templates", "", "post", array("id" => "g-configure-form"));
    $group = $form->group("configure")->label(t("Configure Basket"));
    $group->textarea("payment_details")->label(t("Payment Details Description"))->id("g-payment-details");
    $group->textarea("order_complete_page")->label(t("Order Complete Page"))->id("g-order-complete_page");
    $group->input("order_complete_email_subject")->label(t("Order Complete Email Subject"))->id("g-order-complete_email_subject");
    $group->textarea("order_complete_email")->label(t("Order Complete Email"))->id("g-order-complete_email");
    $group->input("order_paid_email_subject")->label(t("Order Paid Email Subject"))->id("g-order-paid_email_subject");
    $group->textarea("order_paid_email")->label(t("Order Paid Email"))->id("g-order-paid_email");
    $group->input("order_paid_delivered_email_subject")->label(t("Order Paid Delivered Email Subject"))->id("g-order-paid-delivered_email_subject");
    $group->textarea("order_paid_delivered_email")->label(t("Order Paid Delivered Email"))->id("g-order-paid-delivered_email");
    $group->input("order_late_payment_email_subject")->label(t("Order Late Payment Email Subject"))->id("g-order-late-payment_email_subject");
    $group->textarea("order_late_payment_email")->label(t("Order Late Payment Email"))->id("g-order-late-payment_email");
    $group->input("order_delivered_email_subject")->label(t("Order Delivered Email Subject"))->id("g-order-delivered_email_subject");
    $group->textarea("order_delivered_email")->label(t("Order Delivered Email"))->id("g-order-delivered_email");
    $group->input("order_delivered_notpaid_email_subject")->label(t("Order Delivered Not Paid Email Subject"))->id("g-order-delivered-notpaid_email_subject");
    $group->textarea("order_delivered_notpaid_email")->label(t("Order Delivered Not Paid Email"))->id("g-order-delivered-notpaid_email");
    $group->input("order_cancelled_email_subject")->label(t("Order Cancelled Email Subject"))->id("g-order-cancelled_email_subject");
    $group->textarea("order_cancelled_email")->label(t("Order Cancelled Email"))->id("g-order-cancelled_email");
    $group->textarea("order_email_closing")->label(t("Order Email Closing"))->id("g-order-email-closing");
    $group->submit("")->value(t("Save"));
    return $form;
  }

  static function populateForm($form){
    $form->configure->webshop->value(basket::getWebshop());
    $form->configure->website->value(basket::getWebsite());
    $form->configure->email->value(basket::getEmailAddress());
    $form->configure->side_bar->checked(basket::is_side_bar_only());
    $form->configure->paypal->checked(basket::isPaypal());
    $form->configure->paypal_account->value(basket::getPaypalAccount());
    $form->configure->currency->selected(basket::getCurrency());
    $form->configure->allow_pickup->checked(basket::isAllowPickup());
    $form->configure->pickup_location->value(basket::getPickupLocation());
    $form->configure->order_prefix->value(basket::getOrderPrefix());
    $form->configure->order_bankaccount->value(basket::getOrderBankAccount());
    $form->configure->order_accountowner->value(basket::getOrderAccountOwner());
  }

  static function populateTemplateForm($form){
    $form->configure->payment_details->value(basket::getPaymentDetails());
    $form->configure->order_complete_page->value(basket::getOrderCompletePage());
    $form->configure->order_complete_email_subject->value(basket::getOrderCompleteEmailSubject());
    $form->configure->order_complete_email->value(basket::getOrderCompleteEmail());
    $form->configure->order_paid_email_subject->value(basket::getOrderPaidEmailSubject());
    $form->configure->order_paid_email->value(basket::getOrderPaidEmail());
    $form->configure->order_paid_delivered_email_subject->value(basket::getOrderPaidDeliveredEmailSubject());
    $form->configure->order_paid_delivered_email->value(basket::getOrderPaidDeliveredEmail());
    $form->configure->order_late_payment_email_subject->value(basket::getOrderLatePaymentEmailSubject());
    $form->configure->order_late_payment_email->value(basket::getOrderLatePaymentEmail());
    $form->configure->order_delivered_email_subject->value(basket::getOrderDeliveredEmailSubject());
    $form->configure->order_delivered_email->value(basket::getOrderDeliveredEmail());
    $form->configure->order_delivered_notpaid_email_subject->value(basket::getOrderDeliveredNotPaidEmailSubject());
    $form->configure->order_delivered_notpaid_email->value(basket::getOrderDeliveredNotPaidEmail());
    $form->configure->order_cancelled_email_subject->value(basket::getOrderCancelledEmailSubject());
    $form->configure->order_cancelled_email->value(basket::getOrderCancelledEmail());
    $form->configure->order_email_closing->value(basket::getOrderEmailClosing());
  }

  static function extractForm($form){
    $webshop = $form->configure->webshop->value;
    $website = $form->configure->website->value;
    $email = $form->configure->email->value;
    $is_side_bar = $form->configure->side_bar->value;
    $isPaypal = $form->configure->paypal->value;
    $paypal_account = $form->configure->paypal_account->value;
    $currency = $form->configure->currency->selected;
    $allow_pickup = $form->configure->allow_pickup->value;
    $pickup_location = $form->configure->pickup_location->value;
    $order_prefix = $form->configure->order_prefix->value;
    $order_bankaccount = $form->configure->order_bankaccount->value;
    $order_accountowner = $form->configure->order_accountowner->value;
    basket::setWebshop($webshop);
    basket::setWebsite($website);
    basket::setEmailAddress($email);
    basket::set_side_bar_only($is_side_bar);
    basket::setPaypal($isPaypal);
    basket::setPaypalAccount($paypal_account);
    basket::setCurrency($currency);
    basket::setAllowPickup($allow_pickup);
    basket::setPickupLocation($pickup_location);
    basket::setOrderPrefix($order_prefix);
    basket::setOrderBankAccount($order_bankaccount);
    basket::setOrderAccountOwner($order_accountowner);
  }
  static function extractTemplateForm($form){
    $payment_details = $form->configure->payment_details->value;
    $order_complete_page = $form->configure->order_complete_page->value;
    $order_complete_email_subject = $form->configure->order_complete_email_subject->value;
    $order_complete_email = $form->configure->order_complete_email->value;
    $order_paid_email_subject = $form->configure->order_paid_email_subject->value;
    $order_paid_email = $form->configure->order_paid_email->value;
    $order_paid_delivered_email_subject = $form->configure->order_paid_delivered_email_subject->value;
    $order_paid_delivered_email = $form->configure->order_paid_delivered_email->value;
    $order_late_payment_email_subject = $form->configure->order_late_payment_email_subject->value;
    $order_late_payment_email = $form->configure->order_late_payment_email->value;
    $order_delivered_email_subject = $form->configure->order_delivered_email_subject->value;
    $order_delivered_email = $form->configure->order_delivered_email->value;
    $order_delivered_notpaid_email_subject = $form->configure->order_delivered_notpaid_email_subject->value;
    $order_delivered_notpaid_email = $form->configure->order_delivered_notpaid_email->value;
    $order_cancelled_email_subject = $form->configure->order_cancelled_email_subject->value;
    $order_cancelled_email = $form->configure->order_cancelled_email->value;
    $order_email_closing = $form->configure->order_email_closing->value;
    basket::setPaymentDetails($payment_details);
    basket::setOrderCompletePage($order_complete_page);
    basket::setOrderCompleteEmailSubject($order_complete_email_subject);
    basket::setOrderCompleteEmail($order_complete_email);
    basket::setOrderPaidEmailSubject($order_paid_email_subject);
    basket::setOrderPaidEmail($order_paid_email);
    basket::setOrderPaidDeliveredEmailSubject($order_paid_delivered_email_subject);
    basket::setOrderPaidDeliveredEmail($order_paid_delivered_email);
    basket::setOrderLatePaymentEmailSubject($order_late_payment_email_subject);
    basket::setOrderLatePaymentEmail($order_late_payment_email);
    basket::setOrderDeliveredEmailSubject($order_delivered_email_subject);
    basket::setOrderDeliveredEmail($order_delivered_email);
    basket::setOrderDeliveredNotPaidEmailSubject($order_delivered_notpaid_email_subject);
    basket::setOrderDeliveredNotPaidEmail($order_delivered_notpaid_email);
    basket::setOrderCancelledEmailSubject($order_cancelled_email_subject);
    basket::setOrderCancelledEmail($order_cancelled_email);
    basket::setOrderEmailClosing($order_email_closing);
  }

  static public function is_side_bar_only(){
    return module::get_var("basket","is_side_bar_only");
  }

  static public function set_side_bar_only($value){
    module::set_var("basket","is_side_bar_only",$value);
  }

  static function getWebshop(){
    return module::get_var("basket","webshop");
  }

  static function getWebsite(){
    return module::get_var("basket","website");
  }

  static function getEmailAddress(){
    return module::get_var("basket","email");
  }

  static function isPaypal(){
    return module::get_var("basket","paypal");
  }

  static function getPaypalAccount(){
    return module::get_var("basket","paypal_account");
  }

  static function getCurrency(){
    $cur = module::get_var("basket","currency");
    if (!isset($cur))
    {
      $cur = "EUR";
    }
    return $cur;
  }
	/*
	* Get pickup location from user profile unless it's empty. Then get it from the general settings.
	*/
  static function getPickupLocation(){
    $user = identity::active_user();
    $user_basket = ORM::factory("user_basket")->where("id", "=", $user->id)->find();
    $pickup_location = $user_basket->pickup_location;
    if (($pickup_location == "") or ($pickup_location == null)) {
            $pickup_location = module::get_var("basket","pickup_location");}
    return $pickup_location;
  }
	
  static function getPaymentDetails(){
    return module::get_var("basket","payment_details");
  }

  static function getOrderBankAccount(){
    return module::get_var("basket","order_bankaccount");
  }

  static function getOrderAccountOwner(){
    return module::get_var("basket","order_accountowner");
  }

  static function getOrderPrefix(){
    return module::get_var("basket","order_prefix");
  }

  static function isAllowPickup(){
    return module::get_var("basket","allow_pickup");
  }

  static function getOrderCompletePage(){
    return module::get_var("basket","order_complete_page");
  }

  static function getOrderCompleteEmail(){
    return module::get_var("basket","order_complete_email");
  }
  static function getOrderCompleteEmailSubject(){
    return module::get_var("basket","order_complete_email_subject");
  }

  static function getOrderPaidEmail(){
    return module::get_var("basket","order_paid_email");
  }

  static function getOrderPaidEmailSubject(){
    return module::get_var("basket","order_paid_email_subject");
  }

  static function getOrderPaidDeliveredEmail(){
    return module::get_var("basket","order_paid_delivered_email");
  }
  static function getOrderPaidDeliveredEmailSubject(){
    return module::get_var("basket","order_paid_delivered_email_subject");
  }

  static function getOrderLatePaymentEmail(){
    return module::get_var("basket","order_late_payment_email");
  }
  static function getOrderLatePaymentEmailSubject(){
    return module::get_var("basket","order_late_payment_email_subject");
  }

  static function getOrderDeliveredEmail(){
    return module::get_var("basket","order_delivered_email");
  }
  static function getOrderDeliveredEmailSubject(){
    return module::get_var("basket","order_delivered_email_subject");
  }

  static function getOrderDeliveredNotPaidEmail(){
    return module::get_var("basket","order_delivered_notpaid_email");
  }
  static function getOrderDeliveredNotPaidEmailSubject(){
    return module::get_var("basket","order_delivered_notpaid_email_subject");
  }

  static function getOrderCancelledEmail(){
    return module::get_var("basket","order_cancelled_email");
  }
  static function getOrderCancelledEmailSubject(){
    return module::get_var("basket","order_cancelled_email_subject");
  }

  static function getOrderEmailClosing(){
    return module::get_var("basket","order_email_closing");
  }

  static function formatMoney($money){
    return self::$format[self::getCurrency()].number_format($money,2,',','.');
//    return self::$format[self::getCurrency()].number_format($money,2);
  }

  static function formatMoneyForWeb($money){
    return self::$formatweb[self::getCurrency()]." ".number_format($money,2,',','.');
//return self::$formatweb[self::getCurrency()].number_format($money,2);
  }

  static function formatMoneyForMail($money){
    return basket::getCurrency()." ".number_format($money,2,',','.');
  }

  static function replaceStrings($string, $key_values) {
    // Replace x_y before replacing x.
    krsort($key_values, SORT_STRING);

    $keys = array();
    $values = array();
    foreach ($key_values as $key => $value) {
      $keys[] = "%$key";
      $values[] = $value;
    }
    return str_replace($keys, $values, $string);
  }

//Added 2011-10-02
  static function replaceStringsAll($string, $order) {
    $string_new = basket::replaceStrings($string,Array(
        "name"=>$order->name,
        "order_number"=> basket::getOrderPrefix().$order->id,
        "total_cost" => basket::formatMoneyForMail($order->cost),
        "order_details"=> $order->text,
                    "email"=>basket::getEmailAddress(),	
        "pickup_location"=> basket::getPickupLocation(),
        "website"=> basket::getWebsite(),
        "webshop"=> basket::getWebshop()));
    return $string_new;
  }

//Added 2011-10-02
  static function createFullName($basket) {
    if ($basket->title <> ""):$fullname = $basket->title." "; endif;
    if ($basket->initials <> ""):$fullname = $fullname."".$basket->initials." "; endif;
    if ($basket->insertion <> ""):$fullname = $fullname."".$basket->insertion." "; endif;
    $fullname = $fullname."".$basket->name."";
	return $fullname;
  }

//Added 2011-10-02
  static function deliveryMethod($order) {
  //@TODO: configurable delivery methods
    if ($order->method == Order_Model::DELIVERY_MAIL) {$delivery_method = "verstuurd per post";}
    elseif ($order->method == Order_Model::DELIVERY_EMAIL) {$delivery_method = "verstuurd per e-mail";}
    elseif ($order->method == Order_Model::DELIVERY_PICKUP) {$delivery_method = "klaargelegd om af te halen bij ".basket::getPickupLocation()."";}
    else {$delivery_method = "GEEN LEVERINGSWIJZE BEKEND";}
    return $delivery_method;
  }

  static function setWebshop($webshop){
    module::set_var("basket","webshop",$webshop);
  }

  static function setWebsite($website){
    module::set_var("basket","website",$website);
  }

  static function setEmailAddress($email){
    module::set_var("basket","email",$email);
  }

  static function setPaypal($paypal){
    module::set_var("basket","paypal",$paypal);
  }

  static function setPaypalAccount($paypal_account){
    module::set_var("basket","paypal_account",$paypal_account);
  }

  static function setCurrency($currency){
    module::set_var("basket","currency",$currency);
  }

  static function setPickupLocation($pickup_location){
    module::set_var("basket","pickup_location",$pickup_location);
  }

  static function setPaymentDetails($details){
    module::set_var("basket","payment_details",$details);
  }

  static function setOrderBankAccount($order_bankaccount){
    module::set_var("basket","order_bankaccount",$order_bankaccount);
  }

  static function setOrderAccountOwner($order_accountowner){
    module::set_var("basket","order_accountowner",$order_accountowner);
  }

  static function setAllowPickup($allow_pickup){
    module::set_var("basket","allow_pickup",$allow_pickup);
  }

  static function setOrderPrefix($order_prefix){
    module::set_var("basket","order_prefix",strtolower($order_prefix));
  }

  static function setOrderCompletePage($details){
    module::set_var("basket","order_complete_page",$details);
  }

  static function setOrderCompleteEmail($details){
    module::set_var("basket","order_complete_email",$details);
  }
  static function setOrderCompleteEmailSubject($details){
    module::set_var("basket","order_complete_email_subject",$details);
  }

  static function setOrderLatePaymentEmail($details){
    module::set_var("basket","order_late_payment_email",$details);
  }
  static function setOrderLatePaymentEmailSubject($details){
    module::set_var("basket","order_late_payment_email_subject",$details);
  }

  static function setOrderPaidEmail($details){
    module::set_var("basket","order_paid_email",$details);
  }
  static function setOrderPaidEmailSubject($details){
    module::set_var("basket","order_paid_email_subject",$details);
  }

  static function setOrderPaidDeliveredEmail($details){
    module::set_var("basket","order_paid_delivered_email",$details);
  }
  static function setOrderPaidDeliveredEmailSubject($details){
    module::set_var("basket","order_paid_delivered_email_subject",$details);
  }

  static function setOrderDeliveredEmail($details){
    module::set_var("basket","order_delivered_email",$details);
  }
  static function setOrderDeliveredEmailSubject($details){
    module::set_var("basket","order_delivered_email_subject",$details);
  }

  static function setOrderDeliveredNotPaidEmail($details){
    module::set_var("basket","order_delivered_notpaid_email",$details);
  }
  static function setOrderDeliveredNotPaidEmailSubject($details){
    module::set_var("basket","order_delivered_notpaid_email_subject",$details);
  }

  static function setOrderCancelledEmail($details){
    module::set_var("basket","order_cancelled_email",$details);
  }
  static function setOrderCancelledEmailSubject($details){
    module::set_var("basket","order_cancelled_email_subject",$details);
  }

  static function setOrderEmailClosing($details){
    module::set_var("basket","order_email_closing",$details);
  }

  static function createOrder($basket, $method){
// fill customer record; added 2011-08-20
    $customer = ORM::factory("customer");
    $customer->title=$basket->title;
    $customer->initials=$basket->initials;
    $customer->insertion=$basket->insertion;
    $customer->name=$basket->name;
    $customer->street=$basket->street;
    $customer->housenumber=$basket->house;
    $customer->postalcode=$basket->postcode;
    $customer->town=$basket->town;
    $customer->email=$basket->email;
    $customer->phone=$basket->phone;
//    $customer->childname=$basket->childname;
//    $customer->childgroup=$basket->childgroup;
    $customer->deliverypref=$basket->ispp();
    $customer->save();
    
    $order = ORM::factory("order");
    $order->text = "processing";
    $order->customerid=$customer->id;
    $order->save();

    $ordernumber = basket::getOrderPrefix().$order->id;
    $order_bankaccount = basket::getOrderBankAccount();
    $order_accountowner = basket::getOrderAccountOwner();
    $order_email_closing = basket::getOrderEmailClosing();
    //$admin_address = basket::getEmailAddress();
    $website=basket::getWebsite();
    $postage = $basket->postage_cost();
    $product_cost = $basket->cost();
    $street = $basket->street;
    $fullname = basket::createFullName($basket);
    $ppon = $basket->ispp();
    if ($ppon){
      $total_cost = ($product_cost+$postage);
    }
    else{
      $total_cost = $product_cost;
    }
    // added Dutch mailtext JtK
    //set the timezone to show correct order time
    date_default_timezone_set('Europe/Amsterdam');
    $text = "U kunt betalen door het totaalbedrag over te maken op de bankrekening van %webshop.
Totaalbedrag: ".basket::formatMoneyForMail($total_cost)."
Rekeningnummer: ".$order_bankaccount."
 tnv ".$order_accountowner."
 ovv bestelnummer ".$ordernumber."

De aflevering vindt plaats circa 10 werkdagen nadat uw betaling is ontvangen door %webshop.
Voor vragen of opmerkingen over uw bestelling kunt u contact opnemen via bestelling@%website.

BESTELGEGEVENS
Bestelnummer: ".$ordernumber."
Besteld op ".date("d-m-Y G:i")."

Bestemd voor:
  ".$fullname."";
    if ($street <> ""){
      $text = $text."
  ".$basket->street." ".$basket->house."
  ".$basket->postcode." ".$basket->town."";
    }
$text = $text."

  E-mail: ".$basket->email."";
    if ($basket->phone <> ""){
      $text = $text."
  Telefoon: ".$basket->phone."
";  }
    if ($basket->comments <> ""){
      $text = $text."
Opmerking bij de bestelling: ".$basket->comments."
";  }
$text = $text."

Bestelbedrag: ".basket::formatMoneyForMail($product_cost);
  // continue with payment details 
    // posting by normal mail
    if ($ppon && $postage > 0){
      $method = Order_Model::DELIVERY_MAIL;
      $text = $text."
Verpakkings- en verzendkosten: ".basket::formatMoneyForMail($postage)."
Bestelling wordt verstuurd via post.";
    }
    // pickup
    elseif (!$ppon && $postage > 0){
      $method = Order_Model::DELIVERY_PICKUP;
      $text = $text."
Bestelling afhalen bij %pickup_location.";
    }
    // posting by e-mail
    else{
      $method = Order_Model::DELIVERY_EMAIL;
      $text = $text."
Bestelling wordt verstuurd via e-mail.";
    }
    $text = $text."
Totaalbedrag: ".basket::formatMoneyForMail($total_cost)."";
		  
	// continue with order details
    $text = $text."
		
Bestelde foto's:";
    // create the order items
    foreach ($basket->contents as $basket_item){
      $item = $basket_item->getItem();
      $prod = ORM::factory("product", $basket_item->product);
      $text = $text."
-> ".$item->title."
   ".$prod->name." - ".$prod->description."
   aantal: ".$basket_item->quantity." a ".basket::formatMoneyForMail($prod->cost)."
";  }
	// continue with footer
	$text = $text."
".$order_email_closing."";
	//replace variables
	$text = basket::replaceStringsAll($text,$order);

	$order->name = $fullname;
	$order->email = $basket->email;
	$order->cost = $total_cost;
	$order->text = $text;
	$order->status = Order_Model::WAITING_PAYMENT;
	$order->method = $method;
	$order->save();

    //$basket->clear();

    return $order;
  }

  public function send_order($order){
/* internal order mail */
    $to = basket::getEmailAddress();
	$from = "From: ".basket::getEmailAddress();
    $subject = "Bestelling ".basket::getOrderPrefix().$order->id." van ".$order->name;
	$body = $order->text;
	//send mail
    mail($to, $subject, $body, $from);
  }

  public function send_invoice($order){
  // order confirmation mail to customer 
    $to = $order->email;
	$from = "From: ".basket::getEmailAddress();
	$subject = basket::replaceStringsAll(basket::getOrderCompleteEmailSubject(),$order);
    $body = basket::getOrderCompleteEmail();
	//replace variables
	$body = basket::replaceStringsAll($body,$order);
	//send mail
    mail($to, $subject, $body, $from);
  }

    public function send_invoice_copy($order){
  // copy of order confirmation mail to customer 
    $to = $order->email;
	$from = "From: ".basket::getEmailAddress();
	$subject = basket::replaceStringsAll(basket::getOrderCompleteEmailSubject(),$order);
	$subject = $subject." (KOPIE)";
    $body = basket::getOrderCompleteEmail();
	//replace variables
	$body = basket::replaceStringsAll($body,$order);
	//send mail
    mail($to, $subject, $body, $from);
  }

public function send_payment_confirmation($order){
  // payment confirmation mail to customer 
    $to = $order->email;
	$from = "From: ".basket::getEmailAddress();
	if ($order->status == Order_Model::PAYMENT_CONFIRMED) {
		$subject = basket::replaceStringsAll(basket::getOrderPaidEmailSubject(),$order);
    $body = basket::getOrderPaidEmail();
	}
	elseif ($order->status == Order_Model::DELIVERED) {
		$subject = basket::replaceStringsAll(basket::getOrderPaidDeliveredEmailSubject(),$order);
    $body = basket::getOrderPaidDeliveredEmail();
	}
		$body = $body."
".basket::getOrderEmailClosing()."";
	//replace variables
	$body = basket::replaceStringsAll($body,$order);
	//send mail
    mail($to, $subject, $body, $from);
  }

  public function send_payment_reminder($order){
  // payment reminder mail to customer 
    $to = $order->email;
/*
@TODO: replace with getEmailAdressFrom
*/
	$from = "From: ".basket::getEmailAddress();
	$subject = basket::replaceStringsAll(basket::getOrderLatePaymentEmailSubject(),$order);
    $body = basket::getOrderLatePaymentEmail()."
".basket::getOrderEmailClosing()."

OORSPRONKELIJKE BESTELGEGEVENS
".$order->text."";
	//replace variables
	$body = basket::replaceStringsAll($body,$order);
	//send mail
    mail($to, $subject, $body, $from);
  }

  public function send_delivery_confirmation($order){
  // delivered confirmation mail to customer 
    $to = $order->email;
		$from = "From: ".basket::getEmailAddress();
		if ($order->status == Order_Model::DELIVERED) {
			$subject = basket::replaceStringsAll(basket::getOrderdeliveredEmailSubject(),$order);
			$body = basket::getOrderDeliveredEmail();
		}
		elseif ($order->status == Order_Model::DELIVERED_NOTPAID) {
		$subject = basket::replaceStringsAll(basket::getOrderDeliveredNotPaidEmailSubject(),$order);
			$body = basket::getOrderDeliveredNotPaidEmail();
		}
		$body = $body."
".basket::getOrderEmailClosing()."";
		//replace variables
		$body = basket::replaceStringsAll($body,$order);
    $body = basket::replaceStrings($body,Array("delivery_method"=>basket::deliveryMethod($order)));
	//send mail
    mail($to, $subject, $body, $from);          
  }

  public function send_cancellation_confirmation($order){
  // delivery cancelled mail to customer 
    $to = $order->email;
		$from = "From: ".basket::getEmailAddress();
		$subject = basket::replaceStringsAll(basket::getOrderCancelledEmailSubject(),$order);
    $body = basket::getOrderCancelledEmail()."
".basket::getOrderEmailClosing()."";
		//replace variables
		$body = basket::replaceStringsAll($body,$order);
		//send mail
    mail($to, $subject, $body, $from);           
  }

}