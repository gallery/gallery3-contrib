<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class Bp_Order_Log_Model extends ORM {

//order processing log
  const ORDERED = 1;
	const ORDERED_LOG_TEXT = "Order completed";
  const PAID = 2;
  const LATE_PAYMENT = 3;
  const COPY_SENT = 9;
  const DELIVERED_NOTPAID = 10;
  const DELIVERED = 20;
  const EXPIRED = 30;
	const DELAYED = 40;
  const CANCELLED = 99;

  public function status(){
    switch ($this->status){
      case 1:
        return t("Waiting Payment");
      case 2:
        return t("Payment Confirmed");
      case 3:
			return t("Late Payment");
			case 10:
			return t("Delivered,w/o payment");
			case 20:
			return t("Complete");
			case 21:
			return t("Expired");
			case 99:
			return t("Cancelled");

      default:
        return t("Unknown");
    }
  }

  public function event(){
    switch ($this->event){
      case self::ORDERED:
        return t("Order confirmation sent");
      case self::PAID:
        return t("Payment confirmation sent");
      case self::LATE_PAYMENT:
        return t("Late Payment notification sent");
      case self::COPY_SENT:
				return t("Order confirmation Copy sent");
			case self::DELIVERED_NOTPAID:
				return t("Delivery without Payment notification sent");
      case self::DELIVERED:
        return t("Delivery notification sent");
			case self::DELAYED:
				return t("Delay notification sent");
			case self::EXPIRED:
				return t("Expiration notification sent");
			case self::CANCELLED:
				return t("Cancellation notification sent");

      default:
        return t("Unknown");
    }
  }
}
