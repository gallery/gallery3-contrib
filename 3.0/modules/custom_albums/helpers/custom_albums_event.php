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
class custom_albums_event_Core {
  static function item_edit_form($item, $form) {
    if ($item->is_album()) {
      $albumCustom = ORM::factory("custom_album")->where("album_id", "=", $item->id)->find();

      $thumbdata = $form->edit_item->group("custom_album")->label("Custom Album");

      if ($albumCustom->loaded()) {
        $thumbdata->input("thumbsize")->label(t("Thumbnail size (in pixels)"))->value($albumCustom->thumb_size);
      } else {
        $thumbdata->input("thumbsize")->label(t("Thumbnail size (in pixels)"));
      }
    }
  }

  static function item_edit_form_completed($item, $form) {
    if ($item->is_album()) {
      $thumbChanged = false;

      if ($form->edit_item->custom_album->thumbsize->value == "") {
        db::build()
          ->delete("custom_album")
          ->where("album_id", "=", $item->id)
          ->execute();
      } else {
        $albumCustom = ORM::factory("custom_album")->where("album_id", "=", $item->id)->find();
        if (!$albumCustom->loaded()) {
          $albumCustom->album_id = $item->id;
        }
        $albumCustom->thumb_size = $form->edit_item->custom_album->thumbsize->value;
        $albumCustom->save();

        $thumbChanged = true;
      }

      if ($thumbChanged) {
        db::build()
          ->update("items")
          ->set("thumb_dirty", 1)
          ->where("parent_id", "=", $item->id)
          ->execute();
      }
    }
  }

  static function theme_edit_form_completed($form) {
    // Update our resize rules, in case the thumbnail or resize size has changed
    custom_albums_installer::update_rules();
  }
}
