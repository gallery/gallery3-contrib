<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
 
class basket_plus_block_Core {
  static function get_site_list() {
    return array("shopping" => t("Basket"));
  }

  static function get($block_id, $theme) {
    $block = "";
    switch ($block_id) {
      case "shopping":
        $block = new Block();
        $block->css_id = "g-view-basket";
        $block->title = t("Basket");
        $block->content = new View("basket_side_bar.html");
        $block->content->basket = Session_Basket::get();
        break;
    }
    return $block;
  }
}