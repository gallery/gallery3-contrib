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
class developer_event_Core {
  static function admin_menu($menu, $theme) {
    $developer_menu = Menu::factory("submenu")
        ->id("developer_menu")
      ->label(t("Developer tools"));
    $menu->append($developer_menu);

    $developer_menu
      ->append(Menu::factory("link")
          ->id("generate_menu")
          ->label(t("Generate module"))
          ->url(url::site("admin/developer/module")))
      ->append(Menu::factory("link")
          ->id("generate_data")
          ->label(t("Generate test data"))
          ->url(url::site("admin/developer/test_data")))
      ->append(Menu::factory("link")
          ->id("mptt_tree_menu")
          ->label(t("MPTT tree"))
          ->url(url::site("admin/developer/mptt")));

    $csrf = access::csrf_token();
    if (Session::instance()->get("profiler", false)) {
      $developer_menu->append(
        Menu::factory("link")
        ->id("scaffold_profiler")
        ->label(t("Profiling off"))
        ->url(url::site("admin/developer/session/profiler?value=0&csrf=$csrf")));
    } else {
      $developer_menu->append(
        Menu::factory("link")
        ->id("scaffold_profiler")
        ->label(t("Profiling on"))
        ->url(url::site("admin/developer/session/profiler?value=1&csrf=$csrf")));
    }

    if (Session::instance()->get("debug", false)) {
      $developer_menu->append(
        Menu::factory("link")
        ->id("scaffold_debugger")
        ->label(t("Debugging off"))
        ->url(url::site("admin/developer/session/debug?value=0&csrf=$csrf")));
    } else {
      $developer_menu->append(
        Menu::factory("link")
        ->id("scaffold_debugger")
        ->label(t("Debugging on"))
        ->url(url::site("admin/developer/session/debug?value=1&csrf=$csrf")));
    }
  }
}
