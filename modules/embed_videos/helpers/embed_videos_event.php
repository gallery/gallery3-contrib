<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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

class embed_videos_event_Core {

  static function item_created($item) {

    if ($item->type == "embedded_video") {
      // Build our thumbnail/resizes.
      try {
        graphics::generate($item);
      } catch (Exception $e) {
        log::error("graphics", t("Couldn't create a thumbnail or resize for %item_title",
        array("item_title" => $item->title)),
        html::anchor($item->abs_url(), t("details")));
        Kohana_Log::add("error", $e->getMessage() . "\n" . $e->getTraceAsString());
      }

      // If the parent has no cover item, make this it.
      $parent = $item->parent();
      if (access::can("edit", $parent) && $parent->album_cover_item_id == null)  {
        item::make_album_cover($item);
      }
    }
  }
  static function item_deleted($item) {
    ORM::factory("embedded_video")
    ->where("item_id", "=", $item->id)
    ->find()
    ->delete();
  }
  static function site_menu($menu, $theme) {
    $item = $theme->item();

    if ($can_add = $item && access::can("add", $item)) {
      $menu->get("add_menu")
      ->append(Menu::factory("dialog")
      ->id("embed_add")
      ->label(t("Embed Video"))
      ->url(url::site("form/add/embedded_videos/$item->id")));
    }
  }
}
