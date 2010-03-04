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

    // Make sure the current page belongs to an item.
    if (!$theme->item()) {
      return;
    }

    switch ($block_id) {
    case "exif_gps_map":
      // Check and see if the item has exif coordinates associated with it.
      $record = ORM::factory("exif_coordinate")->where("item_id", "=", $theme->item->id)->find();
      if ($record->loaded()) {
        $block = new Block();
        $block->css_id = "g-exif-gps-sidebar";
        $block->title = t("Location");
        $block->content = new View("exif_gps_sidebar.html");
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
              $block->content = new View("exif_gps_sidebar.html");
              $block->content->latitude = $tagsGPS->latitude;
              $block->content->longitude = $tagsGPS->longitude;
              break;
            }
          }
        }
      }
      break;
    }
    return $block;
  }
}
