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
class rwinfo_block_Core {
  static function get_site_list() {
    return array("metadata" => t("rWInfo"));
  }

  static function get($block_id, $theme) {
    $block = "";
    switch ($block_id) {
    case "metadata":
      if ($theme->item()) {
        // rWatcher Edit: Don't display on root album.
        if ($theme->item->id == 1) {
          return "";
        }
        // End rWatcher Edit

        $block = new Block();
        $block->css_id = "g-metadata";

        // rWatcher Edit:  Add Movie Info Option
        //$block->title = $theme->item()->is_album() ? t("Album Info") : t("Photo Info");
        $block_title = "";
        if ($theme->item->is_album()) {
          $block_title = t("Album Info");
        } else if ($theme->item->is_movie()) {
          $block_title = t("Movie Info");
        } else {
          $block_title = t("Photo Info");
        }
        $block->title = $block_title;
        // End rWatcher Edit

        // rWatcher Edit:  File Name change.
        $block->content = new View("rwinfo_block.html");
      }
      break;
    }
    return $block;
  }
}