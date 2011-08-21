<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class embedlinks_block_Core {
  static function get_site_list() {
    return array("embed_links_dialog" => t("Embed Links Dialog"), "embed_links_album" => t("Embed Links Album"));
  }

  static function get($block_id, $theme) {
    $block = "";

    if (!$theme->item()) {
      return;
    }

    switch ($block_id) {
    case "embed_links_dialog":
      // Display dialog buttons in the sidebar.
      $block = new Block();
      $block->css_id = "g-embed-links-sidebar";
      $block->title = t("Link To This Page");
      $block->content = new View("embedlinks_sidebar.html");
      break;

    case "embed_links_album":
      // If the current item is an album and if "In Page" links are enabled then
      //  display links to the current album in the theme sidebar.
      if ($theme->item()->is_album() && module::get_var("embedlinks", "InPageLinks")) {
        $block = new Block();
        $block->css_id = "g-embed-links-album-sidebar";
        $block->title = t("Links");
        $block->content = new View("embedlinks_album_block.html");
      }
      break;
    }

    return $block;
  }
}
