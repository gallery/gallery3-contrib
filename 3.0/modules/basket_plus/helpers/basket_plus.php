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
  
/* ============================================================
 * DEFINED CONSTANTS aim: improve readability of basket code
 * ============================================================ */
define("META_TAG", "meta_tag");
//configuration
define("WEBSITE", "website");
define("WEBSHOP", "webshop");
define("WEBSHOP_OWNER", "webshop_owner");
define("WEBSHOP_ADDRESS", "webshop_address");
define("WEBSHOP_POSTALCODE", "webshop_postalcode");
define("WEBSHOP_CITY", "webshop_city");
define("WEBSHOP_PHONE", "webshop_phone");
define("WEBSHOP_DETAILS", "webshop_details");
define("EMAIL_CONTACT", "email_contact");
define("EMAIL_ORDER", "email_order");
define("EMAIL_FROM", "email_from");
define("USE_SIDE_BAR_ONLY", "use_side_bar_only");
define("ALLOW_PICKUP", "allow_pickup"); 
define("IS_PICKUP_DEFAULT", "is_pickup_default"); 
define("PICKUP_LOCATION", "pickup_location"); 
define("USE_PAYPAL", "use_paypal");
define("PAYPAL_ACCOUNT", "paypal_account"); 
define("PAYPAL_TEST_MODE", "paypal_test_mode");
define("CURRENCY", "currency");
define("DECIMAL_SEPARATOR", "decimal_separator");
define("DATE_TIME_FORMAT", "date_time_format");
define("ORDER_PREFIX", "order_prefix");
define("ORDER_BANK_ACCOUNT", "order_bank_account");
define("ORDER_BANK_ACCOUNT_OWNER", "order_bank_account_owner");
define("PAYMENT_OPTIONS", "payment_options"); 
define("ADDRESS_FORMAT", "address_format");
define("USE_ADDRESS_SUBURB", "use_address_suburb");
define("USE_ADDRESS_PROVINCE", "use_address_province");
define("USE_ADDRESS_COUNTRY", "use_address_country");

//templates
define("PAYMENT_DETAILS", "payment_details"); 
define("CUSTOMER_DETAILS", "customer_details"); 
define("ORDER_COMPLETE_PAGE", "order_complete_page"); 
define("ORDER_PAID_COMPLETE_PAGE", "order_paid_complete_page"); 
define("ORDER_THANKYOU", "order_thankyou"); 
define("ORDER_DETAILS", "order_details"); 
define("ORDER_COMPLETE_EMAIL_SUBJECT", "order_complete_email_subject"); 
define("EMAIL_TEMPLATE_STYLE", "email_template_style"); 
define("ORDER_PAID_EMAIL_SUBJECT", "order_paid_email_subject"); 
define("ORDER_PAID_EMAIL", "order_paid_email"); 
define("ORDER_PAID_DELIVERED_EMAIL_SUBJECT", "order_paid_delivered_email_subject"); 
define("ORDER_PAID_DELIVERED_EMAIL", "order_paid_delivered_email"); 
define("ORDER_LATE_PAYMENT_EMAIL_SUBJECT", "order_late_payment_email_subject"); 
define("ORDER_LATE_PAYMENT_EMAIL", "order_late_payment_email"); 
define("ORDER_DELIVERED_EMAIL_SUBJECT", "order_delivered_email_subject"); 
define("ORDER_DELIVERED_EMAIL", "order_delivered_email"); 
define("ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT", "order_delivered_notpaid_email_subject"); 
define("ORDER_DELIVERED_NOTPAID_EMAIL", "order_delivered_notpaid_email"); 
define("ORDER_DELAYED_EMAIL_SUBJECT", "order_delayed_email_subject"); 
define("ORDER_DELAYED_EMAIL", "order_delayed_email"); 
define("ORDER_CANCELLED_EMAIL_SUBJECT", "order_cancelled_email_subject"); 
define("ORDER_CANCELLED_EMAIL", "order_cancelled_email"); 
define("ORDER_EMAIL_CLOSING", "order_email_closing"); 
define("ORDER_EMAIL_FOOTER", "order_email_footer"); 
define("ORDER_EMAIL_LOGO", "order_email_logo");
 
class basket_plus_Core {

  static $currencies = array(
    "EUR" => "Euro",
    "GBP" => "Pounds Sterling",
    "USD" => "U.S. Dollar",
    "AUD" => "Australian Dollar",
    "CAD" => "Canadian Dollar",
    "NZD" => "New Zealand Dollar",
    "JPY" => "Yen",
    "CHF" => "Swiss Franc",
    "DKK" => "Danish Krone",
    "SEK" => "Swedish Krona",
    "NOK" => "Norwegian Krone",
    "HKD" => "Hong Kong Dollar",
    "SGD" => "Singapore Dollar",
    "ILS" => "Israeli Shekel",
    "MXN" => "Mexican Peso");

  static $curr_symbols= array(
    "AUD" => "$",
    "CAD" => "$",
    "EUR" => "€",
    "GBP" => "£",
    "JPY" => "¥",
    "USD" => "$",
    "NZD" => "$",
    "CHF" => "CHF",
    "HKD" => "$",
    "SGD" => "$",
    "SEK" => "",
    "DKK" => "",
    "NOK" => "",
    "ILS" => "",
    "MXN" => "",
    "none" => "");

  static $curr_symbols_web= array(
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
    "NOK" => "",
    "ILS" => "",
    "MXN" => "");

		static $decimal_separators = array(
    "." => ". (period)",
		"," => ", (comma)");

		static $date_time_formats = array(
    "m/d/Y g:i A" => "m/d/Y g:i A (12/28/2013 6:44 PM)",
		"d-m-Y G:i" => "d-m-Y G:i (28-12-2013 18:44)");
		
/* ============================================================
 * Generic functions 
 * ============================================================ */
/*
 * USED IN: basket admin functions
 * DESCRIPTION: Reset the cache of variables
 */
  static function reset_var_cache() {
		self::$var_cache = Cache::instance()->get("var_cache");
		// Rebuild cache
		foreach (db::build()
				 ->select("module_name", "name", "value")
				 ->from("vars")
				 ->order_by("module_name")
				 ->order_by("name")
				 ->execute() as $row) {
			// Mute the "Creating default object from empty value" warning below
			@self::$var_cache->{$row->module_name}->{$row->name} = $row->value;
		}
		Cache::instance()->set("var_cache", self::$var_cache, array("vars"));
  }
  
//@TODO: clear table function, see class basket_plus_installer_local

/*
 * USED IN: various situations
 * DESCRIPTION: Returns a basket setting (from db table 'vars')
 */
  static function getBasketVar($basket_var){
    return module::get_var("basket_plus",$basket_var);
  }
  
/*
 * USED IN: various situations
 * DESCRIPTION: Sets a basket setting (in db table 'vars')
 */
  static function setBasketVar($basket_var_name,$var_value){
    module::set_var("basket_plus","$basket_var_name",$var_value);
  }

/*
 * USED IN: various situations
 * DESCRIPTION: Formats a technical name to use as a label shown with a setting
 * Replaces each underscore by a space and capitalize each word
 */
  static function formatLabel($var){
    $label = str_replace("_", " ",$var);
		$label = ucwords($label);
    return $label;
  }

// Format money with 2 decimals (eg. 5.00)
// Thousands separator not used
  static function formatMoney($money){
		$decimal_separator = basket_plus::getBasketVar(DECIMAL_SEPARATOR);
    return self::$curr_symbols[self::getCurrency()].number_format($money,2,$decimal_separator,'');
  }

// Format money with money symbol and 2 decimals (eg $ 3.00)
// Thousands separator not used
  static function formatMoneyForWeb($money){
		$decimal_separator = basket_plus::getBasketVar(DECIMAL_SEPARATOR);
    return self::$curr_symbols_web[self::getCurrency()]." ".number_format($money,2,$decimal_separator,'');
  }

// Format money with money symbol as text and 2 decimals (eg EUR 4,00)
// Thousands separator not used
  static function formatMoneyForMail($money){
		$decimal_separator = basket_plus::getBasketVar(DECIMAL_SEPARATOR);
    return basket_plus::getCurrency()." ".number_format($money,2,$decimal_separator,'');
  }

/*
 * USED IN: various situations
 * DESCRIPTION: Replaces a string with a variable set of key-value combinations
 */
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

/*
 * USED IN: various situations
 * DESCRIPTION: Replaces a fixed set of basket variables in a string with basket and order values
 */
  static function replaceStringsAll($string, $order) {
		$string_new = basket_plus::replaceStrings($string,Array(
			"name" => $order->name,
			"order_number"=> basket_plus::getBasketVar(ORDER_PREFIX).$order->id,
			"total_cost" => basket_plus::formatMoneyForMail($order->cost),
//			"order_lines"=> $order->text,
			EMAIL_ORDER => basket_plus::getBasketVar(EMAIL_ORDER),	
			PICKUP_LOCATION => basket_plus::getPickupLocation(),
			WEBSITE => basket_plus::getBasketVar(WEBSITE),
			WEBSHOP => basket_plus::getBasketVar(WEBSHOP)));

		return $string_new;
  }

/*
 * USED IN: various situations
 * DESCRIPTION: Returns the current basket currency; if not set, returns EUR
 */
  static function getCurrency(){
    $cur = basket_plus::getBasketVar(CURRENCY);
    if (!isset($cur)) {
      $cur = "EUR";
    }
    return $cur;
  }

/*
 * USED IN: various situations
 * DESCRIPTION: strips html from a text
 * DOC: see http://php.net/manual/en/function.strip-tags.php, user comment
 */
  static function html2txt($document){ 
		$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript 
						 '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags 
						 '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly 
						 '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA 
		); 
		$text = preg_replace($search, '', $document); 
		return $text; 
  } 

  /** MODIFIED function item::thumb_img 
	 * USED IN: order process: order e-mail
   * Return an <img> tag for the thumbnail with an absolute url, for use in mail etc.
   * @param $abs_url  Absolute url to prepend the thumbnail url (format 'http://www.domain.com')
   * @param int (optional) $max Maximum size of the thumbnail (default: null)
   * @param boolean (optional) $center_vertically Center vertically (default: false)
   * @return string
   */
  static function thumb_img_abs($extra_attrs=array(), $max=null, $center_vertically=false, $item, $abs_url) {
		list ($height, $width) = $item->scale_dimensions($max);
    if ($center_vertically && $max) {
      // The constant is divided by 2 to calculate the file and 10 to convert to em
      $margin_top = (int)(($max - $height) / 20);
    }
    $attrs = array_merge($extra_attrs,
            array(
              "src" => $abs_url.$item->thumb_url(),
              "alt" => $item->title,
              "width" => $width,
              "height" => $height)
						);
    return "<img" . html::attributes($attrs) . "/>";
  }

