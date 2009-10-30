<?php defined("SYSPATH") or die("No direct script access.");/**
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
class register_event {
  static function admin_menu($menu, $theme) {
    $menu->get("identity_menu")
      ->append( Menu::factory("link")
      ->id("register_users")
      ->label(t("Self registration"))
      ->url(url::site("admin/register")));

    return $menu;
  }

  static function site_menu($menu, $theme) {
    if (identity::active_user()->guest) {
      $menu->append( Menu::factory("dialog")
                 ->id("register_users")
                 ->label(t("Register"))
                 ->url(url::site("register")));
    }

    return $menu;
  }

  static function check_username_exists($data) {
    $user = ORM::factory("pending_user")
      ->where("name", $data->name)
      ->find();
      $valid = !$user->loaded;
    $data->exists |= $user->loaded;
  }
}
