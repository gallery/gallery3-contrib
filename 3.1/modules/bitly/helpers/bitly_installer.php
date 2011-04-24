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
class bitly_installer {

  static function install() {
    Database::instance()
      ->query("CREATE TABLE {bitly_links} (
                `id` int(9) NOT NULL AUTO_INCREMENT,
                `item_id` int(9) NOT NULL,
                `hash` char(6) NOT NULL,
                `global_hash` char(6) NOT NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");
    module::set_version("bitly", 1);
    bitly::check_config();
  }

  static function deactivate() {
    site_status::clear("bitly_config");
  }
}