/*
 * USED IN: order process: create_order
 * DESCRIPTION: Gets pickup location from user profile unless it's empty. Then gets it from the basket configuration.
 */
  static function getPickupLocation(){
		$user = identity::active_user();

		//get user_basket setting
		$user_basket = ORM::factory("bp_user_basket")->where("id", "=", $user->id)->find();
		$pickup_location = $user_basket->pickup_location;

		//if user_basket setting is empty, get the default pickup location
		if (($pickup_location == "") or ($pickup_location == null)) {
			$pickup_location = basket_plus::getBasketVar(PICKUP_LOCATION);
		}
		return $pickup_location;
  }

  static function getUserIsGroup() {
		$user = identity::active_user();
		//get user_basket setting
		$user_basket = ORM::factory("bp_user_basket")->where("id", "=", $user->id)->find();
		$is_group = $user_basket->is_group;
		return $is_group;
  }
  
/*
 * USED IN: order process: create_order
 * DESCRIPTION: Creates a string with the customers full name incl. title, initials and insertion
 */
  static function createFullName($basket) {
		$fullname = "";
    if ($basket->title <> ""){
			$fullname = $basket->title." ";
		}
    if ($basket->initials <> ""){
			$fullname .= $basket->initials." ";
		}
    if ($basket->insertion <> ""){
			$fullname .= $basket->insertion." ";
		}
    $fullname .= $basket->fname;
		return $fullname;
  }

/*
 * USED IN: order process: update of the order
 * DESCRIPTION: provides delivery method string for presentation after delivery
 * @TODO: configurable delivery methods
 */
  static function deliveredMethodText($order) {
  if ($order->delivery_method == Bp_Order_Model::DELIVERY_MAIL) {
		$delivered_method = t(basket_plus_label::TEXT_DELIVERED_MAIL);
		}
    elseif ($order->delivery_method == Bp_Order_Model::DELIVERY_EMAIL) {
			$delivered_method = t(basket_plus_label::TEXT_DELIVERED_EMAIL);
		}
    elseif ($order->delivery_method == Bp_Order_Model::DELIVERY_PICKUP) {
			$customer = ORM::factory("bp_customer")->where("id", "=", $order->customerid)->find();
			$delivered_method = t(basket_plus_label::TEXT_DELIVERED_PICKUP). " ".$customer->deliverypref;
		}
		else {
			$delivered_method = t("delivered");
		}
	return $delivered_method;
}

/*
 * USED IN: order process: create_order
 * DESCRIPTION: creates and saves a new customer record
 * @TODO: support other address formats 
 */
  static function createCustomer($basket){
    $customer = ORM::factory("bp_customer");
    $customer->title = $basket->title;
    $customer->initials = $basket->initials;
    $customer->insertion = $basket->insertion;
    $customer->name = $basket->fname;
    $customer->street = $basket->street;
    $customer->housenumber = $basket->house;
    $customer->postalcode = $basket->postalcode;
		$customer->suburb = $basket->suburb;
    $customer->town = $basket->town;
	  $customer->province = $basket->province;
	  $customer->country = $basket->country;
    $customer->email = $basket->email;
    $customer->phone = $basket->phone;
    $customer->order_ref1 = $basket->order_ref1;
    $customer->order_ref2 = $basket->order_ref2;
    $customer->deliverypref = basket_plus::getPickupLocation();
    $customer->save();

		return $customer;
  }

  public static $var_cache = null;

/*
 * USED IN: basket views 
 * DESCRIPTION: Determines if the active user may see the orders; returns true if allowed 
 */
  static public function can_view_orders() {
    //admin users can view orders
		if (identity::active_user()->admin){
      return true;
    }
		//user is not Admin. Check if user is member of group 'shop' (NB retained function from original Basket module)
    print identity::active_user();
    foreach (identity::active_user()->groups() as $group){
      if ($group->name == 'shop'){
        return true;
      }
    }
	//other users cannot view orders
    return false;
  }

/* ============================================================
 * Basket configuration form functions
 * See top for defined field names (e.g. WEBSHOP)
 * ============================================================ */
