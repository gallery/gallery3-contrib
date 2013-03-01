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
class picasa_faces_installer {
  static function install() {
    // Create a table to store face mappings in.
    $db = Database::instance();

    $db->query(
      "CREATE TABLE IF NOT EXISTS {picasa_faces} (
         `id` int(9) NOT NULL auto_increment,
         `face_id` varchar(16) NOT NULL,
         `tag_id` int(9) NOT NULL,
         `user_id` int(9) NOT NULL,
         PRIMARY KEY  (`id`),
         KEY `face_id` (`face_id`,`id`)
        ) DEFAULT CHARSET=utf8;"
      );

    // Set the module version number.
    module::set_version("picasa_faces", 2);
  }

  static function upgrade($version) {
    if ($version == 1) {
      Database::instance()->query(
        "ALTER TABLE `picasa_faces` ADD `user_id` int(9) NOT NULL"
        );

      module::set_version("picasa_faces", 2);
    }
  }

  static function deactivate() {
    // Clear the require photo annototaion message when picasa faces is deactivated.
    site_status::clear("picasa_faces_needs_photoannotation");
  }

  static function uninstall() {
    // Delete the face mapping table before uninstalling.
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {picasa_faces};");
    module::delete("picasa_faces");
  }
}

?>
