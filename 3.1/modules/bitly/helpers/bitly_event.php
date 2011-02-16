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
class bitly_event_Core {

  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
        ->id("bitly_menu")
        ->label(t("bit.ly"))
        ->url(url::site("admin/bitly")));
  }

  static function site_menu($menu, $theme) {
    $link = ORM::factory("bitly_link")->where("item_id", "=", $theme->item->id)->find();
    if (!$link->loaded() && $theme->item->owner->id == identity::active_user()->id) {
      $menu->get("options_menu")
        ->append(Menu::factory("link")
                 ->id("bitly")
                 ->label(t("Shorten link with bit.ly"))
                 ->url(url::site("bitly/shorten/{$theme->item->id}?csrf={$theme->csrf}"))
                 ->css_id("g-bitly-shorten")
                 ->css_class("g-bitly-shorten"));
    }
  }

  static function context_menu($menu, $theme, $item) {
    $link = ORM::factory("bitly_link")->where("item_id", "=", $item->id)->find();
    if (!$link->loaded() && $theme->item->owner->id == identity::active_user()->id) {
      $menu->get("options_menu")
        ->append(Menu::factory("link")
                 ->id("bitly")
                 ->label(t("Shorten link with bit.ly"))
                 ->url(url::site("bitly/shorten/{$item->id}?csrf={$theme->csrf}"))
                 ->css_class("g-bitly-shorten ui-icon-link"));
    }
  }

  static function info_block_get_metadata($block, $item) {
    $link = ORM::factory("bitly_link")->where("item_id", "=", $item->id)->find();
    if ($link->loaded()) {
      $info = $block->content->metadata;
      $info["bitly_url"] = array(
         "label" => t("bit.ly url:"),
         "value" => bitly::url($link->hash)
      );
      $block->content->metadata = $info;
    }
  }

}