/*
 * USED IN: basket admin functions: configuration
 * DESCRIPTION: Creates the basket configuration form 
 */
  static function get_configure_form() {
    $form = new Forge("admin/configure", "", "post", array("id" => "g-configure-basket-form"));
    $group = $form->group("configure")->label(t("Configure Basket"));
    $group->input("WEBSITE")->label(t(basket_plus::formatLabel(WEBSITE)))->id("g-WEBSITE");
    $group->input("WEBSHOP")->label(t(basket_plus::formatLabel(WEBSHOP)))->id("g-WEBSHOP");
    $group->input("WEBSHOP_OWNER")->label(t(basket_plus::formatLabel(WEBSHOP_OWNER)))->id("g-WEBSHOP_OWNER");
    $group->input("WEBSHOP_ADDRESS")->label(t(basket_plus::formatLabel(WEBSHOP_ADDRESS)))->id("g-WEBSHOP_ADDRESS");
    $group->input("WEBSHOP_POSTALCODE")->label(t(basket_plus::formatLabel(WEBSHOP_POSTALCODE)))->id("g-WEBSHOP_POSTALCODE");
    $group->input("WEBSHOP_CITY")->label(t(basket_plus::formatLabel(WEBSHOP_CITY)))->id("g-WEBSHOP_CITY");
    $group->input("WEBSHOP_PHONE")->label(t(basket_plus::formatLabel(WEBSHOP_PHONE)))->id("g-WEBSHOP_PHONE");
    $group->input("EMAIL_CONTACT")->label(t(basket_plus::formatLabel(EMAIL_CONTACT)))->id("g-EMAIL_CONTACT");
    $group->input("EMAIL_ORDER")->label(t(basket_plus::formatLabel(EMAIL_ORDER)))->id("g-EMAIL_ORDER");
    $group->input("EMAIL_FROM")->label(t(basket_plus::formatLabel(EMAIL_FROM)))->id("g-EMAIL_FROM");
    $group->textarea("WEBSHOP_DETAILS")->label(t(basket_plus::formatLabel(WEBSHOP_DETAILS)))->id("g-WEBSHOP_DETAILS");
    $group->dropdown("CURRENCY")->label(t(basket_plus::formatLabel(CURRENCY)))->options(self::$currencies);
    $group->dropdown("DECIMAL_SEPARATOR")->label(t(basket_plus::formatLabel(DECIMAL_SEPARATOR)))->options(self::$decimal_separators);
    $group->dropdown("DATE_TIME_FORMAT")->label(t(basket_plus::formatLabel(DATE_TIME_FORMAT)))->options(self::$date_time_formats);
    $group->checkbox("USE_SIDE_BAR_ONLY")->label(t(basket_plus::formatLabel(USE_SIDE_BAR_ONLY)))->id("g-USE_SIDE_BAR_ONLY");
    $group->checkbox("ALLOW_PICKUP")->label(t(basket_plus::formatLabel(ALLOW_PICKUP)))->id("g-ALLOW_PICKUP");
    $group->checkbox("IS_PICKUP_DEFAULT")->label(t(basket_plus::formatLabel(IS_PICKUP_DEFAULT)))->id("g-IS_PICKUP_DEFAULT");
    $group->input("PICKUP_LOCATION")->label(t(basket_plus::formatLabel(PICKUP_LOCATION)))->id("g-PICKUP_LOCATION");
    $group->checkbox("USE_PAYPAL")->label(t(basket_plus::formatLabel(USE_PAYPAL)))->id("g-USE_PAYPAL");
    $group->input("PAYPAL_ACCOUNT")->label(t(basket_plus::formatLabel(PAYPAL_ACCOUNT)))->id("g-PAYPAL_ACCOUNT");
    $group->checkbox("PAYPAL_TEST_MODE")->label(t(basket_plus::formatLabel(PAYPAL_TEST_MODE)))->id("g-PAYPAL_TEST_MODE");
    $group->input("ORDER_PREFIX")->label(t(basket_plus::formatLabel(ORDER_PREFIX)))->id("g-ORDER_PREFIX");
    $group->input("ORDER_BANK_ACCOUNT")->label(t(basket_plus::formatLabel(ORDER_BANK_ACCOUNT)))->id("g-ORDER_BANK_ACCOUNT");
    $group->input("ORDER_BANK_ACCOUNT_OWNER")->label(t(basket_plus::formatLabel(ORDER_BANK_ACCOUNT_OWNER)))->id("g-ORDER_BANK_ACCOUNT_OWNER");
    $group->textarea("PAYMENT_OPTIONS")->label(t(basket_plus::formatLabel(PAYMENT_OPTIONS)))->id("g-PAYMENT_OPTIONS");
    $group->textarea("ADDRESS_FORMAT")->label(t(basket_plus::formatLabel(ADDRESS_FORMAT)))->id("g-ADDRESS_FORMAT");
    $group->checkbox("USE_ADDRESS_SUBURB")->label(t(basket_plus::formatLabel(USE_ADDRESS_SUBURB)))->id("g-USE_ADDRESS_SUBURB");
    $group->checkbox("USE_ADDRESS_PROVINCE")->label(t(basket_plus::formatLabel(USE_ADDRESS_PROVINCE)))->id("g-USE_ADDRESS_PROVINCE");
    $group->checkbox("USE_ADDRESS_COUNTRY")->label(t(basket_plus::formatLabel(USE_ADDRESS_COUNTRY)))->id("g-USE_ADDRESS_COUNTRY");
    $group->submit("")->value(t("Save"));
    return $form;
  }

/*
 * USED IN: basket admin functions: templates
 * DESCRIPTION: Fills the basket configuration form  
 */
  static function populateForm($form){
		//rebuild the variable cache to ensure the use of the latest DB values
		basket_plus::reset_var_cache();
		
		$form->configure->WEBSITE->value(basket_plus::getBasketVar(WEBSITE));
		$form->configure->WEBSHOP->value(basket_plus::getBasketVar(WEBSHOP));
		$form->configure->WEBSHOP_OWNER->value(basket_plus::getBasketVar(WEBSHOP_OWNER));
		$form->configure->WEBSHOP_ADDRESS->value(basket_plus::getBasketVar(WEBSHOP_ADDRESS));
		$form->configure->WEBSHOP_POSTALCODE->value(basket_plus::getBasketVar(WEBSHOP_POSTALCODE));
		$form->configure->WEBSHOP_CITY->value(basket_plus::getBasketVar(WEBSHOP_CITY));
		$form->configure->WEBSHOP_PHONE->value(basket_plus::getBasketVar(WEBSHOP_PHONE));
    $form->configure->EMAIL_CONTACT->value(basket_plus::getBasketVar(EMAIL_CONTACT));
    $form->configure->EMAIL_ORDER->value(basket_plus::getBasketVar(EMAIL_ORDER));
    $form->configure->EMAIL_FROM->value(basket_plus::getBasketVar(EMAIL_FROM));
    $form->configure->WEBSHOP_DETAILS->value(basket_plus::getBasketVar(WEBSHOP_DETAILS));
    $form->configure->CURRENCY->selected(basket_plus::getBasketVar(CURRENCY));
		$form->configure->DECIMAL_SEPARATOR->selected(basket_plus::getBasketVar(DECIMAL_SEPARATOR));
		$form->configure->DATE_TIME_FORMAT->selected(basket_plus::getBasketVar(DATE_TIME_FORMAT));
    $form->configure->USE_SIDE_BAR_ONLY->checked(basket_plus::getBasketVar(USE_SIDE_BAR_ONLY));
    $form->configure->ALLOW_PICKUP->checked(basket_plus::getBasketVar(ALLOW_PICKUP));
    $form->configure->IS_PICKUP_DEFAULT->checked(basket_plus::getBasketVar(IS_PICKUP_DEFAULT));
		$form->configure->PICKUP_LOCATION->value(basket_plus::getBasketVar(PICKUP_LOCATION));
    $form->configure->USE_PAYPAL->checked(basket_plus::getBasketVar(USE_PAYPAL));
		$form->configure->PAYPAL_ACCOUNT->value(basket_plus::getBasketVar(PAYPAL_ACCOUNT));
    $form->configure->PAYPAL_TEST_MODE->checked(basket_plus::getBasketVar(PAYPAL_TEST_MODE));
    $form->configure->ORDER_PREFIX->value(basket_plus::getBasketVar(ORDER_PREFIX));
    $form->configure->ORDER_BANK_ACCOUNT->value(basket_plus::getBasketVar(ORDER_BANK_ACCOUNT));
    $form->configure->ORDER_BANK_ACCOUNT_OWNER->value(basket_plus::getBasketVar(ORDER_BANK_ACCOUNT_OWNER));
    $form->configure->PAYMENT_OPTIONS->value(basket_plus::getBasketVar(PAYMENT_OPTIONS));
		$form->configure->ADDRESS_FORMAT->value(basket_plus::getBasketVar(ADDRESS_FORMAT));
    $form->configure->USE_ADDRESS_SUBURB->checked(basket_plus::getBasketVar(USE_ADDRESS_SUBURB));
    $form->configure->USE_ADDRESS_PROVINCE->checked(basket_plus::getBasketVar(USE_ADDRESS_PROVINCE));
    $form->configure->USE_ADDRESS_COUNTRY->checked(basket_plus::getBasketVar(USE_ADDRESS_COUNTRY));
  }

/*
 * USED IN: basket admin functions: configuration
 * DESCRIPTION: Saves the values of the basket configuration form 
 */
  static function extractForm($form){
    basket_plus::setBasketVar(WEBSITE,$form->configure->WEBSITE->value);
    basket_plus::setBasketVar(WEBSHOP,$form->configure->WEBSHOP->value);
    basket_plus::setBasketVar(WEBSHOP_OWNER,$form->configure->WEBSHOP_OWNER->value);
    basket_plus::setBasketVar(WEBSHOP_ADDRESS,$form->configure->WEBSHOP_ADDRESS->value);
    basket_plus::setBasketVar(WEBSHOP_POSTALCODE,$form->configure->WEBSHOP_POSTALCODE->value);
    basket_plus::setBasketVar(WEBSHOP_CITY,$form->configure->WEBSHOP_CITY->value);
    basket_plus::setBasketVar(WEBSHOP_PHONE,$form->configure->WEBSHOP_PHONE->value);
    basket_plus::setBasketVar(EMAIL_CONTACT,$form->configure->EMAIL_CONTACT->value);
    basket_plus::setBasketVar(EMAIL_ORDER,$form->configure->EMAIL_ORDER->value);
    basket_plus::setBasketVar(EMAIL_FROM,$form->configure->EMAIL_FROM->value);
    basket_plus::setBasketVar(WEBSHOP_DETAILS,$form->configure->WEBSHOP_DETAILS->value);
    basket_plus::setBasketVar(CURRENCY,$form->configure->CURRENCY->selected);
		basket_plus::setBasketVar(DECIMAL_SEPARATOR,$form->configure->DECIMAL_SEPARATOR->selected);
		basket_plus::setBasketVar(DATE_TIME_FORMAT,$form->configure->DATE_TIME_FORMAT->selected);
    basket_plus::setBasketVar(USE_SIDE_BAR_ONLY,$form->configure->USE_SIDE_BAR_ONLY->checked);
    basket_plus::setBasketVar(ALLOW_PICKUP,$form->configure->ALLOW_PICKUP->checked);
    basket_plus::setBasketVar(IS_PICKUP_DEFAULT,$form->configure->IS_PICKUP_DEFAULT->checked);
    basket_plus::setBasketVar(PICKUP_LOCATION,$form->configure->PICKUP_LOCATION->value);
    basket_plus::setBasketVar(USE_PAYPAL,$form->configure->USE_PAYPAL->checked);
    basket_plus::setBasketVar(PAYPAL_ACCOUNT,$form->configure->PAYPAL_ACCOUNT->value);
    basket_plus::setBasketVar(PAYPAL_TEST_MODE,$form->configure->PAYPAL_TEST_MODE->checked);
    basket_plus::setBasketVar(ORDER_PREFIX,$form->configure->ORDER_PREFIX->value);
    basket_plus::setBasketVar(ORDER_BANK_ACCOUNT,$form->configure->ORDER_BANK_ACCOUNT->value);
    basket_plus::setBasketVar(ORDER_BANK_ACCOUNT_OWNER,$form->configure->ORDER_BANK_ACCOUNT_OWNER->value);
    basket_plus::setBasketVar(PAYMENT_OPTIONS,$form->configure->PAYMENT_OPTIONS->value);
    basket_plus::setBasketVar(ADDRESS_FORMAT,$form->configure->ADDRESS_FORMAT->value);
    basket_plus::setBasketVar(USE_ADDRESS_SUBURB,$form->configure->USE_ADDRESS_SUBURB->checked);
    basket_plus::setBasketVar(USE_ADDRESS_PROVINCE,$form->configure->USE_ADDRESS_PROVINCE->checked);
    basket_plus::setBasketVar(USE_ADDRESS_COUNTRY,$form->configure->USE_ADDRESS_COUNTRY->checked);
  }

