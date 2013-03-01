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
class pages_block_Core {
  static function get_site_list() {
    return array("pages_block" => t("Pages Links"));
  }

  static function get($block_id, $theme) {
    $block = "";

    switch ($block_id) {
    case "pages_block":

      // Create a new block with a list of all Pages and their links.

      // Query the database for all existing pages.
      //  If at least one page exists, display the sidebar block.
      $query = ORM::factory("static_page");
      $pages = $query->order_by("title", "ASC")->find_all();
      if (count($pages) > 0) {

        // Loop through each page and generate an HTML list of their links and titles.
        $content = "<ul id=\"g-pages-list\">";
        foreach ($pages as $one_page) {
          $content .= "<li style=\"clear: both;\"><a href=\"" . url::site("pages/show/" . $one_page->name) . "\">" . t($one_page->title) . "</a></li>";
        }
        $content .= "</ul>";

        // Make a new sidebar block.
        $block = new Block();
        $block->css_id = "g-pages";
        $block->title = t("Pages");
        $block->content = new View("pages_sidebar.html");
        $block->content->links = $content;
      }
      break;
    }
    return $block;
  }
}
