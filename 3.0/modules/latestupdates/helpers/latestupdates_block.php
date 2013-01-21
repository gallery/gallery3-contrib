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
class latestupdates_block_Core {
  static function get_site_list() {
    return array("latestupdates" => t("Latest Updates"));
  }

  static function get($block_id, $theme) {
    $block = "";

    switch ($block_id) {
    case "latestupdates":

      // Make a new sidebar block.
      $block = new Block();
      $block->css_id = "g-latest-updates";
      $block->title = t("Latest Updates");
      $block->content = new View("latestupdates_block.html");

      if (!$theme->item()) {
        $block->content->update_links = array(
          "Entire Gallery" => url::site("latestupdates/updates"));
      } else {
        // Determine the ID# of the current album.
        $albumID = $theme->item->is_album() ? $theme->item->id : $theme->item->parent_id;
        $block->content->update_links = array(
          "Entire Gallery" => url::site("latestupdates/updates"),
          "This Album" => url::site("latestupdates/albums/$albumID")
        );
      }
      break;
    }
    return $block;
  }
}
