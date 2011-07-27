<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class item_links_theme_Core {
  static function head($theme) {
    // If the current page is an item, and if it's in the item_links table,
    //   then redirect to the specified web page.
    if ($theme->item()) {
      $item_url = ORM::factory("item_link")
        ->where("item_id", "=", $theme->item->id)
        ->find_all();
      if (count($item_url) > 0) {
        url::redirect($item_url[0]->url);
      }
    }
    return;
  }
}
