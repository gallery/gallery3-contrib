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
class custom_albums_installer {
  static function install() {
    // Create a table to store custom album info in.
    $db = Database::instance();
     
    $db->query(
      "CREATE TABLE IF NOT EXISTS {custom_albums} (
         `id` int(9) NOT NULL auto_increment,
         `album_id` int(9) NOT NULL,
         `thumb_size` int(9) NOT NULL,
         PRIMARY KEY  (`id`),
         KEY `album_id` (`album_id`,`id`)
       ) DEFAULT CHARSET=utf8;"
     );

    custom_albums_installer::update_rules();

    module::set_version("custom_albums", 1);
  }

  static function uninstall() {
    // Delete the custom album table before uninstalling.
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {custom_albums};");
    module::delete("custom_albums");
  }
  
  static function update_rules() {
    // Make sure our thumb size matches the gallery one
    $thumb_size = module::get_var("gallery", "thumb_size");
    if ($thumb_size != module::get_var("custom_albums", "thumb_size")) {
      // Remove and readd our rule with the latest thumb size
      graphics::remove_rule("custom_albums", "thumb", "custom_albums_graphics::build_thumb");
      graphics::add_rule(
        "custom_albums", "thumb", "custom_albums_graphics::build_thumb",
        array("width" => $thumb_size, "height" => $thumb_size, "master" => Image::AUTO),
        101);

      // Deactivate the gallery thumbnail generation, we'll handle it now
      graphics::deactivate_rules("gallery");
      
      module::set_var("custom_albums", "thumb_size", $thumb_size);
    }

    // Make sure our resize size matches the gallery one
    $resize_size = module::get_var("gallery", "resize_size");
    if ($resize_size != module::get_var("custom_albums", "resize_size")) {
      // Remove and readd our rule with the latest resize size
      graphics::remove_rule("custom_albums", "resize", "custom_albums_graphics::build_resize");
      graphics::add_rule(
        "custom_albums", "resize", "custom_albums_graphics::build_resize",
        array("width" => $resize_size, "height" => $resize_size, "master" => Image::AUTO),
        101);

      // Deactivate the gallery resize, we'll handle it now
      graphics::deactivate_rules("gallery");
      
      module::set_var("custom_albums", "resize_size", $resize_size);
    }
  }
}
