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

class favourites_installer
{
  static function install(){
   module::set_version("favourites", 1);
   favourites_configuration::setEmailTemplate("Hi %name,

This is an automated e-mail. Your list of favourites and comments have been emailed to %owner. To view your list of favourites use the following link.

%url

Thanks");
   favourites_configuration::setFromEmailAddress("website@yourdomain.com");
   favourites_configuration::setEmailAddress("username@youremailaddress.com");
   favourites_configuration::setOwner("Your Name");
  }

}
