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
class phpmailer_installer {
  static function install() {
    // Set the default value for this module's behavior.
    module::set_var("phpmailer", "phpmailer_path", "/path/to/class.phpmailer.php");
    module::set_var("phpmailer", "phpmailer_from_address", "example@gallery.com");
    module::set_var("phpmailer", "phpmailer_from_name", "Gallery Administrator");
    module::set_var("phpmailer", "smtp_server", "smtp.example.com");
    module::set_var("phpmailer", "use_ssl", false);
    module::set_var("phpmailer", "smtp_login", "");
    module::set_var("phpmailer", "smtp_password", "");
    module::set_var("phpmailer", "smtp_port", "25");

    // Set the module's version number.
    module::set_version("phpmailer", 2);
  }

  static function upgrade($version) {
    module::set_var("phpmailer", "use_ssl", false);
    module::set_var("phpmailer", "smtp_port", "25");
    module::set_version("phpmailer", 2);
  }
}
