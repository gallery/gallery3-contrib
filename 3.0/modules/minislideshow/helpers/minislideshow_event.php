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

class minislideshow_event_Core {
  static function admin_menu($menu, $theme) {
    // Add a menu option to the admin screen for configuring the slideshow.
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("minislideshow")
               ->label(t("MiniSlide Show settings"))
               ->url(url::site("admin/minislideshow")));
  }

  static function module_change($changes) {
    // Display a warning message if the RSS module is not installed.
    if (!module::is_active("rss") || in_array("rss", $changes->deactivate)) {
      site_status::warning(
        t("The MiniSlide Show module requires the RSS module.  " .
          "<a href=\"%url\">Activate the RSS module now</a>",
          array("url" => url::site("admin/modules"))),
        "minislideshow_needs_rss");
    } else {
      site_status::clear("minislideshow_needs_rss");
    }
  }

  static function pre_deactivate($data) {
    if ($data->module == "rss") {
      $data->messages["warn"][] = t("The MiniSlide Show module requires the RSS module.");
    }
  }

  static function album_menu($menu, $theme) {
    // Add an option to access the slideshow from the album view.
    if ($theme->item()->children_count(array(array("type", "=", "photo")))) {
      $menu
        ->append(Menu::factory("link")
                 ->id("minislideshow")
                 ->label(t("View MiniSlide Show"))
                 ->url(url::site("minislideshow/showslideshow/" . $theme->item()->id))
                 ->css_class("g-dialog-link")
                 ->css_id("g-mini-slideshow-link"));
    }
  }

  static function photo_menu($menu, $theme) {
    // Add an option to access the slideshow from the photo view.
    $menu
      ->append(Menu::factory("link")
               ->id("minislideshow")
               ->label(t("View MiniSlide Show"))
               ->url(url::site("minislideshow/showslideshow/" . $theme->item()->id))
               ->css_class("g-dialog-link")
               ->css_id("g-mini-slideshow-link"));
  }
}