/* ============================================================
 * Basket templates form functions
 * See top for defined field names (e.g. PAYMENT_DETAILS)
 * ============================================================ */
/*
 * USED IN: basket admin functions: templates
 * DESCRIPTION: Creates the basket templates form 
 */
  static function get_template_form() {
    $form = new Forge("admin/configure/templates", "", "post", array("id" => "g-configure-form"));
    $group = $form->group("configure")->label(t("Configure Basket"));
    $group->textarea("PAYMENT_DETAILS")->label(t(basket_plus::formatLabel(PAYMENT_DETAILS)))->id("g-PAYMENT_DETAILS");
    $group->textarea("CUSTOMER_DETAILS")->label(t(basket_plus::formatLabel(CUSTOMER_DETAILS)))->id("g-CUSTOMER_DETAILS");
    $group->textarea("ORDER_COMPLETE_PAGE")->label(t(basket_plus::formatLabel(ORDER_COMPLETE_PAGE)))->id("g-ORDER_COMPLETE_PAGE");
    $group->textarea("ORDER_PAID_COMPLETE_PAGE")->label(t(basket_plus::formatLabel(ORDER_PAID_COMPLETE_PAGE)))->id("g-ORDER_PAID_COMPLETE_PAGE");
    $group->textarea("EMAIL_TEMPLATE_STYLE")->label(t(basket_plus::formatLabel(EMAIL_TEMPLATE_STYLE)))->id("g-EMAIL_TEMPLATE_STYLE");
    $group->textarea("ORDER_THANKYOU")->label(t(basket_plus::formatLabel(ORDER_THANKYOU)))->id("g-ORDER_THANKYOU");
    $group->textarea("ORDER_DETAILS")->label(t(basket_plus::formatLabel(ORDER_DETAILS)))->id("g-ORDER_DETAILS");
    $group->input("ORDER_PAID_EMAIL_SUBJECT")->label(t(basket_plus::formatLabel(ORDER_PAID_EMAIL_SUBJECT)))->id("g-ORDER_PAID_EMAIL_SUBJECT");
    $group->input("ORDER_COMPLETE_EMAIL_SUBJECT")->label(t(basket_plus::formatLabel(ORDER_PAID_EMAIL_SUBJECT)))->id("g-ORDER_PAID_EMAIL_SUBJECT");
    $group->textarea("ORDER_PAID_EMAIL")->label(t(basket_plus::formatLabel(ORDER_PAID_EMAIL)))->id("g-ORDER_PAID_EMAIL");
    $group->input("ORDER_PAID_DELIVERED_EMAIL_SUBJECT")->label(t(basket_plus::formatLabel(ORDER_PAID_DELIVERED_EMAIL_SUBJECT)))->id("g-ORDER_PAID_DELIVERED_EMAIL_SUBJECT");
    $group->textarea("ORDER_PAID_DELIVERED_EMAIL")->label(t(basket_plus::formatLabel(ORDER_PAID_DELIVERED_EMAIL)))->id("g-ORDER_PAID_DELIVERED_EMAIL");
    $group->input("ORDER_LATE_PAYMENT_EMAIL_SUBJECT")->label(t(basket_plus::formatLabel(ORDER_LATE_PAYMENT_EMAIL_SUBJECT)))->id("g-ORDER_LATE_PAYMENT_EMAIL_SUBJECT");
    $group->textarea("ORDER_LATE_PAYMENT_EMAIL")->label(t(basket_plus::formatLabel(ORDER_LATE_PAYMENT_EMAIL)))->id("g-ORDER_LATE_PAYMENT_EMAIL");
    $group->input("ORDER_DELIVERED_EMAIL_SUBJECT")->label(t(basket_plus::formatLabel(ORDER_DELIVERED_EMAIL_SUBJECT)))->id("g-ORDER_DELIVERED_EMAIL_SUBJECT");
    $group->textarea("ORDER_DELIVERED_EMAIL")->label(t(basket_plus::formatLabel(ORDER_DELIVERED_EMAIL)))->id("g-ORDER_DELIVERED_EMAIL");
    $group->input("ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT")->label(t(basket_plus::formatLabel(ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT)))->id("g-ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT");
    $group->textarea("ORDER_DELIVERED_NOTPAID_EMAIL")->label(t(basket_plus::formatLabel(ORDER_DELIVERED_NOTPAID_EMAIL)))->id("g-ORDER_DELIVERED_NOTPAID_EMAIL");
    $group->input("ORDER_DELAYED_EMAIL_SUBJECT")->label(t(basket_plus::formatLabel(ORDER_DELAYED_EMAIL_SUBJECT)))->id("g-ORDER_DELAYED_EMAIL_SUBJECT");
    $group->textarea("ORDER_DELAYED_EMAIL")->label(t(basket_plus::formatLabel(ORDER_DELAYED_EMAIL)))->id("g-ORDER_DELAYED_EMAIL");
    $group->input("ORDER_CANCELLED_EMAIL_SUBJECT")->label(t(basket_plus::formatLabel(ORDER_CANCELLED_EMAIL_SUBJECT)))->id("g-ORDER_CANCELLED_EMAIL_SUBJECT");
    $group->textarea("ORDER_CANCELLED_EMAIL")->label(t(basket_plus::formatLabel(ORDER_CANCELLED_EMAIL)))->id("g-ORDER_CANCELLED_EMAIL");
    $group->textarea("ORDER_EMAIL_CLOSING")->label(t(basket_plus::formatLabel(ORDER_EMAIL_CLOSING)))->id("g-ORDER_EMAIL_CLOSING");
    $group->textarea("ORDER_EMAIL_FOOTER")->label(t(basket_plus::formatLabel(ORDER_EMAIL_FOOTER)))->id("g-ORDER_EMAIL_FOOTER");
    $group->textarea("ORDER_EMAIL_LOGO")->label(t(basket_plus::formatLabel(ORDER_EMAIL_LOGO)))->id("g-ORDER_EMAIL_LOGO");
    $group->submit("")->value(t("Save"));
    return $form;
  }

