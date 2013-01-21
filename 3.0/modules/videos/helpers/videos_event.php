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
 
// rWatcher Edit:  This file used to be server_add_event.php.
// All occurences of server_add have been replaced with videos.
// Additionally, several new functions have been added.
class videos_event_Core {
  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("videos")
               ->label(t("Videos"))
               ->url(url::site("admin/videos")));
  }

  static function site_menu($menu, $theme) {
    $item = $theme->item();
    $paths = unserialize(module::get_var("videos", "authorized_paths"));

    if ($item && identity::active_user()->admin && $item->is_album() && !empty($paths) &&
        is_writable($item->is_album() ? $item->file_path() : $item->parent()->file_path())) {
      $menu->get("add_menu")
        ->append(Menu::factory("dialog")
                 ->id("Videos")
                 ->label(t("Add videos"))
                 ->url(url::site("videos/browse/$item->id")));
    }
  }

  static function item_before_delete($item) {
    // If deleting a video, make sure the resize is deleted as well, if it exists.
    if ($item->is_movie()) {
      $items_video = ORM::factory("items_video")
                     ->where("item_id", "=", $item->id)
                     ->find();
      if ($items_video->loaded() && file_exists($item->resize_path() . ".flv")) {
        @unlink($item->resize_path() . ".flv");
      }
    }
  }

  static function item_updated($old, $new) {
    // When updating a video, check and see if the file name is being changed.
    //  If so, check for and modify any corresponding resized video

    if ($old->is_movie()) {
      if ($old->file_path() != $new->file_path()) {
        $items_video = ORM::factory("items_video")
                       ->where("item_id", "=", $old->id)
                       ->find();
        if ($items_video->loaded() && file_exists($old->resize_path() . ".flv")) {
          @rename($old->resize_path() . ".flv", $new->resize_path() . ".flv");
        }
      }
    }
  }

  static function item_moved($item, $old_parent) {
    // When moving an video, also move the flash resize, if it exists.

    if ($item->is_movie()) {
      $items_video = ORM::factory("items_video")
                     ->where("item_id", "=", $item->id)
                     ->find();
      $old_resize_path = $old_parent->resize_path() . "/" . $item->name . ".flv";
      if ($items_video->loaded() && file_exists($old_resize_path)) {
        @rename($old_resize_path, $item->resize_path() . ".flv");
      }
    }
  }
}
