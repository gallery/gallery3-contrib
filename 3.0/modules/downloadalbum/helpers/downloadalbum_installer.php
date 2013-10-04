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
class downloadalbum_installer {
  static function install() {
    access::register_permission("downloadalbum", "Download album");
    module::set_version("downloadalbum", 3);
  }

  static function upgrade($version) {
  	if( $version == 2 ) {
      access::register_permission("downloadalbum", "Download album");
      module::set_version("downloadalbum", 3);
  	}
  }

  static function uninstall() {
    access::delete_permission("downloadalbum");
    module::delete("downloadalbum");
  }
}
