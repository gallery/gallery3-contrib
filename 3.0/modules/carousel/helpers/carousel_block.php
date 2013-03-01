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
class carousel_block_Core {
  static function get_site_list() {
    return array(
		"carousel_recent" => t("Recent items carousel"),
		"carousel_popular" => t("Popular items carousel"),
		"carousel_random" => t("Random items carousel"));
	}

  static function get($block_id, $theme) {
    $block = "";
    switch ($block_id) {
    case "carousel_recent":
      if (module::get_var("carousel", "onalbum2") && $theme->page_type == "collection"  || 
		  module::get_var("carousel", "onphoto2") && $theme->page_type == "item") {
        $block = new Block();
        $block->css_id = "g-carousel-rec";
        $block->title = module::get_var("carousel", "title2", "Recent items");
	    $block->content = new View("carousel_recent.html");
	  }
      break;
    case "carousel_popular":
      if (module::get_var("carousel", "onalbum3") && $theme->page_type == "collection"  ||
	  	  module::get_var("carousel", "onphoto3") && $theme->page_type == "item") {
        $block = new Block();
        $block->css_id = "g-carousel-pop";
        $block->title = module::get_var("carousel", "title3", "Popular items");
	    $block->content = new View("carousel_popular.html");
	  }
      break;
    case "carousel_random":
      if (module::get_var("carousel", "onalbum") && $theme->page_type == "collection"  || 
		  module::get_var("carousel", "onphoto") && $theme->page_type == "item") {
        $block = new Block();
        $block->css_id = "g-carousel-ran";
        $block->title = module::get_var("carousel", "title", "Random items");
	    $block->content = new View("carousel_random.html");
	  }
      break;
    }
    return $block;
  }
}