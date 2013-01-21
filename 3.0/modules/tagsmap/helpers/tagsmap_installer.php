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
class tagsmap_installer {
  static function install() {
    // Create a table to store GPS data in.
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {tags_gpses} (
               `id` int(9) NOT NULL auto_increment,
               `tag_id` int(9) NOT NULL,
               `latitude` varchar(128) NOT NULL,
               `longitude` varchar(128) NOT NULL,
               `description` varchar(2048) default NULL,
               PRIMARY KEY (`id`),
               KEY(`tag_id`, `id`))
               DEFAULT CHARSET=utf8;");

    // Set the default to Australia (homage to rWatcher)
    module::set_var("tagsmap", "googlemap_latitude", -26.11);
    module::set_var("tagsmap", "googlemap_longitude", 134);
    module::set_var("tagsmap", "googlemap_zoom", 5);
    module::set_var("tagsmap", "googlemap_type", "G_NORMAL_MAP");
    module::set_var("tagsmap", "restrict_maps", "0");
    module::set_version("tagsmap", 3);
  }

  static function upgrade($version) {
    if ($version == 2) {
      module::set_var("tagsmap", "restrict_maps", "0");
      module::set_version("tagsmap", 3);
    }
  }

  static function deactivate() {
    site_status::clear("tagsmap_needs_tag");
  }

  static function can_activate() {
    $messages = array();
    if (!module::is_active("tag")) {
      $messages["warn"][] = t("The TagsMap module requires the Tags module.");
    }
    return $messages;
  }

  static function uninstall() {
    // Delete the GPS table before uninstalling.
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {tags_gpses};");
    module::delete("tagsmap");
  }
}
