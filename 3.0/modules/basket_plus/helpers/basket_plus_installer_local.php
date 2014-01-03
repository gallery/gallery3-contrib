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

class basket_plus_installer_local{
/*
 * USED IN: basket_plus_installer
 * DESCRIPTION: Set initial basket product settings 
 */
  static function initBasketProducts($language,$isReset){
		$db = Database::instance();
		//if it's a reset request, empty the tables
		if ($isReset) {
			$db->query("DELETE FROM {bp_postage_bands}");
			$db->query("DELETE FROM {bp_products}");
			$db->query("DELETE FROM {bp_product_overrides}");
			$db->query("DELETE FROM {bp_item_products}");
		}
		//Add postage_bands if the table is empty
		$postage_band = ORM::factory("bp_postage_band")->where("id", "=", 1)->find();
		if (!$postage_band->loaded()) {
	//==========================
	// SETTINGS FOR ENGLISH
			if ($language == "en_US" or $language == "en_UK"){
				// name, fixed cost, per item cost, is download 
				bp_postage_band::create("No posting cost",0,0,false);
				bp_postage_band::create("Default posting cost",2,0,false);
				bp_postage_band::create("Posting by e-mail (free)",0,0,true);
			}
	//==========================
	// SETTINGS FOR DUTCH		
			elseif ($language == "nl_NL"){
				// name, fixed cost, per item cost, is download 
				bp_postage_band::create("Geen verzendkosten",0,0,false);
				bp_postage_band::create("Standaard verzendkosten",2,0,false);
				bp_postage_band::create("Verzending via e-mail (gratis)",0,0,true);
			}
		}
		//Add products if the table is empty
		$product = ORM::factory("bp_product")->where("id", "=", 1)->find();
		if (!$product->loaded()) {
	//==========================
	// SETTINGS FOR ENGLISH
			if ($language == "en_US" or $language == "en_UK"){
				//get posting band id for mail
				$postage = ORM::factory("bp_postage_band")->where("name","=", "Default posting cost")->find();
				// name, cost, descr, postageband id 
				bp_product::create("4x6",6,"Print 4x6 inch glossy",$postage->id);
				bp_product::create("8x10",9,"Print 8x10 inch glossy",$postage->id);
				//get posting band id for e-mail
				$postage = ORM::factory("bp_postage_band")->where("name","=", "Posting by e-mail (free)")->find();
				bp_product::create("Original",10,"Original high resolution photo file",$postage->id);
			}
	//==========================
	// SETTINGS FOR DUTCH		
			elseif ($language == "nl_NL"){
				//get posting band id for mail
				$postage = ORM::factory("bp_postage_band")->where("name","=", "Standaard verzendkosten")->find();
				// name, cost, descr, postageband id 
				bp_product::create("13x18",6,"Afdruk 13x18 cm glanzend",$postage->id);
				bp_product::create("20x30",9,"Afdruk 20x30 cm glanzend",$postage->id);
				//get posting band id for e-mail
				$postage = ORM::factory("bp_postage_band")->where("name","=", "Verzending via e-mail (gratis)")->find();
				bp_product::create("Origineel",10,"Originele fotobestand in hoge resolutie",$postage->id);
			}
		}	
	}
	
/*
 * USED IN: basket_plus_installer
 * DESCRIPTION: Set initial basket mail templates settings 
 */
  static function initBasketMailTemplates($language,$isReset){
		$db = Database::instance();
		//if it's a reset request, empty the table
		if ($isReset) {
			$db->query("DELETE FROM {bp_email_templates}");
		}
		//Add email_templates if the table is empty
		$email_template = ORM::factory("bp_email_template")->where("id", "=", 1)->find();
		if (!$email_template->loaded()) {
	//==========================
	// SETTINGS FOR ENGLISH
			if ($language == "en_US" or $language == "en_UK"){
				bp_email_template::create(
					"order",
					str_replace('$','"',"INTERNAL ORDER
Order number: %order_number
Ordered on: %order_datetime
Total amount: %total_cost
Delivery method: %delivery_method

Order for: 
%customer_details 
%comments 

%order_lines"),
					str_replace('$','"',"<h2>Order e-mail Html</h2><p>Insert Html formatted order template e-mail from directory $!install$ here</p>"));
				bp_email_template::create(
					"order_paypal",
					str_replace('$','"',"INTERNAL ORDER
Order number: %order_number
Ordered on: %order_datetime
Total amount: %total_cost
Delivery method: %delivery_method

Order for: 
%customer_details 
%comments 

%order_lines"),
					str_replace('$','"',"<h2>PayPal Order e-mail Html</h2><p>Insert Html formatted PayPal order template e-mail from directory $!install$ here</p>"));
				bp_email_template::create(
					"order_update",
					"Not used.",
					str_replace('$','"',"<h2>Order update e-mail Html</h2><p>Insert Html formatted order update template e-mail from directory $!install$ here</p>"));
			}
	//==========================
	// SETTINGS FOR DUTCH		
			elseif ($language == "nl_NL"){
				// name, email_text, email_html 
				bp_email_template::create(
					"order",
					str_replace('$','"',"INTERNE BESTELLING
Bestelnummer: %order_number
Besteld op: %order_datetime
Totaalbedrag: %total_cost
Bezorgwijze: %delivery_method

Bestemd voor: 
%customer_details 
%comments 

%order_lines"),
					str_replace('$','"',"<h2>bestelbevestigingsmail Html</h2><p>Voeg de Html bestelbevestigingsmail uit directory $!install$ hier toe</p>"));
				bp_email_template::create(
					"order_paypal",
					str_replace('$','"',"INTERNE BESTELLING
Bestelnummer: %order_number
Besteld op: %order_datetime
Totaalbedrag: %total_cost
Bezorgwijze: %delivery_method

Bestemd voor: 
%customer_details 
%comments 

%order_lines"),
					str_replace('$','"',"<h2>PayPal Bestelling e-mail Html</h2><p>Voeg de Html PayPal bestelling e-mail uit directory $!install$ hier toe</p>"));
				bp_email_template::create(
					"order_update",
					"Niet gebruikt.",
					str_replace('$','"',"<h2>Bestelling update e-mail Html</h2><p>Voeg de Html bestelling update e-mail uit directory $!install$ hier toe</p>"));
			}
		}
	}

/*
 * USED IN: basket_plus_installer
 * DESCRIPTION: Set initial basket settings (configuration and templates)
 */
  static function initBasketVars($language,$isReset){
		$db = Database::instance();
	     /* basket config settings */
		if ($isReset) {
			$db->query("DELETE FROM {vars} WHERE `module_name`='basket_plus'");
		}
	//==========================
	// SETTINGS FOR ENGLISH
		if ($language == "en_US" or $language == "en_UK"){
			basket_plus::setBasketVar(WEBSITE,"www.basketplusphoto.com");
			basket_plus::setBasketVar(WEBSHOP,"BasketPlusPhoto.com");
			basket_plus::setBasketVar(WEBSHOP_OWNER,"Photographer Mr X");
			basket_plus::setBasketVar(WEBSHOP_ADDRESS,"999, A-Street");
			basket_plus::setBasketVar(WEBSHOP_POSTALCODE,"99999");
			basket_plus::setBasketVar(WEBSHOP_CITY,"Photo City Photo Country");
			basket_plus::setBasketVar(WEBSHOP_PHONE,"555 1234");
			basket_plus::setBasketVar(WEBSHOP_DETAILS,"<strong>%webshop</strong><br />
<strong>%webshop_owner</strong><br />
%webshop_address<br />
%webshop_postalcode %webshop_city<br />
<br />
<strong>Contact</strong><br />
%email_contact<br />
%webshop_phone");
			basket_plus::setBasketVar(EMAIL_FROM,"BasketPlusPhoto.com <order@basketplusphoto.com>");
			basket_plus::setBasketVar(EMAIL_ORDER,"order@basketplusphoto.com, 999 A-Street Photo City Photo Country");
			basket_plus::setBasketVar(EMAIL_CONTACT,"contact@basketplusphoto.com");
			basket_plus::setBasketVar(CURRENCY,"USD");
			basket_plus::setBasketVar(DATE_TIME_FORMAT,"m/d/Y g:i A");
			basket_plus::setBasketVar(DECIMAL_SEPARATOR,".");
			basket_plus::setBasketVar(USE_SIDE_BAR_ONLY,"1");
			basket_plus::setBasketVar(ALLOW_PICKUP,"1");
			basket_plus::setBasketVar(IS_PICKUP_DEFAULT,"0");
			basket_plus::setBasketVar(PICKUP_LOCATION,"BasketPlusPhoto.com, Order desk");
			basket_plus::setBasketVar(USE_PAYPAL,"1");
			basket_plus::setBasketVar(PAYPAL_ACCOUNT,"paypal@basketplusphoto.com");
			basket_plus::setBasketVar(PAYPAL_TEST_MODE,"1");
			basket_plus::setBasketVar(ORDER_PREFIX,"2013-");
			basket_plus::setBasketVar(ORDER_BANK_ACCOUNT,"1234567890");
			basket_plus::setBasketVar(ORDER_BANK_ACCOUNT_OWNER,"Photographer");
			basket_plus::setBasketVar(PAYMENT_OPTIONS,str_replace('$','"','<p>Please fulfil the order payment by transferring the order amount into the bank account of %webshop.<br />
You will find payment instructions in the order confirmation e-mail.</p>
<br />
<h3>General Terms</h3>
<p>Our General Terms are available for review under <a href=$/downloads/General_Terms_2013.pdf$ target=$_blank$>this link</a>.</p>'));
			basket_plus::setBasketVar(ADDRESS_FORMAT,"%house %street %suburb<br />
%town %province %postalcode<br />
%country<br />");
			basket_plus::setBasketVar(USE_ADDRESS_SUBURB,"1");
			basket_plus::setBasketVar(USE_ADDRESS_PROVINCE,"1");
			basket_plus::setBasketVar(USE_ADDRESS_COUNTRY,"1");

		/* basket template settings */
		
			basket_plus::setBasketVar(PAYMENT_DETAILS,str_replace('$','"','<h3 class="h3">Payment</h3>
															<p style="font-family: verdana, sans-serif; font-size: 11px; color: rgb(80, 84, 80); line-height: 19px; border: none; margin-top: 5px; vertical-align: top;">
																Please fulfil the order payment by transferring the order amount into the bank account of %webshop.<br /><br />
																Total amount: <strong>%total_cost</strong><br />
																Bank account: <strong>%order_bank_account</strong><br />
																 in the name of: <strong>%order_bank_account_owner</strong><br />
																 reference: <strong>order number %order_number</strong><br />
															</p>'));
			basket_plus::setBasketVar(CUSTOMER_DETAILS," %full_name<br />
 %address<br />
 E-mail: %email<br />
 Phone: %phone<br />");
			basket_plus::setBasketVar(ORDER_COMPLETE_PAGE,"<p>Thank you for your order. Your order number is </b>%order_number</b>.</p>
<br />
<p>%webshop has sent a confirmation e-mail with the order details and payment instructions.</p>
<p>We will process your order when the payment has been received.</p>");
			basket_plus::setBasketVar(ORDER_PAID_COMPLETE_PAGE,"<p>Thank you for your order. Your order number is </b>%order_number</b>.</p>
<br />
<p>%webshop has sent a confirmation e-mail with the order details.</p>
<p>Your payment has been received. You will receive an e-mail when the order is delivered or ready for pickup.</p>");
			basket_plus::setBasketVar(ORDER_THANKYOU,"Dear %full_name,<br>
<br>
Thank you for your order. Below, you find the order details and payment instructions.<br>
Delivery is expected in 10 working days after receival of the payment by %webshop.<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_DETAILS,'<h3 class="h3">Order details</h3>
<p>
	Order number: <strong>%order_number</strong><br />
	Ordered on: <strong>%order_datetime</strong><br />
	<br>
	<strong>Order for</strong><br />
	%customer_details <!-- // replace with html -->
	%cost_details <!-- // replace with html -->
	%delivery_method <!-- // replace with html -->
	%comments <!-- // displayed only if the customer provided comments AND/OR ref/ref2 is filled\\-->															
</p>');
			basket_plus::setBasketVar(ORDER_COMPLETE_EMAIL_SUBJECT,"Your order %order_number with %webshop");
			basket_plus::setBasketVar(ORDER_PAID_EMAIL_SUBJECT,"Update of your order %order_number with %webshop: payment received");
			basket_plus::setBasketVar(ORDER_PAID_EMAIL,"Dear %name,<br>
<br>
%webshop received your payment of %total_cost and will process order %order_number. <br>
You will receive an e-mail when the order is delivered or ready for pickup. <br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_PAID_DELIVERED_EMAIL_SUBJECT,"Update of your order %order_number with %webshop: payment received");
			basket_plus::setBasketVar(ORDER_PAID_DELIVERED_EMAIL,"Dear %name,<br>
<br>
%webshop received your payment of %total_cost. The order has already been delivered.<br>
We'd like to thank you again for your order and wish you happy photo viewing!<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_LATE_PAYMENT_EMAIL_SUBJECT,"Update of your order %order_number with %webshop: waiting payment");
			basket_plus::setBasketVar(ORDER_LATE_PAYMENT_EMAIL,"Dear %name,<br>
<br>
Some time ago, you ordered at %webshop order %order_number.<br>
Our records show that the amount of <strong>%total_cost</strong> has not been fulfilled yet. Please note that we will only process your order when the payment has been received.<br>
<br>
You can transfer the amount due into the bank account <strong>%order_bank_account</strong> in the name of <strong>%order_bank_account_owner</strong> with reference to <strong>order number %order_number</strong>.<br>
<br>
If you have already sent this payment, please disregard this notice.<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_DELIVERED_EMAIL_SUBJECT,"Update of your order %order_number with %webshop: order shipped");
			basket_plus::setBasketVar(ORDER_DELIVERED_EMAIL,"Dear %name,<br>
<br>
Your order %order_number has been %delivery_method. <br>
We'd like to thank you again for your order and wish you happy photo viewing!<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT,"Update of your order %order_number with %webshop: order shipped without payment");
			basket_plus::setBasketVar(ORDER_DELIVERED_NOTPAID_EMAIL,"Dear %name,<br>
<br>
Your order %order_number has been %delivery_method, although the amount of %total_cost has not been fulfilled according to our records. <br>
<br>
You can transfer the amount due into the bank account <strong>%order_bank_account</strong> in the name of <strong>%order_bank_account_owner</strong> with reference to <strong>order number %order_number</strong>.<br>
<br>
If you have already sent this payment, please disregard this notice.<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_DELAYED_EMAIL_SUBJECT,"Update of your order %order_number with %webshop: order delayed");
			basket_plus::setBasketVar(ORDER_DELAYED_EMAIL,"Dear %name,<br>
<br>
Your order %order_number has been delayed due to unexpected circumstances.<br> 
We apologize for the delay and will do our very best to deliver you order as soon as possible.<br>");
			basket_plus::setBasketVar(ORDER_CANCELLED_EMAIL_SUBJECT,"Update of your order %order_number with %webshop: order cancelled");
			basket_plus::setBasketVar(ORDER_CANCELLED_EMAIL,"Dear %name,<br>
<br>
%Your order %order_number has been cancelled.<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_EMAIL_CLOSING,"If you have any questions or remarks, please contact us by e-mail on %email_order.<br>
<br>
With kind regards,<br>
%webshop - %webshop_owner");
		}
	//==========================
	// SETTINGS FOR DUTCH		
		elseif ($language == "nl_NL"){
			basket_plus::setBasketVar(WEBSITE,"www.basketplusphoto.com");
			basket_plus::setBasketVar(WEBSHOP,"BasketPlusPhoto.com");
			basket_plus::setBasketVar(WEBSHOP_OWNER,"Fotograaf Basket Plus");
			basket_plus::setBasketVar(WEBSHOP_ADDRESS,"Postlaan 1");
			basket_plus::setBasketVar(WEBSHOP_POSTALCODE,"8888 ZZ");
			basket_plus::setBasketVar(WEBSHOP_CITY,"Poststad");
			basket_plus::setBasketVar(WEBSHOP_PHONE,"06-1122 3344");
			basket_plus::setBasketVar(WEBSHOP_DETAILS,"<strong>%webshop</strong><br />
<strong>%webshop_owner</strong><br />
%webshop_address<br />
%webshop_postalcode %webshop_city<br />
<br />
<strong>Contact</strong><br />
%email_contact<br />
%webshop_phone");
			basket_plus::setBasketVar(EMAIL_FROM,"BasketPlusPhoto.com <bestelling@basketplusphoto.com>");
			basket_plus::setBasketVar(EMAIL_ORDER,"bestelling@basketplusphoto.com");
			basket_plus::setBasketVar(EMAIL_CONTACT,"contact@basketplusphoto.com");
			basket_plus::setBasketVar(USE_SIDE_BAR_ONLY,"1");
			basket_plus::setBasketVar(CURRENCY,"EUR");
			basket_plus::setBasketVar(DATE_TIME_FORMAT,"d-m-Y G:i");
			basket_plus::setBasketVar(DECIMAL_SEPARATOR,",");
			basket_plus::setBasketVar(ALLOW_PICKUP,"1");
			basket_plus::setBasketVar(IS_PICKUP_DEFAULT,"0");
			basket_plus::setBasketVar(PICKUP_LOCATION,"BasketPlusPhoto.com, Postlaan 1, 8888 ZZ Poststad");
			basket_plus::setBasketVar(USE_PAYPAL,"0");
			basket_plus::setBasketVar(PAYPAL_ACCOUNT,"paypal@basketplusphoto.com");
			basket_plus::setBasketVar(PAYPAL_TEST_MODE,"1");
			basket_plus::setBasketVar(ORDER_PREFIX,"2013-");
			basket_plus::setBasketVar(ORDER_BANK_ACCOUNT,"NL00INGB0000123456 (P123456)");
			basket_plus::setBasketVar(ORDER_BANK_ACCOUNT_OWNER,"B. Plus, Poststad");
			basket_plus::setBasketVar(PAYMENT_OPTIONS,str_replace('$','"','<p>U kunt betalen via overmaking op de bankrekening van %webshop.<br />
Instructies vindt u in de bevestigingsmail van de bestelling.</p><br />
<br />
<h3>Algemene voorwaarden</h3>
<p>U kunt de Algemene voorwaarden via deze <a href=$/downloads/Algemene_voorwaarden_2012.pdf$ target=$_blank$>link</a> bekijken.</p><br />'));
			basket_plus::setBasketVar(ADDRESS_FORMAT,"%street %house<br />
%postalcode %town<br />");
			basket_plus::setBasketVar(USE_ADDRESS_SUBURB,"0");
			basket_plus::setBasketVar(USE_ADDRESS_PROVINCE,"0");
			basket_plus::setBasketVar(USE_ADDRESS_COUNTRY,"0");

		/* basket template settings */
		
			basket_plus::setBasketVar(PAYMENT_DETAILS,str_replace('$','"','<h3 class="h3">Betaling</h3>
															<p style="font-family: verdana, sans-serif; font-size: 11px; color: rgb(80, 84, 80); line-height: 19px; border: none; margin-top: 5px; vertical-align: top;">
																U kunt betalen door het totaalbedrag over te maken op de bankrekening van %webshop.<br /><br />
																Totaalbedrag: <strong>%total_cost</strong><br />
																Rekeningnummer: <strong>%order_bank_account</strong><br />
																&nbsp;t.n.v. <strong>%order_bank_account_owner</strong><br />
																&nbsp;o.v.v. <strong>bestelnummer %order_number</strong><br />
															</p>'));
			basket_plus::setBasketVar(CUSTOMER_DETAILS," %full_name<br />
 %address<br />
 E-mail: %email<br />
 Telefoon: %phone<br />");
			basket_plus::setBasketVar(ORDER_COMPLETE_PAGE,"<p>Hartelijk dank voor uw bestelling. Uw bestelnummer is </b>%order_number</b>.</p>
<br />
<p>%webshop heeft een bevestigingsmail verzonden met de gegevens van uw bestelling en de betalingsinformatie.</p>
<p>Wij verwerken de bestelling zodra de betaling is ontvangen.</p>
<br />
<p>Voor vragen of opmerkingen over uw bestelling kunt u contact opnemen via %email_order</p>");
			basket_plus::setBasketVar(ORDER_PAID_COMPLETE_PAGE,"<p>Hartelijk dank voor uw bestelling. Uw bestelnummer is </b>%order_number</b>.</p>
<br />
<p>%webshop heeft een bevestigingsmail verzonden met de gegevens van uw bestelling.</p>
<p>Uw betaling is ontvangen. U ontvangt een e-mail zodra de bestelling naar u wordt verzonden of klaar ligt om af te halen.</p>");
			basket_plus::setBasketVar(ORDER_THANKYOU,"Beste %full_name,<br>
<br>
Hartelijk dank voor uw bestelling. De bestelgegevens en betalingsinformatie vindt u hieronder.<br>
De aflevering vindt plaats circa 10 werkdagen nadat uw betaling is ontvangen door %webshop.<br>
<br>
%order_email_closing
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_DETAILS,'<h3 class="h3">Bestelgegevens</h3>
<p style="font-family: verdana, sans-serif; font-size: 11px; color: rgb(80, 84, 80); line-height: 19px; border: none; margin-top: 5px; vertical-align: top;">
	Bestelnummer: <strong>%order_number</strong><br />
	Besteld op: <strong>%order_datetime</strong><br />
	<br>
	<strong>Bestemd voor</strong><br />
	%customer_details <!-- // replace with html -->
	%cost_details <!-- // replace with html -->
	%delivery_method <!-- // replace with html -->
	%comments <!-- // displayed only if the customer provided comments AND/OR ref/ref2 is filled\\-->															
</p>');
			basket_plus::setBasketVar(ORDER_COMPLETE_EMAIL_SUBJECT,"Uw bestelling %order_number bij %webshop");
			basket_plus::setBasketVar(ORDER_PAID_EMAIL_SUBJECT,"Update van uw bestelling %order_number bij %webshop: betaling ontvangen");
			basket_plus::setBasketVar(ORDER_PAID_EMAIL,"Beste %name,<br>
<br>
%webshop heeft uw betaling van %total_cost ontvangen en zal bestelling %order_number verwerken. <br>
U ontvangt een e-mail zodra de bestelling naar u wordt verzonden of klaar ligt om af te halen. <br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_PAID_DELIVERED_EMAIL_SUBJECT,"Update van uw bestelling %order_number bij %webshop: betaling ontvangen");
			basket_plus::setBasketVar(ORDER_PAID_DELIVERED_EMAIL,"Beste %name,<br>
<br>
%webshop heeft uw betaling van %total_cost ontvangen. De foto's zijn al eerder afgeleverd.<br>
Nogmaals dank voor uw bestelling en veel plezier met de foto's!<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_LATE_PAYMENT_EMAIL_SUBJECT,"Update van uw bestelling %order_number bij %webshop: wacht op betaling");
			basket_plus::setBasketVar(ORDER_LATE_PAYMENT_EMAIL,"Beste %name,<br>
<br>
Enige tijd geleden heeft u bij %webshop bestelling %order_number geplaatst.<br>
Uit onze administratie blijkt dat het bedrag van <strong>%total_cost</strong> nog niet is voldaan. Wij maken u erop attent dat wij pas na ontvangst van de betaling de bestelling verwerken.<br>
<br>
U kunt het openstaande bedrag overmaken op rekening <strong>%order_bank_account</strong> tnv <strong>%order_bank_account_owner</strong> ovv <strong>bestelnummer %order_number</strong>.<br>
<br>
Mocht deze herinnering uw betaling hebben gekruist, dan kunt u deze als niet verzonden beschouwen.<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_DELIVERED_EMAIL_SUBJECT,"Update van uw bestelling %order_number bij %webshop: bestelling verstuurd");
			basket_plus::setBasketVar(ORDER_DELIVERED_EMAIL,"Beste %name,<br>
<br>
Uw bestelling %order_number is %delivery_method. <br>
Nogmaals dank voor uw bestelling en veel plezier met de foto's!<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_DELIVERED_NOTPAID_EMAIL_SUBJECT,"Update van uw bestelling %order_number bij %webshop: bestelling verstuurd zonder betaling");
			basket_plus::setBasketVar(ORDER_DELIVERED_NOTPAID_EMAIL,"Beste %name,<br>
<br>
Uw bestelling %order_number is %delivery_method, hoewel het bedrag van %total_cost volgens onze administratie nog niet is voldaan. <br>
<br>
Wij verzoeken u het openstaande bedrag zo spoedig mogelijk te voldoen. U kunt het openstaande bedrag overmaken op rekening <strong>%order_bank_account</strong> tnv <strong>%order_bank_account_owner</strong> ovv <strong>bestelnummer %order_number</strong>.<br>
<br>
Mocht dit bericht uw betaling hebben gekruist, dan kunt u dit verzoek negeren.<br>
<br>
Voor vragen of opmerkingen over uw bestelling of de betaling kunt u contact opnemen via %email_order.<br>");
			basket_plus::setBasketVar(ORDER_DELAYED_EMAIL_SUBJECT,"Update van uw bestelling %order_number bij %webshop: bestelling vertraagd");
			basket_plus::setBasketVar(ORDER_DELAYED_EMAIL,"Beste %name,<br>
<br>
Uw bestelling %order_number is door omstandigheden vertraagd.<br> 
Wij doen ons best de bestelling zo snel mogelijk af te leveren.<br>
<br>
%order_email_closing");
			basket_plus::setBasketVar(ORDER_CANCELLED_EMAIL_SUBJECT,"Update van uw bestelling %order_number bij %webshop: bestelling geannuleerd");
			basket_plus::setBasketVar(ORDER_CANCELLED_EMAIL,"Beste %name,<br>
<br>
%webshop heeft uw bestelling %order_number geannuleerd.<br>");
			basket_plus::setBasketVar(ORDER_EMAIL_CLOSING,"Voor vragen of opmerkingen over uw bestelling kunt u contact opnemen via %email_order.<br>
<br>
Met vriendelijke groet,<br>
%webshop - %webshop_owner");
		}
	}
}
