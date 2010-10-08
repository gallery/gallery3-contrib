<?php defined("SYSPATH") or die("No direct script access.");/**
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
class dynamic_block_Core {
  static function get_site_list() {
    return array("dynamic" => t("Dynamic Albums"));
  }

  static function get($block_id) {
    switch ($block_id) {
    case "dynamic":
      $albums = array();
      foreach (array("updates", "popular") as $album) {
        $album_defn = unserialize(module::get_var("dynamic", $album));
        if ($album_defn->enabled) {
          $albums[$album] = $album_defn->title;
        }
      }

      if (!empty($albums)) {
        $block = new Block();
        $block->css_id = "g-dynamic";
        $block->title = t("Dynamic Albums");
        $block->content = new View("dynamic_block.html");
        $block->content->albums = $albums;
        return $block;
      }
    }
    return "";
  }

}
