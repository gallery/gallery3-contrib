<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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
  static function install() {
    module::set_var("ecard", "subject", "You have been sent an eCard");
    module::set_var("ecard", "message",
                    "Hello %toname, \r\n%fromname has sent you an eCard. " .
                    "Click the image to be taken to the gallery.");
    module::set_var("ecard", "bcc", "");
    module::set_var("ecard", "access_permissions", "everybody");
    module::set_version("ecard", 4);
  }
}
