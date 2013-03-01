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
class ecard_installer {
  private static function getversion() { 
	return 11; 
  }
  
  private static function setversion() { 
	module::set_version("ecard", self::getversion()); 
  }
  
  static function install() {
	module::set_var("ecard","send_plain",false); 
    module::set_var("ecard", "subject", "You have been sent an eCard");
    module::set_var("ecard", "message",
                    "Hello, \r\n%fromname has sent you an eCard. " .
                    "Click the image to be taken to the gallery.");
    module::set_var("ecard", "bcc", "");
    module::set_var("ecard", "access_permissions", "everybody");
	module::set_var("ecard","max_length",255);
    self::setversion();
  }
  
  static function upgrade($version) {
	if($version <= 8) {
		module::set_var("ecard", "message",
						"Hello, \r\n%fromname has sent you an eCard. " .
						"Click the image to be taken to the gallery.");	  
		module::set_var("ecard","max_length",255);
	} else if($version == 9 || $version == 10) {
		module::set_var("ecard","send_plain",false);
	}
	self::setversion();
  }
}
