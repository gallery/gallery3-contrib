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
class Order_Log_Model extends ORM {

  public function status(){
    switch ($this->status){
      case 1:
        //return "Waiting Payment";
        return "Wacht op betaling";
      case 2:
        //return "Payment Confirmed";
        return "Betaling bevestigd";
      case 3:
			//return "Late Payment";
			return "Betalingsherinnering verstuurd";
			case 10:
			//return "Delivered,w/o payment";
			return "Afgeleverd zonder betaling";
			case 20:
			//return "Complete";
			return "Afgehandeld";
			case 21:
			//return "Expired";
			return "Verlopen";
			case 99:
			//return "Cancelled";
			return "Geannuleerd";

      default:
        //return "Unknown";
        return "Onbekend";
    }
  }

  public function event(){
    switch ($this->event){
      case 1:
        //return "Waiting Payment";
        return "Bestelling geplaatst";
      case 2:
        //return "Payment Confirmed";
        return "Betaling ontvangen";
      case 3:
        //return "Late Payment";
        return "Betalingsherinnering verstuurd";
      case 9:
			//return "Order Copy";
			return "Kopie bestelling verstuurd";
			case 10:
			//return "Delivered,w/o payment";
			return "Bestelling afgeleverd zonder betaling";
      case 20:
        //return "Complete";
        return "Bestelling verzonden";
			case 21:
				//return "Expired";
				return "Bestelling verlopen";
			case 99:
			//return "Cancelled";
			return "Bestelling geannuleerd";

      default:
        //return "Unknown";
        return "Onbekend";
    }
  }
}
