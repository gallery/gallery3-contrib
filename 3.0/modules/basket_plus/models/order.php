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
class Order_Model extends ORM {
  const WAITING_PAYMENT = 1;
  const PAYMENT_CONFIRMED = 2;
  const DELIVERED_NOTPAID = 10;
  const DELIVERED = 20;
  const EXPIRED = 21;
  const CANCELLED = 99;

  const PAYMENT_PAYPAL = 1;
  const PAYMENT_OFFLINE = 2;

  const DELIVERY_MAIL = 2;
  const DELIVERY_EMAIL = 3;
  const DELIVERY_PICKUP = 4;

  public function title(){
    return  basket::getOrderPrefix().$this->id." ".$this->name." ".$this->status();
  }

  public function status(){
    switch ($this->status){
      case Order_Model::WAITING_PAYMENT:
        //return "Waiting Payment";
        return "Wacht op betaling";
      case Order_Model::PAYMENT_CONFIRMED:
        //return "Payment Confirmed";
        return "Betaling bevestigd";
//@TODO add completed+delivery method
        case Order_Model::DELIVERED_NOTPAID:
        //return "Delivered,w/o payment";
        return "Afgeleverd zonder betaling";
        case Order_Model::DELIVERED:
        //return "Complete";
        return "Afgehandeld";
        case Order_Model::EXPIRED:
        //return "Expired";
        return "Verlopen";
        case Order_Model::CANCELLED:
        //return "Cancelled";
        return "Geannuleerd";

      default:
        //return "Unknown";
        return "Onbekend";
    }
  }

  public function payment_method(){
    switch ($this->method){
      case 1:
        //return "through Paypal";
        return "Via PayPal";
      case 2:
        //return "offline";
        return "Overmaking - versturen per post";
      case 3:
        return "Overmaking - versturen per e-mail";
      case 4:
        return "Overmaking - afhalen";

      default:
        //return "Unknown";
        return "Onbekend";
    }
  }
}
