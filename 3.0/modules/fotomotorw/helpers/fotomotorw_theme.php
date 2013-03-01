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
 
class fotomotorw_theme_Core {
  static function head($theme) {
    // Load fotomoto's js file on photo and album pages.
    if(($theme->page_subtype == "photo") || ($theme->page_subtype == "album")) {
      return html::script('http://widget.fotomoto.com/stores/script/' . module::get_var("fotomotorw", "fotomoto_site_key") . '.js?api=true');
    }
  }

  static function resize_bottom($theme) {
    // Create a new block to use to display Fotomoto buy links below the photo.
    $block = new Block;
    $block->css_id = "g-fotomoto";
    $block->anchor = "fotomoto";

    // Generate an array of links to display below photos.
    $link_array = array();
    $counter = 0;
    if (module::get_var("fotomotorw", "fotomoto_buy_prints")) {
      $link_array[$counter] = array("100", "Buy Prints");
      $counter++;
    }
    if (module::get_var("fotomotorw", "fotomoto_buy_cards")) {
      $link_array[$counter] = array("300", "Buy Cards");
      $counter++;
    }
    if (module::get_var("fotomotorw", "fotomoto_buy_download")) {
      $link_array[$counter] = array("400", "Download");
      $counter++;
    }
    if (module::get_var("fotomotorw", "fotomoto_share_ecard")) {
      $link_array[$counter] = array("200", "Send eCard");
      $counter++;
    }
    if (module::get_var("fotomotorw", "fotomoto_share_facebook")) {
      $link_array[$counter] = array("201", "Share on Facebook");
      $counter++;
    }
    if (module::get_var("fotomotorw", "fotomoto_share_twitter")) {
      $link_array[$counter] = array("202", "Share on Twitter");
      $counter++;
    }
    if (module::get_var("fotomotorw", "fotomoto_share_digg")) {
      $link_array[$counter] = array("203", "Share on Digg");
      $counter++;
    }

    $view = new View("fotomotorw_photo_block.html");
    $view->details = $link_array;
    $block->content = $view;
    return $block;
  }

  static function album_bottom($theme) {
    // Add some javascript to the bottom of album pages.
    $block = new Block;
    $block->css_id = "g-fotomoto";
    $block->anchor = "fotomoto"; 
    $view = new View("fotomotorw_album_block.html");
    $block->content = $view;
    return $block;
  }
}
