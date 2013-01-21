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
class tag_cloud_html5_block {
  static function get_site_list() {
    return array(
      "tag_cloud_html5_site" => (t("Tag cloud")." HTML5"));
  }

  static function get($block_id, $theme) {
    $block = "";
    switch ($block_id) {
    case "tag_cloud_html5_site":
      // load settings
	    $options = module::get_var("tag_cloud_html5", "options_sidebar", null);
	    $maxtags = module::get_var("tag_cloud_html5", "maxtags_sidebar", null);
      $showlink = module::get_var("tag_cloud_html5", "show_wholecloud_link", null);
      $showaddtag = module::get_var("tag_cloud_html5", "show_add_tag_form", null);
      $width = module::get_var("tag_cloud_html5", "width_sidebar", null);
      $height = module::get_var("tag_cloud_html5", "height_sidebar", null);

	    // make the block
      $block = new Block();
      $block->css_id = "g-tag";
      $block->title = t("Tag cloud");
      $block->content = new View("tag_cloud_html5_block.html");
      $block->content->cloud = tag::cloud($maxtags);
      $block->content->options = $options;
      $block->content->width = $width;
      $block->content->height = $height;
      
      // add the 'View whole cloud' link if needed
      if ($showlink) {
        $block->content->wholecloud_link = "<a href=".url::site("tag_cloud/").">".t("View whole cloud")."</a>";
      } else {
        $block->content->wholecloud_link = "";
      }

      // add the 'Add tag' form if needed
      if ($theme->item() && $theme->page_subtype() != "tag" && access::can("edit", $theme->item()) && $showaddtag) {
        $controller = new Tags_Controller();
        $block->content->form = tag::get_add_form($theme->item());
      } else {
        $block->content->form = "";
      }

      break;
    }
    return $block;
  }
}
