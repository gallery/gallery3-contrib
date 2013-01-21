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
class custom_menus_event_Core {
  static function admin_menu($menu, $theme) {
    // Add a settings link to the admin menu.
    $menu->get("content_menu")
      ->append(Menu::factory("link")
               ->id("custom_menus")
               ->label(t("Custom Menus Manager"))
               ->url(url::site("admin/custom_menus")));
  }

  static function site_menu($menu, $theme) {
    // Add user definied menu and sub-menu items to the site menu.
    $existing_menu = ORM::factory("custom_menu")
                     ->where("parent_id", "=", "0")
                     ->order_by("order_by", "DESC")
                     ->find_all();
    if (count($existing_menu) > 0) {
      foreach ($existing_menu as $one_menu) {
        if ($one_menu->url == "") {
          $menu->add_after("home", $new_menu = Menu::factory("submenu")
             ->id("custom_menus-" . $one_menu->id)
             ->label(t($one_menu->title)));
          custom_menus_event::add_sub_menus($one_menu->id, $new_menu);
        } else {
          $menu->add_after("home", Menu::factory("link")
             ->id("custom_menus-" . $one_menu->id)
             ->label(t($one_menu->title))
             ->url($one_menu->url));
        }
      }
    }
  }

  function add_sub_menus($parent_id, $parent_menu) {
    // Populate the menu bar with any sub-menu items on the current menu ($parent_menu).
    $existing_menu = ORM::factory("custom_menu")
                     ->where("parent_id", "=", $parent_id)
                     ->order_by("order_by", "ASC")
                     ->find_all();
    if (count($existing_menu) > 0) {
      foreach ($existing_menu as $one_menu) {
        if ($one_menu->url == "") {
          $parent_menu->append($new_menu = Menu::factory("submenu")
             ->id("custom_menus-" . $one_menu->id)
             ->label(t($one_menu->title)));
          custom_menus_event::add_sub_menus($one_menu->id, $new_menu);
        } else {
            $parent_menu->append(Menu::factory("link")
                              ->id("custom_menus-" . $one_menu->id)
                              ->label(t($one_menu->title))
                              ->url($one_menu->url));
        }
      }
    }
  }
}
