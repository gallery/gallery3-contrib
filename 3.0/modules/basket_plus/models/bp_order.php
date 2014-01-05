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
class Bp_Order_Model extends ORM {
  const WAITING_PAYMENT = 1;
  const PAYMENT_CONFIRMED = 2;
  const DELIVERED_NOTPAID = 10;
  const DELIVERED = 20;
  const EXPIRED = 21;
  const CANCELLED = 99;

// Payment methods
  const PAYMENT_OFFLINE = 1;
  const PAYMENT_PAYPAL = 2;

// Delivery methods
  const DELIVERY_MAIL = 2;
  const DELIVERY_EMAIL = 3;
  const DELIVERY_PICKUP = 4;

	//Fixed texts for order screens and order e-mail
  const DELIVERY_MAIL_TEXT = "Delivery at home address by mail";
  const DELIVERY_EMAIL_TEXT = "Delivery by e-mail";
  const DELIVERY_PICKUP_TEXT = "Pickup at";
	
/*
 * USED IN: view order (admin)
 * DESCRIPTION: shows order title
 */
  public function title(){
    return  basket_plus::getBasketVar(ORDER_PREFIX).$this->id." ".$this->name." ".t($this->status());
  }

/*
 * USED IN: views orders and order_logs (admin)
 * DESCRIPTION: shows order status
 */
  public function status(){
//@TODO add completed+delivery method
    switch ($this->status){
      case self::WAITING_PAYMENT:
        return "Waiting Payment";
      case self::PAYMENT_CONFIRMED:
        return "Payment confirmed";
			case self::DELIVERED_NOTPAID:
        return "Delivered without payment";
			case self::DELIVERED:
        return "Delivered";
			case self::EXPIRED:
        return "Expired";
			case self::CANCELLED:
        return "Cancelled";

      default:
        return "Unknown";
    }
  }

/*
 * USED IN: view order (admin)
 * DESCRIPTION: shows order payment method
 */
  public function payment_method(){
    switch ($this->payment_method){
      case Bp_Order_Model::PAYMENT_OFFLINE:
        return t("Offline Payment");
      case Bp_Order_Model::PAYMENT_PAYPAL:
        return t("Paypal Payment");
      default:
        return t("Unknown Payment method");
    }
  }

/*
 * USED IN: view order (admin)
 * DESCRIPTION: shows order delivery method
 */
  public function delivery_method(){
    switch ($this->delivery_method){
      case Bp_Order_Model::DELIVERY_MAIL:
        return t("Mail");
      case Bp_Order_Model::DELIVERY_EMAIL:
        return t("Email");
      case Bp_Order_Model::DELIVERY_PICKUP:
        return t("Pickup");
      default:
        return t("Unknown Delivery method");
    }
  }
}
