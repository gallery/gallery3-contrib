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
class albumpassword_installer {
  static function install() {
    // Create a table to store passwords in.
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {items_albumpasswords} (
               `id` int(9) NOT NULL auto_increment,
               `album_id` int(9) NOT NULL,
               `password` varchar(64) NOT NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");

    // Create a table to store a list of all protected items in.
    $db->query("CREATE TABLE IF NOT EXISTS {albumpassword_idcaches} (
               `cache_id` int(9) NOT NULL auto_increment,
               `password_id` int(9) NOT NULL,
               `item_id` int(9) NOT NULL,
               PRIMARY KEY (`cache_id`))
               DEFAULT CHARSET=utf8;");

    // Set the default value for this module's behavior.
    module::set_var("albumpassword", "hideonly", true);

    // Set the module's version number.
    module::set_version("albumpassword", 3);
  }

  static function upgrade($version) {
    $db = Database::instance();
    if ($version == 1) {
      // Set the default value for this module's behavior.
      module::set_var("albumpassword", "hideonly", true);
      module::set_version("albumpassword", $version = 2);
    }
    if ($version == 2) {
      // Create a table to store a list of all protected items in.
      $db->query("CREATE TABLE IF NOT EXISTS {albumpassword_idcaches} (
                 `cache_id` int(9) NOT NULL auto_increment,
                 `password_id` int(9) NOT NULL,
                 `item_id` int(9) NOT NULL,
                 PRIMARY KEY (`cache_id`))
                 DEFAULT CHARSET=utf8;");
      module::set_version("albumpassword", $version = 3);
    }
  }

  static function uninstall() {
    // Delete the password table before uninstalling.
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {items_albumpasswords};");
    $db->query("DROP TABLE IF EXISTS {albumpassword_idcaches};");
    module::delete("albumpassword");
  }
}
