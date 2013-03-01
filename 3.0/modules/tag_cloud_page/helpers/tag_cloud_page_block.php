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
class tag_cloud_page_block_Core {
  static function get_site_list() {
    // Create a sidebar block to link to the cloud page.
    return array("tag_cloud_page" => t("Tag Cloud Page Link"));
  }

  static function get($block_id, $theme) {
    // Generate the sidebar block for linking to the tag cloud page.
    $block = "";
    switch ($block_id) {
    case "tag_cloud_page":
      $block = new Block();
      $block->css_id = "g-tag-cloud-page";
      $block->title = t("Tag Cloud");
      $block->content = new View("tag_cloud_page_block.html");

      break;
    }
    return $block;
  }
}
