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

class star_event_Core {

  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->label(t("Star"))
               ->url(url::site("admin/star")));
  }

  static function site_menu($menu, $theme, $item_css_selector) {
    $item = $theme->item();

    if (!empty($item) && star::can_be_starred($item) && star::can_star($item)) {
      $csrf = access::csrf_token();
      $link = self::_get_star_link_data($item);

      $menu->get("options_menu")
        ->append(Menu::factory("ajax_link")
                 ->label($link["text"])
                 ->ajax_handler("function(data) { window.location.reload() }")
                 ->url(url::site("display/".$link["action"]."/$item->id?csrf=$csrf")));
    }
  }

  static function context_menu($menu, $theme, $item, $thumb_css_selector) {
    if (star::can_be_starred($item) && star::can_star($item)) {
      $csrf = access::csrf_token();
      $link = self::_get_star_link_data($item);

      $menu
        ->get("options_menu")
        ->append(Menu::factory("ajax_link")
                 ->label($link["text"])
                 ->ajax_handler("function(data) { window.location.reload() }")
                 ->url(url::site("display/".$link["action"]."/$item->id?csrf=$csrf")));
    }
  }

  /**
   * Returns some data used to create a star link.
   *
   * @param Item_Model $item  the related item
   * @return array
   */
  private static function _get_star_link_data(Item_Model $item) {
    if (star::is_starred($item)) {
      $action = "unstar";
      $action_label = "Unstar";
    }
    else {
      $action = "star";
      $action_label = "Star";
    }

    switch ($item->type) {
    case "movie":
      $item_type_label = "movie";
      break;
    case "album":
      $item_type_label = "album";
      break;
    default:
      $item_type_label = "photo";
      break;
    }

    $label = t("$action_label this $item_type_label");

    return array("text" => $label, "action" => $action);
  }
}
