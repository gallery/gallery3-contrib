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
class basket_plus_theme_Core {

  static function head($theme) {
    $theme->css("basket_plus.css");
  }

  static function header_top($theme) {

    if (!basket_plus::getBasketVar(USE_SIDE_BAR_ONLY))
    {
      $view = new View("basket.html");

      $view->basket = Session_Basket::get();
      return $view->render();
    }
    return "";
  }

  static function admin_head($theme) {
    if (strpos(Router::$current_uri, "admin/product_lines") !== false) {
      $theme->script("gallery.panel.js");
    }
  }
  static function photo_top($theme){
    if (!basket_plus::getBasketVar(USE_SIDE_BAR_ONLY))
    {
        if ( bp_product::isForSale($theme->item()->id)){
        $view = new View("add_to_basket.html");

        $view->item = $theme->item();

        return $view->render();
      }
    }
    return "";
  }
}