/*
 * USED IN: basket admin functions: templates
 * DESCRIPTION: Fills the basket templates form  
 */
  static function populateTemplateForm($form){
	//rebuild the variable cache to ensure the use of the latest DB values
		basket_plus::reset_var_cache();
    $form->configure->PAYMENT_DETAILS->value(basket_plus::getBasketVar(PAYMENT_DETAILS));
    $form->configure->CUSTOMER_DETAILS->value(basket_plus::getBasketVar(CUSTOMER_DETAILS));
    $form->configure->ORDER_COMPLETE_PAGE->value(basket_plus::getBasketVar(ORDER_COMPLETE_PAGE));
    $form->configure->ORDER_PAID_COMPLETE_PAGE->value(basket_plus::getBasketVar(ORDER_PAID_COMPLETE_PAGE));
    $form->configure->ORDER_THANKYOU->value(basket_plus::getBasketVar(ORDER_THANKYOU));
    $form->configure->ORDER_DETAILS->value(basket_plus::getBasketVar(ORDER_DETAILS));
    $form->configure->EMAIL_TEMPLATE_STYLE->value(basket_plus::getBasketVar(EMAIL_TEMPLATE_STYLE));
    $form->configure->ORDER_COMPLETE_EMAIL_SUBJECT->value(basket_plus::getBasketVar(ORDER_COMPLETE_EMAIL_SUBJECT));
    $form->configure->ORDER_PAID_EMAIL_SUBJECT->value(basket_plus::getBasketVar(ORDER_PAID_EMAIL_SUBJECT));
    $form->configure->ORDER_PAID_EMAIL->value(basket_plus::getBasketVar(ORDER_PAID_EMAIL));
    $form->configure->ORDER_PAID_DELIVERED_EMAIL_SUBJECT->value(basket_plus::getBasketVar(ORDER_PAID_DELIVERED_EMAIL_SUBJECT));
    $form->configure->ORDER_PAID_DELIVERED_EMAIL->value(basket_plus::getBasketVar(ORDER_PAID_DELIVERED_EMAIL));
    $form->configure->ORDER_LATE_PAYMENT_EMAIL_SUBJECT->value(basket_plus::getBasketVar(ORDER_LATE_PAYMENT_EMAIL_SUBJECT));
    $form->configure->ORDER_LATE_PAYMENT_EMAIL->value(basket_plus::getBasketVar(ORDER_LATE_PAYMENT_EMAIL));
    $form->configure->ORDER_DELIVERED_EMAIL_SUBJECT->value(basket_plus::getBasketVar(ORDER_DELIVERED_EMAIL_SUBJECT));
    $form->configure->ORDER_DELIVERED_EMAIL->value(basket_plus::getBasketVar(ORDER_DELIVERED_EMAIL));
    $form->configure->ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT->value(basket_plus::getBasketVar(ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT));
    $form->configure->ORDER_DELIVERED_NOTPAID_EMAIL->value(basket_plus::getBasketVar(ORDER_DELIVERED_NOTPAID_EMAIL));
    $form->configure->ORDER_DELAYED_EMAIL_SUBJECT->value(basket_plus::getBasketVar(ORDER_DELAYED_EMAIL_SUBJECT));
    $form->configure->ORDER_DELAYED_EMAIL->value(basket_plus::getBasketVar(ORDER_DELAYED_EMAIL));
    $form->configure->ORDER_CANCELLED_EMAIL_SUBJECT->value(basket_plus::getBasketVar(ORDER_CANCELLED_EMAIL_SUBJECT));
    $form->configure->ORDER_CANCELLED_EMAIL->value(basket_plus::getBasketVar(ORDER_CANCELLED_EMAIL));
    $form->configure->ORDER_EMAIL_CLOSING->value(basket_plus::getBasketVar(ORDER_EMAIL_CLOSING));
    $form->configure->ORDER_EMAIL_FOOTER->value(basket_plus::getBasketVar(ORDER_EMAIL_FOOTER));
    $form->configure->ORDER_EMAIL_LOGO->value(basket_plus::getBasketVar(ORDER_EMAIL_LOGO));
  }

/*
 * USED IN: basket admin functions: templates
 * DESCRIPTION: Saves the values of the basket templates form  
 */
  static function extractTemplateForm($form){
    basket_plus::setBasketVar(PAYMENT_DETAILS,$form->configure->PAYMENT_DETAILS->value);
    basket_plus::setBasketVar(CUSTOMER_DETAILS,$form->configure->CUSTOMER_DETAILS->value);
    basket_plus::setBasketVar(ORDER_COMPLETE_PAGE,$form->configure->ORDER_COMPLETE_PAGE->value);
    basket_plus::setBasketVar(ORDER_PAID_COMPLETE_PAGE,$form->configure->ORDER_PAID_COMPLETE_PAGE->value);
    basket_plus::setBasketVar(ORDER_THANKYOU,$form->configure->ORDER_THANKYOU->value);
    basket_plus::setBasketVar(ORDER_DETAILS,$form->configure->ORDER_DETAILS->value);
    basket_plus::setBasketVar(EMAIL_TEMPLATE_STYLE,$form->configure->EMAIL_TEMPLATE_STYLE->value);
    basket_plus::setBasketVar(ORDER_COMPLETE_EMAIL_SUBJECT,$form->configure->ORDER_COMPLETE_EMAIL_SUBJECT->value);
    basket_plus::setBasketVar(ORDER_PAID_EMAIL_SUBJECT,$form->configure->ORDER_PAID_EMAIL_SUBJECT->value);
    basket_plus::setBasketVar(ORDER_PAID_EMAIL,$form->configure->ORDER_PAID_EMAIL->value);
    basket_plus::setBasketVar(ORDER_PAID_DELIVERED_EMAIL_SUBJECT,$form->configure->ORDER_PAID_DELIVERED_EMAIL_SUBJECT->value);
    basket_plus::setBasketVar(ORDER_PAID_DELIVERED_EMAIL,$form->configure->ORDER_PAID_DELIVERED_EMAIL->value);
    basket_plus::setBasketVar(ORDER_LATE_PAYMENT_EMAIL_SUBJECT,$form->configure->ORDER_LATE_PAYMENT_EMAIL_SUBJECT->value);
    basket_plus::setBasketVar(ORDER_LATE_PAYMENT_EMAIL,$form->configure->ORDER_LATE_PAYMENT_EMAIL->value);
    basket_plus::setBasketVar(ORDER_DELIVERED_EMAIL_SUBJECT,$form->configure->ORDER_DELIVERED_EMAIL_SUBJECT->value);
    basket_plus::setBasketVar(ORDER_DELIVERED_EMAIL,$form->configure->ORDER_DELIVERED_EMAIL->value);
    basket_plus::setBasketVar(ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT,$form->configure->ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT->value);
    basket_plus::setBasketVar(ORDER_DELIVERED_NOTPAID_EMAIL,$form->configure->ORDER_DELIVERED_NOTPAID_EMAIL->value);
    basket_plus::setBasketVar(ORDER_DELAYED_EMAIL_SUBJECT,$form->configure->ORDER_DELAYED_EMAIL_SUBJECT->value);
    basket_plus::setBasketVar(ORDER_DELAYED_EMAIL,$form->configure->ORDER_DELAYED_EMAIL->value);
    basket_plus::setBasketVar(ORDER_CANCELLED_EMAIL_SUBJECT,$form->configure->ORDER_CANCELLED_EMAIL_SUBJECT->value);
    basket_plus::setBasketVar(ORDER_CANCELLED_EMAIL,$form->configure->ORDER_CANCELLED_EMAIL->value);
    basket_plus::setBasketVar(ORDER_EMAIL_CLOSING,$form->configure->ORDER_EMAIL_CLOSING->value);
    basket_plus::setBasketVar(ORDER_EMAIL_FOOTER,$form->configure->ORDER_EMAIL_FOOTER->value);
    basket_plus::setBasketVar(ORDER_EMAIL_LOGO,$form->configure->ORDER_EMAIL_LOGO->value);
  }

/* ============================================================
 * Basket order functions
 * ============================================================ */
