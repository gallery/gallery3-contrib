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
class pages_event_Core {
  static function admin_menu($menu, $theme) {
    // Add a settings link to the admin menu.
    $menu->get("content_menu")
      ->append(Menu::factory("link")
               ->id("pages")
               ->label(t("Pages Settings"))
               ->url(url::site("admin/pages")));
  }

  static function site_menu($menu, $theme) {
    $menu_pages = ORM::factory("static_page")
                  ->where("display_menu", "=", true)
                  ->order_by("title", "DESC")
                  ->find_all();
    if (count($menu_pages) > 0) {
      foreach ($menu_pages as $one_page) {
        $menu->add_after("home", Menu::factory("link")
             ->id("pages-" . $one_page->id)
             ->label(t($one_page->title))
             ->url(url::site("pages/show/" . $one_page->name)));
      }
    }
  }
}
