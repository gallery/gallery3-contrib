<?php defined("SYSPATH") or die("No direct script access.");/**
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
class register_event {
  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append( Menu::factory("link")
      ->id("register_users")
      ->label(t("User registration"))
      ->url(url::site("admin/register")));
  }

  static function user_menu($menu, $theme) {
    $user = identity::active_user();
    if ($user->guest) {
      $menu->append(Menu::factory("dialog")
                    ->id("user_menu_register")
                    ->css_id("g-register-menu")
                    ->url(url::site("register"))
                    ->label(t("Register")));
    }
  }
}
