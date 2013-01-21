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
class exif_gps_block_Core {
  static function get_site_list() {
    return array("exif_gps_location" => t("EXIF GPS Location"),
                 "exif_gps_maps" => t("EXIF GPS Maps"));
  }

  static function get($block_id, $theme) {
    $block = "";

    // Make sure the user can view maps before displaying any sidebars.
    if ((module::get_var("exif_gps", "restrict_maps") == true) && (identity::active_user()->guest)) {
      return;
    }

    switch ($block_id) {
    case "exif_gps_maps":
      // Display links to a map of the current album and
      //  a map of the current user.
      if ($theme->item()) {
        $album_id = "";
        $user_name = "";
        $item = $theme->item;
        if ($item->is_album()) {
          $album_id = $item->id;
        } else {
          $album_id = $item->parent_id;
        }
        $curr_user = ORM::factory("user")->where("id", "=", $item->owner_id)->find_all();
        if (count($curr_user) > 0) {
          $user_name = $curr_user[0]->full_name;
        }

        // Make sure there are actually map-able items to display.
        $album_items_count = ORM::factory("item", $album_id)
               ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
               ->viewable()
               ->order_by("exif_coordinates.latitude", "ASC")
               ->descendants_count(1);
        $user_items_count = ORM::factory("item")
               ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
               ->where("items.owner_id", "=", $item->owner_id)
               ->viewable()
               ->order_by("exif_coordinates.latitude", "ASC")
               ->count_all(1);

        if (($album_items_count > 0) || ($user_items_count > 0)) {
          $block = new Block();
          $block->css_id = "g-exif-gps-maps";
          $block->title = t("Maps");
          $block->content = new View("exif_gps_maps_sidebar.html");
          $block->content->album_id = $album_id;
          $block->content->user_id = $item->owner_id;
          $block->content->user_name = $user_name;
          $block->content->album_items = $album_items_count;
          $block->content->user_items = $user_items_count;
        }
      }
      break;

    case "exif_gps_location":
      // Look for coordinates to display.
      $latitude = "";
      $longitude = "";
      if ($theme->item()) {
        // Check and see if the item has exif coordinates associated with it.
        $record = ORM::factory("exif_coordinate")->where("item_id", "=", $theme->item->id)->find();
        if ($record->loaded()) {
          $latitude = $record->latitude;
          $longitude = $record->longitude;
        } elseif (module::is_active("tagsmap") && module::is_active("tag")) {
          // If there are no exif coordinates, check for tagsmap coordinates instead.
          $tagsItem = ORM::factory("tag")
            ->join("items_tags", "tags.id", "items_tags.tag_id")
            ->where("items_tags.item_id", "=", $theme->item->id)
            ->find_all();
          if (count($tagsItem) > 0) {
            foreach ($tagsItem as $oneTag) {
              $tagsGPS = ORM::factory("tags_gps")->where("tag_id", "=", $oneTag->id)->find();
              if ($tagsGPS->loaded()) {
                $latitude = $tagsGPS->latitude;
                $longitude = $tagsGPS->longitude;
                break;
              }
            }
          }
        }
      } elseif ( ($theme->tag()) && (module::is_active("tagsmap") && module::is_active("tag")) ) {
        // If the current page belongs to a tag, check and see if the tag has GPS coordinates.
        $tagsGPS = ORM::factory("tags_gps")->where("tag_id", "=", $theme->tag()->id)->find();
        if ($tagsGPS->loaded()) {
          $latitude = $tagsGPS->latitude;
          $longitude = $tagsGPS->longitude;
        }
      }

      // If coordinates were found, create the block.
      if ($latitude != "" && $longitude != "") {
        $block = new Block();
        $block->css_id = "g-exif-gps-location";
        $block->title = t("Location");
        if (module::get_var("exif_gps", "sidebar_mapformat") == 1) {
          $block->content = new View("exif_gps_dynamic_sidebar.html");
          if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "ROADMAP";
          if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "SATELLITE";
          if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "HYBRID";
          if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "TERRAIN";
          $block->content->items_count = 1;
        } else {
          $block->content = new View("exif_gps_static_sidebar.html");
          if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "roadmap";
          if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "satellite";
          if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "hybrid";
          if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "terrain";
        }
        $block->content->latitude = $latitude;
        $block->content->longitude = $longitude;
      } elseif (($theme->item()) && ($theme->item->is_album() && (module::get_var("exif_gps", "sidebar_mapformat") == 1))) {
        // If coordinates were NOT found, and this is an album with a dynamic map, then map the contents of the album.
        $items_count = ORM::factory("item", $theme->item->id)
                 ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
                 ->viewable()
                 ->order_by("exif_coordinates.latitude", "ASC")
                 ->descendants_count();
        if ($items_count > 0) {
          $block = new Block();
          $block->css_id = "g-exif-gps-location";
          $block->title = t("Location");
          $block->content = new View("exif_gps_dynamic_sidebar.html");
          if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "ROADMAP";
          if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "SATELLITE";
          if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "HYBRID";
          if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "TERRAIN";
          $block->content->album_id = $theme->item->id;
          $block->content->latitude = 0;
          $block->content->longitude = 0;
          $block->content->items_count = $items_count;
          $block->content->google_map_key = module::get_var("exif_gps", "googlemap_api_key");
        }
      }
      break;
    }
    return $block;
  }
}
