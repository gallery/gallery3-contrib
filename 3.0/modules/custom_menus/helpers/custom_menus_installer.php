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
class custom_menus_installer {
  static function install() {
    // Create a table to store menu info in.
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {custom_menus} (
               `id` int(9) NOT NULL auto_increment,
               `title` varchar(255) default NULL,
               `url` text default NULL,
               `parent_id` int(9) NOT NULL default 0,
               `order_by` int(9) NOT NULL default 0,
               PRIMARY KEY (`id`),
               UNIQUE KEY(`id`))
               DEFAULT CHARSET=utf8;");

    // Set the module version number.
    module::set_version("custom_menus", 1);
  }
}
