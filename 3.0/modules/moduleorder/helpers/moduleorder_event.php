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
class moduleorder_event_Core {
  static function module_change($changes) {
    // Gallery version must be >= 32
    if (module::get_version("gallery") < 32) {
      site_status::warning(
        t("The module 'Module Order' requires Gallery core version of 32 or higher."),
        "moduleorder_needs_higherversion");
    } else {
      site_status::clear("moduleorder_needs_higherversion");
    }
  }
  
  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("moduleorder_menu")
               ->label(t("Module order"))
               ->url(url::site("admin/moduleorder")));
  }
}
