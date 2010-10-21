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
class photoannotation_installer {
  static function install() {
    // Create a table to store face coordinates in.
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {items_faces} (
               `id` int(9) NOT NULL auto_increment,
               `tag_id` int(9) NOT NULL,
               `item_id` int(9) NOT NULL,
               `x1` int(9) NOT NULL,
               `y1` int(9) NOT NULL,
               `x2` int(9) NOT NULL,
               `y2` int(9) NOT NULL,
               `description` varchar(2048) default NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE IF NOT EXISTS {items_notes} (
               `id` int(9) NOT NULL auto_increment,
               `item_id` int(9) NOT NULL,
               `x1` int(9) NOT NULL,
               `y1` int(9) NOT NULL,
               `x2` int(9) NOT NULL,
               `y2` int(9) NOT NULL,
               `title` varchar(64) NOT NULL,
               `description` varchar(2048) default NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");
               
    $db->query("CREATE TABLE IF NOT EXISTS {items_users} (
               `id` int(9) NOT NULL auto_increment,
               `user_id` int(9) NOT NULL,
               `item_id` int(9) NOT NULL,
               `x1` int(9) NOT NULL,
               `y1` int(9) NOT NULL,
               `x2` int(9) NOT NULL,
               `y2` int(9) NOT NULL,
               `description` varchar(2048) default NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");
               
    $db->query("CREATE TABLE IF NOT EXISTS {photoannotation_notifications} (
               `id` int(9) NOT NULL auto_increment,
               `user_id` int(9) NOT NULL unique,
               `newtag` int(2) default NULL,
               `comment` int(2) default NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");

    // Set the module's version number.
    module::set_version("photoannotation", 4);
  }

  static function upgrade($version) {
    if ($version == 1) { 
      module::set_version("photoannotation", $version = 2);
    }
    if ($version == 2) { 
      $db = Database::instance();
      $db->query("CREATE TABLE IF NOT EXISTS {items_users} (
                 `id` int(9) NOT NULL auto_increment,
                 `user_id` int(9) NOT NULL,
                 `item_id` int(9) NOT NULL,
                 `x1` int(9) NOT NULL,
                 `y1` int(9) NOT NULL,
                 `x2` int(9) NOT NULL,
                 `y2` int(9) NOT NULL,
                 `description` varchar(2048) default NULL,
                 PRIMARY KEY (`id`))
                 DEFAULT CHARSET=utf8;");
      module::set_version("photoannotation", $version = 3);
    }
    if ($version == 3) { 
      $db = Database::instance();
      $db->query("CREATE TABLE IF NOT EXISTS {photoannotation_notifications} (
             `id` int(9) NOT NULL auto_increment,
             `user_id` int(9) NOT NULL unique,
             `newtag` int(2) default NULL,
             `comment` int(2) default NULL,
             PRIMARY KEY (`id`))
             DEFAULT CHARSET=utf8;");
      module::set_version("photoannotation", $version = 4);
    }
  }

  static function deactivate() {
  // Clear the require tags message when photoannotation is deactivated.
    site_status::clear("photoannotation_needs_tag");
    site_status::clear("photoannotation_incompatibility_tagfaces");
   }

  static function uninstall() {
    // Delete the face table before uninstalling.
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {items_faces};");
    $db->query("DROP TABLE IF EXISTS {items_notes};");
    $db->query("DROP TABLE IF EXISTS {items_users};");
    $db->query("DROP TABLE IF EXISTS {photoannotation_notifications};");
    module::delete("photoannotation");
  }
}
