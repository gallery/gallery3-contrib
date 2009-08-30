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

class basket_event_Core{
  /**
   * adds the shopping basket administration controls to the admin menu
   */
  static function admin_menu($menu, $theme){

    $menu->add_after("users_groups",
      $basket_menu = Menu::factory("submenu")
        ->id("basket_menu")
        ->label(t("Basket")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("configure")
        ->label(t("Configure"))
        ->url(url::site("admin/configure")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("product_line")
        ->label(t("Product Lines"))
        ->url(url::site("admin/product_lines")));

  }
}