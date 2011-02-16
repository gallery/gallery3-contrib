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
class tagsinalbum_block_Core {
  static function get_site_list() {
    return array("tagsinalbum" => t("Tags In Album"));
  }

  static function get($block_id, $theme) {
    $block = "";

    switch ($block_id) {
      case "tagsinalbum":
      if (($theme->item) && ($theme->item->is_album())) {
        $item = $theme->item;
        $all_tags = ORM::factory("tag")
            ->join("items_tags", "items_tags.tag_id", "tags.id")
            ->join("items", "items.id", "items_tags.item_id", "LEFT")
            ->where("items.parent_id", "=", $item->id)
            ->order_by("tags.id", "ASC")
            ->find_all();
        if (count($all_tags) > 0) {
          $block = new Block();
          $block->css_id = "g-tags-in-album-block";
          $block->title = t("In this album");
          $block->content = new View("tagsinalbum_sidebar.html");
          $block->content->all_tags = $all_tags;
        }
      }
      break;
    }
    return $block;
  }
}