/*
 * USED IN: order process
 * DESCRIPTION: Creates an order after the customer has confirmed the order.
 *						  Create and saves a customer record, create order confirmation e-mail text
 * 							for the customer and internal order e-mail text for order handling and saves them in the order.
 * 
 * @TODO: create order lines for each basket item and link them to the order.
 */
  static function createOrder($basket, $payment_method){
		//create a new order and set a temp value
		$order = ORM::factory("bp_order");
    $order->text = t("processing");
 
		//cost calculation
    $product_cost = $basket->product_cost();
    $postage = $basket->postage_cost();
		$pickup = $basket->pickup;
    if ($pickup) { //no postage cost
      $total_cost = $product_cost;
    }
    else {
      $total_cost = ($product_cost + $postage);
    }
		$delivery_method = basket_plus::getDeliveryMethod($pickup, $postage);
		//create order and customer		
		$customer = basket_plus::createCustomer($basket);
    $full_name = basket_plus::createFullName($basket);

		//fill the order record and save it
		$order->name = $full_name;
		$order->email = $basket->email;
		$order->cost = $total_cost;
		$order->payment_method = $payment_method;
		$order->delivery_method = $delivery_method;
    $order->customerid = $customer->id;
    $order->save();

//----------------------------------------------------------------
// Create  the order confirmation e-mail text to the customer
		if ($basket->paypal){
			$email_template_name = "order_paypal";
		}
		else{
			$email_template_name = "order";
		}
		// get order Email template
		$email_template = ORM::factory("bp_email_template")->where("name", "=", $email_template_name)->find();
		$body = $email_template->email_html;
    $order_number = basket_plus::getBasketVar(ORDER_PREFIX).$order->id;
		$order_datetime = date(basket_plus::getBasketVar(DATE_TIME_FORMAT));
		//-------------
		// The mail has many parts, each containing variables that need to be replaced.
		// Replaced variables may contain other variables, so the replacement takes more than one step
		//-------------
	/** 
	* first replace: all variables in the order Email template 
		%meta_tag: SYSTEM VAR: <meta> tag; workaround for Kohana limitations
		%email_template_style: VAR: CSS style definitions for e-mails
		%order_email_title: fixed email title
		%order_email_header: fixed email header
		%order_email_logo: VAR: path to the logo in the email
		%order_email_footer: VAR: optional text in the email footer
		%order_thankyou: VAR: default 'thank you' message
		%webshop_details: VAR: webshop name, adddress and contact
		%order_datetime: calc order date/time in user chosen format
		%customer_details: VAR: the customer name and address
		%payment_details: VAR: the payment details
		%cost_details: function: all amounts leading to total amount
		%delivery_method: function: how the order is delivered
		%order_lines: function: the actual order
		*/
		$body = basket_plus::replaceStrings($body,Array(
			META_TAG => basket_plus::getBasketVar(META_TAG),
			EMAIL_TEMPLATE_STYLE => basket_plus::getBasketVar(EMAIL_TEMPLATE_STYLE),
			"order_email_title" => t("Order confirmation"),
			"order_email_header" => t("Order confirmation"),
			ORDER_EMAIL_LOGO => basket_plus::getBasketVar(ORDER_EMAIL_LOGO),
			ORDER_THANKYOU => basket_plus::getBasketVar(ORDER_THANKYOU),
			ORDER_EMAIL_FOOTER => basket_plus::getBasketVar(ORDER_EMAIL_FOOTER),
			WEBSHOP_DETAILS => basket_plus::getBasketVar(WEBSHOP_DETAILS),
			"order_datetime" => $order_datetime,
			CUSTOMER_DETAILS => basket_plus::getBasketVar(CUSTOMER_DETAILS),
			PAYMENT_DETAILS => basket_plus::getBasketVar(PAYMENT_DETAILS),
			ORDER_DETAILS => basket_plus::getBasketVar(ORDER_DETAILS),
			"cost_details" => basket_plus::getCostDetailsHtml($postage, $product_cost, $pickup),//$basket),
			"delivery_method" => basket_plus::getDeliveryMethodHtml($delivery_method,false),
			"order_lines" => basket_plus::getOrderLinesHtml($basket)));

	/** 
	 * second replace: all remaining variables in the resulting body variable 
		%email_order: VAR
		%email_from: VAR
		%email_contact: VAR
		%order_number: calc: prefix+id
		%total_cost: from order: total order amount incl delivery
		%order_bank_account: VAR
		%order_bank_account_owner: VAR
		%order_email_closing: VAR: email closing message
	 */
		$body = basket_plus::replaceStrings($body,Array(
			EMAIL_ORDER => basket_plus::getBasketVar(EMAIL_ORDER),
			EMAIL_FROM => basket_plus::getBasketVar(EMAIL_FROM),
			EMAIL_CONTACT => basket_plus::getBasketVar(EMAIL_CONTACT),
			"order_number" => $order_number,
			"total_cost" => basket_plus::formatMoneyForWeb($total_cost),
			ORDER_BANK_ACCOUNT_OWNER => basket_plus::getBasketVar(ORDER_BANK_ACCOUNT_OWNER),
			ORDER_BANK_ACCOUNT => basket_plus::getBasketVar(ORDER_BANK_ACCOUNT),
			ORDER_EMAIL_CLOSING => basket_plus::getBasketVar(ORDER_EMAIL_CLOSING),
"GT" => "<", //TEMP WORKAROUND FOR Kohana limitations (save of certain tags is not working)
			"full_name" => $full_name,
			"address" => basket_plus::getAddressHtml($basket),
			"phone" => $basket->phone,
			"email" => $basket->email,
			"comments" => basket_plus::getOrderCommentsHtml($basket)));

	/** third replace: all remaining variables in the resulting body variable 
	 * %website: VAR: website URL
	 * %webshop_XX: VARS with webshop info
	 */
		$body = basket_plus::replaceStrings($body,Array(
			WEBSITE => basket_plus::getBasketVar(WEBSITE),
			WEBSHOP_ADDRESS => basket_plus::getBasketVar(WEBSHOP_ADDRESS),
			WEBSHOP_POSTALCODE => basket_plus::getBasketVar(WEBSHOP_POSTALCODE),
			WEBSHOP_CITY => basket_plus::getBasketVar(WEBSHOP_CITY),
			WEBSHOP_PHONE => basket_plus::getBasketVar(WEBSHOP_PHONE),
			WEBSHOP_OWNER => basket_plus::getBasketVar(WEBSHOP_OWNER),
			WEBSHOP => basket_plus::getBasketVar(WEBSHOP))); 

		//convert to valid html and save in the order record
		$order->text = htmlspecialchars_decode($body);
		$order->save();

	//----------------------------------------------------------------
	// Create the internal order e-mail
		$email_template = ORM::factory("bp_email_template")->where("name", "=", $email_template_name)->find();
		$body = $email_template->email_text;
		$title = t("Internal Order confirmation");
		
	//	first replace: all variables in the order Email template 
		$body = basket_plus::replaceStrings($body,Array(
			"order_number" => $order_number,
			"total_cost" => basket_plus::formatMoneyForWeb($total_cost),
			"order_datetime" => $order_datetime,
			CUSTOMER_DETAILS => basket_plus::html2txt(basket_plus::getBasketVar(CUSTOMER_DETAILS)),
			"delivery_method" => basket_plus::html2txt(basket_plus::getDeliveryMethodHtml($delivery_method,false)),
			"order_lines" => basket_plus::getOrderDetailsText($basket)));
	//	second replace: all remaining variables in the resulting body variable 
		$body = basket_plus::replaceStrings($body,Array(
			EMAIL_ORDER => basket_plus::getBasketVar(EMAIL_ORDER),
			EMAIL_FROM => basket_plus::getBasketVar(EMAIL_FROM),
			"order_email_title" => $title,
			"order_email_header" => $title,
			"full_name" => $full_name,
			"address" => basket_plus::html2txt(basket_plus::getAddressHtml($basket)),
			"phone" => $basket->phone,
			"email" => $basket->email,
			"comments" => basket_plus::html2txt(str_replace(array("<br/>","<br>"),"\n",basket_plus::getOrderCommentsHtml($basket)))));
		$body = str_replace("nbsp;"," ",$body);
	//put the internal mail text and order status in the order
		$order->internal_text = $body;
		$order->status = Bp_Order_Model::WAITING_PAYMENT;
	//save the order record
		$order->save();
    return $order;
  }

/*
 * USED IN: order confirmation e-mail
 * DESCRIPTION: creates a html cost details block
 */
	static function getCostDetailsHtml($postage, $cost, $pickup) {
		$cost_details = "<br>".t(basket_plus_label::TEXT_ORDER_AMOUNT).": <strong>".basket_plus::formatMoneyForWeb($cost)."</strong><br />";
		if ($pickup && $postage > 0){
			$cost_details .= t(basket_plus_label::TEXT_PACKAGING_POSTING).": <strong>".basket_plus::formatMoneyForWeb(0)."</strong>";
		}
    elseif (!$pickup && $postage > 0){
			$cost_details .= t(basket_plus_label::TEXT_PACKAGING_POSTING).": <strong>".basket_plus::formatMoneyForWeb($postage)."</strong>";
			$cost = $cost + $postage;
		}
    else {
      $cost_details .= t(basket_plus_label::TEXT_FREE_SHIPMENT);
    }
		$cost_details .= "<br>".t(basket_plus_label::TEXT_TOTAL_AMOUNT).": <strong>".basket_plus::formatMoneyForWeb($cost)."</strong><br>";
		return $cost_details;
	}

