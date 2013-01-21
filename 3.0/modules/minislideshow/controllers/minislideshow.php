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

class MiniSlideShow_Controller extends Controller {
  public function showslideshow($item_id) {
    // Generate the Dialog Box to display the slideshow in.
    $view = new View("minislideshow_dialog.html");
    
    // Figure out if the user has the necessary privlidges to view the album.
    $item = ORM::factory("item", $item_id);
    if ($item->is_album()) {
      $view->item_id = $item->id;
    } else {
      $view->item_id = $item->parent_id;
      $item = ORM::factory("item", $item_id);
    }
    access::required("view", $item);
    
    // Generate additional slideshow parameters from database values.
    $slideshow_params = "";
    if (module::get_var("minislideshow", "shuffle") != "") {
      $slideshow_params = $slideshow_params . "&shuffle=" . module::get_var("minislideshow", "shuffle");
    }
    if (module::get_var("minislideshow", "dropshadow") != "") {
      $slideshow_params = $slideshow_params . "&showDropShadow=" . module::get_var("minislideshow", "dropshadow");
    }
    if (module::get_var("minislideshow", "show_title") != "") {
      $slideshow_params = $slideshow_params . "&showTitle=" . module::get_var("minislideshow", "show_title");
    }
    if (module::get_var("minislideshow", "trans_in_type") != "") {
      $slideshow_params = $slideshow_params . "&transInType=" . module::get_var("minislideshow", "trans_in_type");
    }
    if (module::get_var("minislideshow", "trans_out_type") != "") {
      $slideshow_params = $slideshow_params . "&transOutType=" . module::get_var("minislideshow", "trans_out_type");
    }
    if (module::get_var("minislideshow", "mask") != "") {
      $slideshow_params = $slideshow_params . "&" . module::get_var("minislideshow", "mask") . "=true";
    }
    if (module::get_var("minislideshow", "use_full_image") != "") {
      $slideshow_params = $slideshow_params . "&useFull=true";
      if (module::get_var("minislideshow", "use_full_image") == "2") {
        $slideshow_params = $slideshow_params . "&useResizes=true";
      }
    }
    if (module::get_var("minislideshow", "delay") != "") {
      $slideshow_params = $slideshow_params . "&delay=" . module::get_var("minislideshow", "delay");
    }    
    $view->slideshow_params = $slideshow_params;
    
    // Display the slideshow.
    print $view;
  }  
}
