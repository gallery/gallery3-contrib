<?php defined("SYSPATH") or die("No direct script access.")
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
?>
<SCRIPT language="JavaScript">
	function back(){
		history.go(-1);
	}

  function isValidEmail(s){
    return (s.indexOf(".")>2)&&(s.indexOf("@")>0);
  }

  function isHotmail(s) { //not used
    return(s.indexOf("hotmail")==-1)
  }

  function se(val){
    val.style.backgroundColor="#FAA";
  }

  function re(val){
    val.style.backgroundColor="#FFF";
  }

  function checkInput(val){
    if ((!val.value) || (val.value.length==0)) {se(val);return false;}
    re(val);
    return true;
  }
	//mandatory fields with all delivery options
  function checkMandatory() {
    var p=true;
    var doc=document.checkout;
    //check initials
    if(!checkInput(doc.initials)){p=false;}
    //check name
    if(!checkInput(doc.fname)){p=false;}
    //check phone
    if(!checkInput(doc.phone)){p=false;}
    //check email
    if((!checkInput(doc.email))||(!isValidEmail(doc.email.value))){se(doc.email);p=false;}
    if (!p){
      alert('Please fill all required fields.');
//NL      alert('U heeft een of meer verplichte velden niet ingevuld.');
    }
    return p;  
  }
	//mandatory fields with all delivery option MAIL
  function checkAddress() {
    var p=true;
    var doc=document.checkout;
    //check address
    if(!checkInput(doc.street)){p=false;}
    if(!checkInput(doc.house)){p=false;}
    if(!checkInput(doc.postalcode)){p=false;}
    if(!checkInput(doc.town)){p=false;}
    if (!p){
      alert('Please fill all required address fields.');
//NL      alert('U heeft een of meer adresvelden niet ingevuld.');
    }
    return p;  
  }
	//mandatory fields with all delivery option PICKUP
  function checkRef() {
    var p=true;
    var doc=document.checkout;
    //check additional reference
    if(!checkInput(doc.order_ref1)){p=false;}
    if(!checkInput(doc.order_ref2)){p=false;}
    if (!p){
			alert('Please fill both additional reference fields.');
//NL      alert('U heeft de naam en/of groep van uw kind niet ingevuld.');
    }
    return p;  
  }
  function checkTerms() {
    var doc=document.checkout;
    //check agreeTerms
    if(doc.agreeterms.checked==false){
		      alert('To complete your order, you need to agree with our General Terms.');
//NL      alert('Om te kunnen bestellen dient u akkoord te gaan met de Algemene voorwaarden.');
      return false;
    }
    return true;
  }
  //checkout with pickup
  function checkCheckoutPickup() {
    if (checkMandatory()){
			if (checkTerms()) {
				if (checkRef()){
					document.checkout.submit();
				}
			}
    }
  }
  //checkout with e-mail
  function checkCheckoutEmail() {
    if (checkMandatory()){
			if (checkTerms()) {
				document.checkout.submit();
			}
    }
  }
  //checkout with pack&post
  function checkCheckoutMail() {
    var p=true;
    if (checkMandatory()){
      if (checkAddress()) {
        if (checkTerms()) {
          document.checkout.submit();
        }
      }
    }
  }
</SCRIPT>

<div class="g-block">
<h2><?= t("Delivery and Contact (Step 1 of 3)") ?></h2>
  <div id="b-complete">
  <? 
	$payment_details = basket_plus::getBasketVar(PAYMENT_OPTIONS); 
  $webshop = basket_plus::getBasketVar(WEBSHOP);
  $payment_details = basket_plus::replaceStrings($payment_details,Array("webshop"=> $webshop));
	/* here the payment options text is loaded */
	if ($payment_details):?>
    <div class="basket-right" id="payment">
      <h3> <?= t("Payment Options") ?></h3>
      <?= $payment_details; ?>
    </div>
  <? 
	endif;
	/* here the form is loaded */?>
	<?= $form ?>
  <div><label><?= t("* required field") ?><br/></label></div>
		<div class="basketbuttons">
			<a href="javascript:back();" class="left g-button ui-state-default ui-corner-all ui-icon-left">
				<span class="ui-icon ui-icon-arrow-1-w"></span><?= t("Back to Basket") ?></a>
			<?
			/* check for pack&post */
			$basket = Session_Basket::get();
			$postage = $basket->postage_cost();

			/* Pickup not selected and postage cost */
			if ($basket->pickup && $postage > 0):?>
			<a href="javascript: checkCheckoutPickup()" class="right g-button ui-state-default ui-corner-all ui-icon-right">
				<span class="ui-icon ui-icon-arrow-1-e"></span><?= t("To Order Confirmation") ?></a>
		<? /* Pickup selected and postage cost */
			elseif ($postage > 0):?>
			<a href="javascript: checkCheckoutMail()" class="right g-button ui-state-default ui-corner-all ui-icon-right">
				<span class="ui-icon ui-icon-arrow-1-e"></span><?= t("To Order Confirmation") ?></a>
		<? /* no postage cost */
			else: ?>
			<a href="javascript: checkCheckoutEmail()" class="right g-button ui-state-default ui-corner-all ui-icon-right">
				<span class="ui-icon ui-icon-arrow-1-e"></span><?= t("To Order Confirmation") ?></a>
		<? endif; ?>
		</div>
  </div>
</div>
