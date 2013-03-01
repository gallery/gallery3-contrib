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
class exif_gps_installer {
  static function install() {
    // Create a table to store GPS data in.
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {exif_coordinates} (
               `id` int(9) NOT NULL auto_increment,
               `item_id` int(9) NOT NULL,
               `latitude` varchar(128) NOT NULL,
               `longitude` varchar(128) NOT NULL,
               PRIMARY KEY (`id`),
               KEY(`item_id`, `id`))
               DEFAULT CHARSET=utf8;");

    // If tagsmap is installed, copy the API key over.
    if (module::is_active("tagsmap")) {
      module::set_var("exif_gps", "googlemap_api_key", module::get_var("tagsmap", "googlemap_api_key"));
    } else {
      module::set_var("exif_gps", "googlemap_api_key", "");
    }

    // Set some default values.
    module::set_var("exif_gps", "sidebar_zoom", "14");
    module::set_var("exif_gps", "sidebar_mapformat", "1");
    module::set_var("exif_gps", "sidebar_maptype", "1");
    module::set_var("exif_gps", "largemap_maptype", "2");
    module::set_var("exif_gps", "googlemap_max_autozoom", "");
    module::set_var("exif_gps", "toolbar_map_album", "0");
    module::set_var("exif_gps", "toolbar_map_user", "0");
    module::set_var("exif_gps", "markercluster_gridsize", "50");
    module::set_var("exif_gps", "markercluster_maxzoom", "15");
    module::set_var("exif_gps", "restrict_maps", "0");

    // Set the module version number.
    module::set_version("exif_gps", 3);
  }

  static function upgrade($version) {
    if ($version == 1) {
      // If tagsmap is installed, copy the API key over.
      if (module::is_active("tagsmap")) {
        module::set_var("exif_gps", "googlemap_api_key", module::get_var("tagsmap", "googlemap_api_key"));
      } else {
        module::set_var("exif_gps", "googlemap_api_key", "");
      }

      // Set some default values.
      module::set_var("exif_gps", "sidebar_zoom", "14");
      module::set_var("exif_gps", "sidebar_mapformat", "1");
      module::set_var("exif_gps", "sidebar_maptype", "1");
      module::set_var("exif_gps", "largemap_maptype", "2");
      module::set_version("exif_gps", 2);
    }
    if ($version == 2) {
      module::set_var("exif_gps", "markercluster_gridsize", "50");
      module::set_var("exif_gps", "markercluster_maxzoom", "15");
      module::set_var("exif_gps", "restrict_maps", "0");
      module::set_version("exif_gps", 3);
    }
  }

  static function deactivate() {
    site_status::clear("exif_gps_needs_exif");
  }

  static function can_activate() {
    $messages = array();
    if (!module::is_active("exif")) {
      $messages["warn"][] = t("The EXIF_GPS module requires the EXIF module.");
    }
    return $messages;
  }

  static function uninstall() {
    // Delete the GPS table before uninstalling.
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {exif_coordinates};");
    module::delete("exif_gps");
  }
}
