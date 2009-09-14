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


 static function get_configure_form() {
    $form = new Forge("admin/configure", "", "post", array("id" => "gConfigureForm"));
    $group = $form->group("configure")->label(t("Configure Basket"));
    $group->input("email")->label(t("Offline Paying Email Address"))->id("gOrderEmailAddress");
    $group->dropdown("currency")
      ->label(t("Currency"))
      ->options(self::$currencies);

    $group->checkbox("paypal")->label(t("Use Paypal"))->id("gPaypal");
    $group->input("paypal_account")->label(t("Paypal E-Mail Address"))->id("gPaypalAddress");
    $group->submit("")->value(t("Save"));
    return $form;
  }

  static function populateForm($form){
      $form->configure->email->value(basket::getEmailAddress());
      $form->configure->paypal->checked(basket::isPaypal());
      $form->configure->paypal_account->value(basket::getPaypalAccount());
      $form->configure->currency->selected(basket::getCurrency());
  }

  static function extractForm($form){
      $email = $form->configure->email->value;
      $isPaypal = $form->configure->paypal->value;
      $paypal_account = $form->configure->paypal_account->value;
      $currency = $form->configure->currency->selected;
      basket::setEmailAddress($email);
      basket::setPaypal($isPaypal);
      basket::setPaypalAccount($paypal_account);
      basket::setCurrency($currency);
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

  static function formatMoney($money){
    return self::$format[self::getCurrency()].number_format($money);
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

  static function generatePaypalForm($session_basket){
    $form = "
<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" name=\"paypal_form\">
<input type=\"hidden\" name=\"cmd\" value=\"_cart\"/>
<input type=\"hidden\" name=\"upload\" value=\"1\"/>
<input type=\"hidden\" name=\"currency_code\" value=\"".self::getCurrency()."\">
<input type=\"hidden\" name=\"business\" value=\"".self::getPaypalAccount()."\"/>";

    $id = 1;
    foreach ($session_basket->contents as $key => $basket_item){
      $form = $form."
<input type=\"hidden\" name=\"item_name_$id\" value=\"".$basket_item->getCode()."\"/>
<input type=\"hidden\" name=\"amount_$id\" value=\"$basket_item->cost_per\"/>
<input type=\"hidden\" name=\"quantity_$id\" value=\"$basket_item->quantity\"/>";
      $id++;
    }
    $form = $form."</form>";

    return $form;
  }

}