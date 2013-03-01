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
class g1_import_installer {
  static function install() {
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {g1_maps} (
                 `id` int(9) NOT NULL,
                 `album` varchar(128) NOT NULL,
                 `item` varchar(128) default NULL,
                 `resource_type` varchar(64) default NULL,
               PRIMARY KEY (`id`, `resource_type`),
               KEY `g1_map_index` (`album`, `item`))
               DEFAULT CHARSET=utf8;");

    module::set_version("g1_import", 1);
  }

  static function upgrade($version) {
    $db = Database::instance();
    /* reserved for future versions
    if ($version == 1) {
      module::set_version('g1_import', $version = 2);
    }
    //*/
  }

  static function uninstall() {
  }
}
