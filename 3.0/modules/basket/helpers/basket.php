<?php
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
    "MXN" => "");

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
    $group->input("email")->label(t("Offline Paying Email Address"))->id("g-order-email-address");
    $group->dropdown("currency")
    ->label(t("Currency"))
    ->options(self::$currencies);

    $group->checkbox("side_bar")->label(t("Use only side bar"))->id("g-side-bar-only");

    $group->checkbox("paypal")->label(t("Use Paypal"))->id("g-paypal");
    $group->input("paypal_account")->label(t("Paypal E-Mail Address"))->id("g-paypal-address");
    $group->checkbox("allow_pickup")->label(t("Allow Product Pickup"))->id("g-allow-pickup");
    $group->input("order_prefix")->label(t("Order Number Prefix"))->id("g-order-prefix");
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
    $group->submit("")->value(t("Save"));
    return $form;
  }

  static function populateForm($form){
    $form->configure->email->value(basket::getEmailAddress());
    $form->configure->side_bar->checked(basket::is_side_bar_only());
    $form->configure->paypal->checked(basket::isPaypal());
    $form->configure->paypal_account->value(basket::getPaypalAccount());
    $form->configure->currency->selected(basket::getCurrency());
    $form->configure->allow_pickup->checked(basket::isAllowPickup());
    $form->configure->order_prefix->value(basket::getOrderPrefix());
  }

  static function populateTemplateForm($form){
    $form->configure->payment_details->value(basket::getPaymentDetails());
    $form->configure->order_complete_page->value(basket::getOrderCompletePage());
    $form->configure->order_complete_email_subject->value(basket::getOrderCompleteEmailSubject());
    $form->configure->order_complete_email->value(basket::getOrderCompleteEmail());
  }

  static function extractForm($form){
    $email = $form->configure->email->value;
    $is_side_bar = $form->configure->side_bar->value;
    $isPaypal = $form->configure->paypal->value;
    $paypal_account = $form->configure->paypal_account->value;
    $currency = $form->configure->currency->selected;
    $allow_pickup = $form->configure->allow_pickup->value;
    $order_prefix = $form->configure->order_prefix->value;
    basket::setEmailAddress($email);
    basket::set_side_bar_only($is_side_bar);
    basket::setPaypal($isPaypal);
    basket::setPaypalAccount($paypal_account);
    basket::setCurrency($currency);
    basket::setAllowPickup($allow_pickup);
    basket::setOrderPrefix($order_prefix);
  }
  static function extractTemplateForm($form){
    $payment_details = $form->configure->payment_details->value;
    $order_complete_page = $form->configure->order_complete_page->value;
    $order_complete_email_subject = $form->configure->order_complete_email_subject->value;
    $order_complete_email = $form->configure->order_complete_email->value;
    basket::setPaymentDetails($payment_details);
    basket::setOrderCompletePage($order_complete_page);
    basket::setOrderCompleteEmailSubject($order_complete_email_subject);
    basket::setOrderCompleteEmail($order_complete_email);
  }

  static public function is_side_bar_only()
  {
    return module::get_var("basket","is_side_bar_only");

  }

  static public function set_side_bar_only($value)
  {
    module::set_var("basket","is_side_bar_only",$value);

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
      $cur = "USD";
    }
    return $cur;
  }

  static function getPaymentDetails(){
    return module::get_var("basket","payment_details");
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

  static function formatMoney($money){
    return self::$format[self::getCurrency()].number_format($money,2);
  }

  static function formatMoneyForWeb($money){
    return self::$formatweb[self::getCurrency()].number_format($money,2);
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

  static function setPaymentDetails($details){
    module::set_var("basket","payment_details",$details);
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

  static function createOrder($basket, $method){

    $order = ORM::factory("order");
    $order->text = "processing";
    $order->save();

    $ordernumber = basket::getOrderPrefix().$order->id;

    //$admin_address = basket::getEmailAddress();
    $postage = $basket->postage_cost();
    $product_cost = $basket->cost();
    $ppon = $basket->ispp();

    $text = "
    Order Number : ".$ordernumber."

    for :
".$basket->name."
".$basket->house."
".$basket->street."
".$basket->suburb."
".$basket->town."
".$basket->postcode."
".$basket->email."
".$basket->phone."
Placed at ".date("d F Y - H:i" ,time())."
Cost of Ordered Products = ".$product_cost;
    if ($ppon){
      $text = $text."
Postage and Packaging Costs + ".$postage."
Total Owed ".($product_cost+$postage)." Total in ".basket::getCurrency();
    }
    else{
      $text = $text."
Person has chosen to pick up product.
Total Owed ".($product_cost)." Total in ".basket::getCurrency();
    }
    $text = $text."

Items Ordered:

";

    // create the order items
    foreach ($basket->contents as $basket_item){
      $item = $basket_item->getItem();
      $prod = ORM::factory("product", $basket_item->product);
      $text = $text."
".$item->title." - ".$item->url()."
".$prod->name." - ".$prod->description."
".$basket_item->quantity." @ ".$prod->cost."

";
    }

    if ($ppon){
      $total_cost = ($product_cost+$postage);
    }
    else{
      $total_cost = $product_cost;
    }

    $order->name = $basket->name;
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

    $from = "From: ".basket::getEmailAddress();
    $ordernumber = basket::getOrderPrefix().$order->id;

    mail(basket::getEmailAddress(), "Order ".$ordernumber." from ".$order->name, $order->text, $from);

  }

  public function send_invoice($order)
  {

    $from = "From: ".basket::getEmailAddress();
    $ordernumber = basket::getOrderPrefix().$order->id;
    $invoice_email = basket::replaceStrings(basket::getOrderCompleteEmail(),Array(
            "name"=>$order->name,
            "order_number"=> $ordernumber,
            "total_cost" =>basket::formatMoney($order->cost),
            "order_details"=>$order->text));

    mail($order->email,
        basket::replaceStrings(basket::getOrderCompleteEmailSubject(),Array("order_number"=>$ordernumber)),
        $invoice_email, $from);

  }

}