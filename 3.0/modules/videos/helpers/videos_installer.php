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
 
// rWatcher Edit:  This file was server_add_installer.
//  All occurences of server_add have been replaced with videos.
// The installer has been edited to create an additional table and module variable.
// The upgrader has been edited to skip everything before version 4, to keep version numbers in sync with server_add.

class videos_installer {
  static function install() {
    $db = Database::instance();
    $db->query("CREATE TABLE {videos_entries} (
                  `id` int(9) NOT NULL auto_increment,
                  `checked` boolean default 0,
                  `is_directory` boolean default 0,
                  `item_id` int(9),
                  `parent_id` int(9),
                  `path` varchar(255) NOT NULL,
                  `task_id` int(9) NOT NULL,
                  PRIMARY KEY (`id`))
                DEFAULT CHARSET=utf8;");

    // rWatcher Edit:  My Table.
    $db->query("CREATE TABLE {items_videos} (
                        `id` int(9) NOT NULL auto_increment,
                        `item_id` int(9) NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY (`item_id`, `id`))
                        DEFAULT CHARSET=utf8;");
    // rWatcher Edit:  My Variable.
    module::set_var("videos", "allowed_extensions", serialize(array("avi", "mpg", "mpeg", "mov", "wmv", "asf", "mts")));

    module::set_version("videos", 4);
    videos::check_config();
  }

  static function upgrade($version) {
    $db = Database::instance();

    if ($version < 4) {
      $db->query("DROP TABLE {videos_files}");
      $db->query("CREATE TABLE {videos_entries} (
                    `id` int(9) NOT NULL auto_increment,
                    `checked` boolean default 0,
                    `is_directory` boolean default 0,
                    `item_id` int(9),
                    `parent_id` int(9),
                    `path` varchar(255) NOT NULL,
                    `task_id` int(9) NOT NULL,
                    PRIMARY KEY (`id`))
                  DEFAULT CHARSET=utf8;");
      module::set_version("videos", $version = 4);
    }
  }

  static function deactivate() {
    site_status::clear("videos_configuration");
  }
}
