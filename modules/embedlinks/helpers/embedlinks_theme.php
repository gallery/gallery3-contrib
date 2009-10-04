<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class embedlinks_theme_Core {
  static function sidebar_blocks($theme) {
    // If the current item is an album and if "In Page" links are enabled then
    //  display links to the current album in the theme sidebar.
    if ($theme->item()->is_album() && module::get_var("embedlinks", "InPageLinks")) {
      $block = new Block();
      $block->css_id = "g-metadata";
      $block->title = t("Links");
      $block->content = new View("embedlinks_album_block.html");
      return $block;
    }    
  }
  
  static function sidebar_bottom($theme) {
    // If displaying links in a dialog box is enabled then 
    //   insert buttons into the bottom of the side bar
    //   to open up the dialog window.  
    if (module::get_var("embedlinks", "DialogLinks")) {
      $block = new Block();
      $block->css_id = "g-metadata";
      $block->title = t("Link To This Page:");
      $block->content = new View("embedlinks_sidebar.html");
      return $block;
    }
  }
  
  static function photo_bottom($theme) {
    // If the current item is a photo and displaying "In Page" links
    //   is enabled, then insert HTML/BBCode links into the bottom
    //   of the page. 
    if (module::get_var("embedlinks", "InPageLinks")) {
      $block = new Block();
      $block->css_id = "g-metadata";
      $block->title = t("Links");
      $block->content = new View("embedlinks_photo_block.html");
      return $block;
    }
  }
}
