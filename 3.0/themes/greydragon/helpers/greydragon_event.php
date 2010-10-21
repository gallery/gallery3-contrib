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
class greydragon_event_Core {

  static function site_menu($menu, $theme) {
    $submenu = $menu->get("add_menu");
    if (!empty($submenu)) {
      $item = $submenu->get("add_photos_item");
      if (!empty($item)) { $item->css_class("ui-icon-plus"); }

      $item = $submenu->get("add_album_item");
      if (!empty($item)) { $item->css_class("ui-icon-note"); }
    }

    $submenu = $menu->get("options_menu");
    if (!empty($submenu)) {
      $item = $submenu->get("edit_item");
      if (!empty($item)) { $item->css_class("ui-icon-pencil"); }

      $item = $submenu->get("edit_permissions");
      if (!empty($item)) { $item->css_class("ui-icon-key"); }
    }
  }
}
