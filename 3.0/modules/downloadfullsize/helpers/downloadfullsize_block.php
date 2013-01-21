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
class downloadfullsize_block_Core {
  static function get_site_list() {
    return array("downloadfullsize" => t("Download Item"));
  }

  static function get($block_id, $theme) {
    $item = $theme->item;
    switch ($block_id) {
    case "downloadfullsize":

      // If Item is movie then...
      if ($item && $item->is_movie() && access::can("view_full", $item)) {
        $block = new Block();
        $block->css_id = "g-download-fullsize";
        $block->title = t("Download Movie");
        $block->content = new View("downloadfullsize_block.html");
        return $block;
      }

      // If Item is photo then...
      if ($item && $item->is_photo() && access::can("view_full", $item)) {
        $block = new Block();
        $block->css_id = "g-download-fullsize";
        $block->title = t("Download Photo");
        $block->content = new View("downloadfullsize_block.html");
        return $block;
      }
    }
    return "";
  }

}
