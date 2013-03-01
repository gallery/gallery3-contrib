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

class fotomotorw_event_Core {
  static function admin_menu($menu, $theme) {
    // Display an option under the admin Settings menu.
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
        ->id("fotomotorw_menu")
        ->label(t("Fotomoto"))
        ->url(url::site("admin/fotomotorw")));
  }

  static function context_menu($menu, $theme, $item) {
    // Add a "Buy Prints" option to the photo's thumbnail menu.
    if ($item->type == "photo") {
      $menu->get("options_menu")
        ->append(Menu::factory("link")
                 ->id("fotomotorw")
                 ->label(t("Buy Prints"))
                 ->url("javascript:showFotomotoDialog(100, '" . url::abs_site("fotomotorw/resize/" . md5($item->created) . "/{$item->id}") . "');")
                 ->css_class("g-print-fotomotorw-link ui-icon-print"));
    }
  }
}
