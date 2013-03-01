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
class about_this_album_block_Core {

  static function get_site_list() {
    return array("aboutthisalbum" => t("About This Album"));
  }

  static function get($block_id, $theme) {
    switch ($block_id) {
    case "aboutthisalbum":
      $item = $theme->item;
 	  if ((!$item) or (!$theme->item->is_album())) {
        return ""; 
	  }
      if ($theme->item->is_album()) {
        $block = new Block();
	    $block->css_id = "g-about-this-album";
	    $block->content = new View("about_this_album.html");

 	    if ($theme->item()->id == item::root()->id) {
		  $block->title = t("About this Site");
		  $block->content->album_count = ORM::factory("item")->where("type", "=", "album")->where("id", "<>", 1)->count_all();
          $block->content->photo_count = ORM::factory("item")->where("type", "=", "photo")->count_all();
          $block->content->vcount = Database::instance()->query("SELECT SUM({items}.view_count) as c FROM {items} WHERE type=\"photo\"")->current()->c;
		} Else {
           $block->title = t("About this Album");
           $block->content->album_count = $item->descendants_count(array(array("type", "=", "album"))); 
           $block->content->photo_count = $item->descendants_count(array(array("type", "=", "photo"))); 
  	       // $block->content->vcount= $theme->item()->view_count; 
  	       $descds = $item->descendants();
		   $descds_view = 0;
		   foreach ($descds as $descd) {
		     if ($descd->is_photo()) {
		       $descds_view += $descd->view_count;
			 }
		   }
		   $block->content->vcount = $descds_view;
          if ($item->description) {
            $block->content->description = html::clean($item->description);
          }
		}


        $all_tags = ORM::factory("tag")
        ->join("items_tags", "items_tags.tag_id", "tags.id")
        ->join("items", "items.id", "items_tags.item_id", "LEFT")
        ->where("items.parent_id", "=", $item->id)
        ->order_by("tags.id", "ASC")
        ->find_all();
        if (count($all_tags) > 0) {
         $block->content->all_tags = $all_tags;
        }
	  }
      break;
    }
    return $block;
  }
}
