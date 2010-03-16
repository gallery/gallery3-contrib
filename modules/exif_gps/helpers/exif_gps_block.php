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
class exif_gps_block_Core {
  static function get_site_list() {
    return array("exif_gps_map" => t("EXIF GPS Map"));
  }

  static function get($block_id, $theme) {
    $block = "";

    switch ($block_id) {
    case "exif_gps_map":
      if ($theme->item()) {
        // Check and see if the item has exif coordinates associated with it.
        $record = ORM::factory("exif_coordinate")->where("item_id", "=", $theme->item->id)->find();
        if ($record->loaded()) {
          $block = new Block();
          $block->css_id = "g-exif-gps-sidebar";
          $block->title = t("Location");
          if (module::get_var("exif_gps", "sidebar_mapformat") == 1) {
            $block->content = new View("exif_gps_dynamic_sidebar.html");
            if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "ROADMAP";
            if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "SATELLITE";
            if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "HYBRID";
            if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "TERRAIN";
          } else {
            $block->content = new View("exif_gps_static_sidebar.html");
            if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "roadmap";
            if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "satellite";
            if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "hybrid";
            if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "terrain";
          }
          $block->content->latitude = $record->latitude;
          $block->content->longitude = $record->longitude;
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
                $block = new Block();
                $block->css_id = "g-exif-gps-sidebar";
                $block->title = t("Location");
                if (module::get_var("exif_gps", "sidebar_mapformat") == 1) {
                  $block->content = new View("exif_gps_dynamic_sidebar.html");
                  if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "ROADMAP";
                  if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "SATELLITE";
                  if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "HYBRID";
                  if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "TERRAIN";
                } else {
                  $block->content = new View("exif_gps_static_sidebar.html");
                  if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "roadmap";
                  if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "satellite";
                  if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "hybrid";
                  if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "terrain";
                }
                $block->content->latitude = $tagsGPS->latitude;
                $block->content->longitude = $tagsGPS->longitude;
                break;
              }
            }
          }
        }
      } elseif ( ($theme->tag()) && (module::is_active("tagsmap") && module::is_active("tag")) ) {
        // If the current page belongs to a tag, check and see if the tag has GPS coordinates.
        $tagsGPS = ORM::factory("tags_gps")->where("tag_id", "=", $theme->tag()->id)->find();
        if ($tagsGPS->loaded()) {
          $block = new Block();
          $block->css_id = "g-exif-gps-sidebar";
          $block->title = t("Location");
          if (module::get_var("exif_gps", "sidebar_mapformat") == 1) {
            $block->content = new View("exif_gps_dynamic_sidebar.html");
            if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "ROADMAP";
            if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "SATELLITE";
            if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "HYBRID";
            if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "TERRAIN";
          } else {
            $block->content = new View("exif_gps_static_sidebar.html");
            if (module::get_var("exif_gps", "sidebar_maptype") == 0) $block->content->sidebar_map_type = "roadmap";
            if (module::get_var("exif_gps", "sidebar_maptype") == 1) $block->content->sidebar_map_type = "satellite";
            if (module::get_var("exif_gps", "sidebar_maptype") == 2) $block->content->sidebar_map_type = "hybrid";
            if (module::get_var("exif_gps", "sidebar_maptype") == 3) $block->content->sidebar_map_type = "terrain";
          }
          $block->content->latitude = $tagsGPS->latitude;
          $block->content->longitude = $tagsGPS->longitude;
        }
      }
      break;
    }
    return $block;
  }
}
