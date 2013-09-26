<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
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


class ratings_block_Core {

  static function get_site_list() {
    return array("ratings" => t("Ratings Block"));
  }

  static function get($block_id, $theme) {
    if(module::get_var("ratings","showinsidebar") == 1){
      $block = "";
      if($theme->item()) {
	$item = $theme->item;
	if($item->is_photo() || $item->is_movie()){
	  switch ($block_id) {
	    case "ratings":
	    // Make a new sidebar block.
	    $block = new Block();
	    $block->css_id = "g-ratings";
	    $block->title = t("Ratings");
	    $block->content = new View("ratings_block.html");
	    $block->content->imageword = module::get_var("ratings","imageword");
	    break;
	    }
	  return $block;
	}
      }
    }
  }
}