/*
 * USED IN: order confirmation e-mail, view 'confirm_order.html'
 * DESCRIPTION: creates a html delivery method block, with the label formatted strong or the delivery method
 */
	static function getDeliveryMethodHtml($delivery_method,$label_strong) {
		if ($label_strong){
			$method_html = "<strong>".t(basket_plus_label::TEXT_DELIVERY_METHOD).": </strong>";
		}else{
			$method_html = t(basket_plus_label::TEXT_DELIVERY_METHOD).": <strong>";
		}
		//mail 
		if ($delivery_method == Bp_Order_Model::DELIVERY_MAIL){
      $method_html .= t(basket_plus_label::TEXT_DELIVERY_MAIL);
    }
    // posting by e-mail
    elseif ($delivery_method == Bp_Order_Model::DELIVERY_EMAIL){
      $method_html .= t(basket_plus_label::TEXT_DELIVERY_EMAIL);
		}
    // pickup 
    elseif ($delivery_method == Bp_Order_Model::DELIVERY_PICKUP){
      $method_html .= t(basket_plus_label::TEXT_DELIVERY_PICKUP)." ".basket_plus::getPickupLocation();
    }
    // posting unknown
    else {
      $method_html .= t("Unknown delivery method");
    }
		if (!$label_strong){
			$method_html .= "</strong>";
		}
		$method_html .= "<br>";
		
		return $method_html;
	}

/*
 * USED IN: order confirmation e-mail, view 'confirm_order.html'
 * DESCRIPTION: creates a html payment method block, with the label formatted strong or the payment method
 */
	static function getPaymentMethodHtml($payment_method,$label_strong) {
		if ($label_strong){
			$method_html = "<strong>".t(basket_plus_label::TEXT_PAYMENT_METHOD).": </strong>";
		}else{
			$method_html = t(basket_plus_label::TEXT_PAYMENT_METHOD).": <strong>";
		}
		// offline 
		if ($payment_method == Bp_Order_Model::PAYMENT_OFFLINE){
      $method_html .= t(basket_plus_label::TEXT_PAYMENT_OFFLINE);
    }
    // paypal
    elseif ($payment_method == Bp_Order_Model::PAYMENT_PAYPAL){
      $method_html .= t(basket_plus_label::TEXT_PAYMENT_PAYPAL);
		}
    // payment unknown
    else {
      $method_html .= t("Unknown payment method");
    }
		if (!$label_strong){
			$method_html .= "</strong>";
		}
		$method_html .= "<br>";
		
		return $method_html;
	}

/*
 * USED IN: getOrderCommentsHtml (for use in order confirmation e-mail)
 * DESCRIPTION: gets order reference label from user basket or sets a default label
 */
	static function getOrderRef($refnr){

		$is_group = basket_plus::getUserIsGroup();
		if ($is_group){
			$user = identity::active_user();
			//get user_basket setting
			$user_basket = ORM::factory("bp_user_basket")->where("id", "=", $user->id)->find();
			if ($refnr == 1){
				$order_ref = $user_basket->extra_order_info_lbl;
			}
			elseif ($refnr == 2){
				$order_ref = $user_basket->extra_order_info_lbl2;
			}
		}
		if ($order_ref == ""){
			if ($refnr == 1){
				$order_ref = t("Order reference");
			}
			elseif ($refnr == 2){
				$order_ref = t("Order reference 2");
			}
		}
		return $order_ref;
	}

/*
 * USED IN: order confirmation e-mail
 * DESCRIPTION: creates a html order reference and order comments block
 */
	static function getOrderCommentsHtml($basket) {
		$order_comments = "";
		if ($basket->order_ref1 <> "") {
			$order_ref_lbl = t(basket_plus::getOrderRef(1));
			$order_comments = "<br>";
			if ($basket->order_ref2 <> "") {
				$order_ref_lbl .= "/".t(basket_plus::getOrderRef(2));
				$order_comments .= $order_ref_lbl.":&nbsp;<strong>".$basket->order_ref1."/".$basket->order_ref2."</strong>";
			}else{
				$order_comments .= $order_ref_lbl.":&nbsp;<strong>".$basket->order_ref1."</strong>";
			}
		}
		if ($basket->comments <> "") {
      $order_comments .= "<br>";
			$order_comments .= t(basket_plus_label::TEXT_MAIL_ORDER_COMMENT).":&nbsp;<strong>".$basket->comments."</strong>";
		}
		return $order_comments;
	}
		
/*
 * USED IN: order confirmation e-mail
 * DESCRIPTION: creates a html address block
 */
	static function getAddressHtml($basket) {
		$address = basket_plus::getBasketVar(ADDRESS_FORMAT);
		$address = basket_plus::replaceStrings($address,Array(
			"house" => $basket->house,
			"street" => $basket->street,
			"postalcode" => $basket->postalcode,
			"suburb" => $basket->suburb,
			"town" => $basket->town,
			"province" => $basket->province,
			"country" => $basket->country));
		return $address;	
	}	
	
/*
 * USED IN: order confirmation e-mail
 * DESCRIPTION: creates a html table of the order items with a header row
 * REMARKS: contains a thumbnail of each item; as inline display doesn't work (due to authorisation model), 
 *					the thumbnails are replaced with attachments before sending the e-mail and must not be visible
 *					as broken images in the e-mail. Therefor, $max_thumb_size is set to 1.
 */
	static function getOrderLinesHtml($basket) {
		
		// TODO make heading labels configurable
    $website = basket_plus::getBasketVar(WEBSITE);
		$website_url = "http://".$website;

		//		$max_thumb_size = 60;
		// WORKAROUND: the inline images (created by function prepareHtml) in the mail aren't displayed correctly. Size '1' makes them invisible in the table.
		$max_thumb_size = 1;
		$body_details = "					<table width=&quot;100%&quot;>
																	<tr>          
																		<th style=&quot;text-align:left;&quot;>".t("Photo")."</th>
																		<th style=&quot;text-align:left;&quot;>".t("Product")."</th>
																		<th style=&quot;text-align:right;&quot;>".t("Quantity")."</th>
																		<th style=&quot;text-align:right;&quot;>".t("Price")."</th>
																	</tr>";
		// loop through all basket items
		// create a row for each item with thumbnail, product description, quantity and cost 
    foreach ($basket->contents as $basket_item){
      $item = $basket_item->getItem();
      $prod = ORM::factory("bp_product", $basket_item->product);
			// get real item cost
			$prod_cost = $basket_item->product_cost;
			$body_details .= "
																	<tr>
																		<td width=&quot;20%&quot; style=&quot;text-align:left;&quot;>
																			<div id=&quot;basketThumb&quot;>
																				".basket_plus::thumb_img_abs(array("style" => "display:none;"),$max_thumb_size,false,$item, $website_url)."&nbsp;".$item->title."
																			</div>
																		</td>
																		<td width=&quot;55%&quot; style=&quot;text-align:left;&quot;>".$prod->description."</td>
																		<td width=&quot;10%&quot; style=&quot;text-align:right;&quot;>".$basket_item->quantity."</td>
																		<td style=&quot;text-align:right;&quot;>".basket_plus::formatMoneyForWeb($prod_cost)."</td>
																	</tr>";
		}
		// close the table
		$body_details .= "
																</table>";
		return $body_details;
	}
	
/*
 * USED IN: order confirmation e-mail
 * DESCRIPTION: returns the delivery method as a code. See models/Order.
 */
	static function getDeliveryMethod($pickup, $postage) {
    // posting by mail
    if ($postage > 0 && !$pickup){
      $delivery_method = Bp_Order_Model::DELIVERY_MAIL;
    }
    // pickup
    elseif ($postage > 0 && $pickup){
      $delivery_method = Bp_Order_Model::DELIVERY_PICKUP;
    }
    // posting by e-mail
    else{
      $delivery_method = Bp_Order_Model::DELIVERY_EMAIL;
		}
		return $delivery_method;
	}
	
/*
 * USED IN: internal order confirmation e-mail
 * DESCRIPTION: creates a list of the order items with a header row
 * @TODO: create order lines for each basket item and link them to the order.
 */
	static function getOrderDetailsText($basket) {		
		$text = "
".t("Photo").";".t("Quantity").";".t("Product").";".t("Cost")."
";
    foreach ($basket->contents as $basket_item){
      $item = $basket_item->getItem();
      $prod = ORM::factory("bp_product", $basket_item->product);
      $text .= $item->title."; ".$basket_item->quantity."; ".$prod->description."; ".basket_plus::formatMoneyForMail($basket_item->quantity*$prod->cost)."
";
		return $text;
		}
	
	}

	/* ============================================================
 * Basket e-mail functions
 * ============================================================ */
/*
 * USED IN: order e-mail
 * DESCRIPTION: Sends the order confirmation e-mail to the customer (Html) and to the order email address
 */
  static function send_order($order){
	// order confirmation mail to customer
    $to = $order->email;
		$from = basket_plus::getBasketVar(EMAIL_FROM);
		$subject = basket_plus::replaceStringsAll(basket_plus::getBasketVar(ORDER_COMPLETE_EMAIL_SUBJECT),$order);
		$final_msg = basket_plus::preparehtmlmail($order->text,$from); // give a function your html

		mail($to, $subject, $final_msg['multipart'], $final_msg['headers']); 

		//order mail to internal order handling (text only)
    $to = basket_plus::getBasketVar(EMAIL_ORDER);
		$from = "From: ".$from;
    $subject = t("Order number")." ".basket_plus::getBasketVar(ORDER_PREFIX).$order->id." ".t("from")." ".$order->name;
		$body = $order->internal_text;
		
		//send mail
		mail($to, $subject, $body, $from);
	}

