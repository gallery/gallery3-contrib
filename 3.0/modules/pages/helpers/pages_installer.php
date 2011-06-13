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
class pages_installer {
  static function install() {
    // Create a table to store pages in.
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {static_pages} (
               `id` int(9) NOT NULL auto_increment,
               `name` varchar(255) default NULL,
               `title` varchar(255) default NULL,
               `html_code` text default NULL,
               `display_menu` boolean default 0,
               PRIMARY KEY (`id`),
               UNIQUE KEY(`name`))
               DEFAULT CHARSET=utf8;");

    // Set the module version number.
    module::set_version("pages", 2);
  }
  static function upgrade($version) {
    $db = Database::instance();
    if ($version == 1) {
      $db->query("ALTER TABLE {static_pages} ADD COLUMN `display_menu` boolean default 0");
      module::set_version("pages", $version = 2);
    }
  }
}
