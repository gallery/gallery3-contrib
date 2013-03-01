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
class movie_tools_event_Core {
  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("movie_tools")
               ->label(t("Movie tools"))
               ->url(url::site("admin/movie_tools")));
  }

  static function movie_types_by_extension($types_by_extension_wrapper) {
    $formats = movie_tools::get_formats();
    foreach ($formats as $id => $data) {
      if (module::get_var("movie_tools", "allow_$id", false)) {
        $types_by_extension_wrapper->types_by_extension = array_merge_recursive(
          $types_by_extension_wrapper->types_by_extension, $data["types"]);
      }
    }
    if ($custom_formats = module::get_var("movie_tools", "custom_formats", "")) {
      $types_by_extension_wrapper->types_by_extension = array_merge_recursive(
        $types_by_extension_wrapper->types_by_extension, json_decode($custom_formats, true));
    }
  }
}