/*
 * USED IN: order update e-mails
 * DESCRIPTION: Generic function to send an order update mail
 */
	static function send_order_update($order, $subject, $body_text){
  // order update mail to customer 
    $to = $order->email;
		$from = basket_plus::getBasketVar(EMAIL_FROM);
		$email_template_name = "order_update";
		// get order Email template
		$email_template = ORM::factory("bp_email_template")->where("name", "=", $email_template_name)->find();
		$body = $email_template->email_html;
		$subject = basket_plus::replaceStringsAll(basket_plus::getBasketVar($subject),$order);
	//first replace variables
		$body = basket_plus::replaceStrings($body, Array(
			META_TAG => basket_plus::getBasketVar(META_TAG),
			EMAIL_TEMPLATE_STYLE => basket_plus::getBasketVar(EMAIL_TEMPLATE_STYLE),
			"order_email_title" => t("Order Update"),
			"order_email_header" => t("Order Update"),
			ORDER_EMAIL_LOGO => basket_plus::getBasketVar(ORDER_EMAIL_LOGO),
			ORDER_EMAIL_FOOTER => basket_plus::getBasketVar(ORDER_EMAIL_FOOTER),
			"body_text" => basket_plus::getBasketVar($body_text),
			WEBSHOP_DETAILS => basket_plus::getBasketVar(WEBSHOP_DETAILS)));
	//second replace variables
		$body = basket_plus::replaceStrings($body,Array(
			ORDER_EMAIL_CLOSING => basket_plus::getBasketVar(ORDER_EMAIL_CLOSING),
"GT" => "<", //TEMP WORKAROUND FOR Kohana limitations (save of certain tags is not working)
			ORDER_BANK_ACCOUNT_OWNER => basket_plus::getBasketVar(ORDER_BANK_ACCOUNT_OWNER),
			ORDER_BANK_ACCOUNT => basket_plus::getBasketVar(ORDER_BANK_ACCOUNT),
			"delivery_method" => basket_plus::deliveredMethodText($order),
			WEBSITE => basket_plus::getBasketVar(WEBSITE),
			WEBSHOP_ADDRESS => basket_plus::getBasketVar(WEBSHOP_ADDRESS),
			WEBSHOP_POSTALCODE => basket_plus::getBasketVar(WEBSHOP_POSTALCODE),
			WEBSHOP_CITY => basket_plus::getBasketVar(WEBSHOP_CITY),
			WEBSHOP_PHONE => basket_plus::getBasketVar(WEBSHOP_PHONE),
			WEBSHOP_OWNER => basket_plus::getBasketVar(WEBSHOP_OWNER),
			EMAIL_CONTACT => basket_plus::getBasketVar(EMAIL_CONTACT),
			WEBSHOP => basket_plus::getBasketVar(WEBSHOP))); 
	//third replace variables
		$body = basket_plus::replaceStringsAll($body,$order);
		$final_msg = basket_plus::preparehtmlmail($body,$from);
	//send mail
    mail($to, $subject, $final_msg['multipart'], $final_msg['headers']); 
  }

/*
 * USED IN: order update e-mails
 * DESCRIPTION: Send a copy of the the order confirmation e-mail to the customer (Html)
 */
	static function send_order_copy($order){
    $to = $order->email;
		$from = basket_plus::getBasketVar(EMAIL_FROM);
		//add text 'COPY' to the subject
		$subject = basket_plus::replaceStringsAll(basket_plus::getBasketVar(ORDER_COMPLETE_EMAIL_SUBJECT),$order)." (".t("COPY").")";
		$final_msg = basket_plus::preparehtmlmail($order->text,$from); // give a function your html

		mail($to, $subject, $final_msg['multipart'], $final_msg['headers']); 
  }

/*
 * USED IN: order update e-mails
 * DESCRIPTION: Send the payment confirmation e-mail to the customer (Html)
 */
	static function send_payment_confirmation($order){
		if ($order->status == Bp_Order_Model::PAYMENT_CONFIRMED) {
			basket_plus::send_order_update($order,ORDER_PAID_EMAIL_SUBJECT,ORDER_PAID_EMAIL);
		}
		//order paid and delivered earlier
		elseif ($order->status == Bp_Order_Model::DELIVERED) {
			basket_plus::send_order_update($order,ORDER_PAID_DELIVERED_EMAIL_SUBJECT,ORDER_PAID_DELIVERED_EMAIL);
		}
  }

/*
 * USED IN: order update e-mails
 * DESCRIPTION: Send a payment reminder e-mail to the customer (Html)
 */
  static function send_payment_reminder($order){
		basket_plus::send_order_update($order,ORDER_LATE_PAYMENT_EMAIL_SUBJECT,ORDER_LATE_PAYMENT_EMAIL);
  }

/*
 * USED IN: order update e-mails
 * DESCRIPTION: Send the delivery confirmation e-mail to the customer (Html)
 */
  static function send_delivery_confirmation($order){
		// order delivered 
		if ($order->status == Bp_Order_Model::DELIVERED) {
			basket_plus::send_order_update($order,ORDER_DELIVERED_EMAIL_SUBJECT,ORDER_DELIVERED_EMAIL);
		}
		//order paid and delivered earlier
		elseif ($order->status == Bp_Order_Model::DELIVERED_NOTPAID) {
			basket_plus::send_order_update($order,ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT,ORDER_DELIVERED_NOTPAID_EMAIL);
		}
  }

/*
 * USED IN: order update e-mails
 * DESCRIPTION: Send the delay notification e-mail to the customer (Html)
 */
  static function send_delay_notification($order){
		basket_plus::send_order_update($order,ORDER_DELAYED_EMAIL_SUBJECT,ORDER_DELAYED_EMAIL);
  }

/*
 * USED IN: order update e-mails
 * DESCRIPTION: Send the cancellation confirmation e-mail to the customer (Html)
 */
  static function send_cancellation_confirmation($order){
		basket_plus::send_order_update($order,ORDER_CANCELLED_EMAIL_SUBJECT,ORDER_CANCELLED_EMAIL);
  }

/*
 * USED IN: order update e-mails
 * DESCRIPTION: Support function to prepare the html for an e-mail with inline photos
 */
	static function preparehtmlmail($html,$from) {

		preg_match_all('~<img.*?src=.([\/.a-z0-9:_-]+).*?>~si',$html,$matches);
		$i = 0;
		$paths = array();

		foreach ($matches[1] as $img) {
			$img_old = $img;

			if(strpos($img, "http://") == false) {
				$uri = parse_url($img);
				$paths[$i]['path'] = $_SERVER['DOCUMENT_ROOT'].$uri['path'];
				$content_id = md5($img);
				$html = str_replace($img_old,'cid:'.$content_id,$html);
				$paths[$i++]['cid'] = $content_id;
			}
		}

		$boundary = "--".md5(uniqid(time()));
		$headers = "MIME-Version: 1.0\n";
		$headers .="Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
		$headers .= "From: ".$from."\r\n";
		$multipart = '';
		$multipart .= "--$boundary\n";
		$kod = 'utf-8';
		$multipart .= "Content-Type: text/html; charset=$kod\n";
		$multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";
		$multipart .= "$html\n\n";

		foreach ($paths as $path) {
			if(file_exists($path['path']))
				$fp = fopen($path['path'],"r");
				if (!$fp)  {
					return false;
				}

			$imagetype = strtolower(substr(strrchr($path['path'], '.' ),1));
			$file = fread($fp, filesize($path['path']));
			fclose($fp);

			$message_part = "";

			switch ($imagetype) {
				case 'png':
				case 'PNG':
							$message_part .= "Content-Type: image/png";
							break;
				case 'jpg':
				case 'jpeg':
				case 'JPG':
				case 'JPEG':
							$message_part .= "Content-Type: image/jpeg";
							break;
				case 'gif':
				case 'GIF':
							$message_part .= "Content-Type: image/gif";
							break;
			}

			$message_part .= "; file_name = \"$path\"\n";
			$message_part .= 'Content-ID: <'.$path['cid'].">\n";
			$message_part .= "Content-Transfer-Encoding: base64\n";
			$message_part .= "Content-Disposition: inline; filename = \"".basename($path['path'])."\"\n\n";
			$message_part .= chunk_split(base64_encode($file))."\n";
			$multipart .= "--$boundary\n".$message_part."\n";

		}

		$multipart .= "--$boundary--\n";
		return array('multipart' => $multipart, 'headers' => $headers);  
	}

} 
