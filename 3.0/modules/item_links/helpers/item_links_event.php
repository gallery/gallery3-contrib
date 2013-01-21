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
class item_links_event_Core {
  static function item_edit_form($item, $form) {
    // Create fields on the album edit screen to allow the user to link
    //   the item to another page.

    $item_url = ORM::factory("item_link")
      ->where("item_id", "=", $item->id)
      ->find_all();

    $existing_url = "";
    if (count($item_url) > 0) {
      $existing_url = $item_url[0]->url;
    }
    $form->edit_item
         ->input("item_links_url")
         ->label(t("Redirect to URL:"))
         ->value($existing_url);
  }

  static function item_deleted($item) {
    // Whenever an item is deleted, delete any corresponding data.
    db::build()->delete("item_links")->where("item_id", "=", $item->id)->execute();
  }

  static function item_edit_form_completed($item, $form) {
    // Update the database with any changes to the item_links field.
    $record = ORM::factory("item_link")->where("item_id", "=", $item->id)->find();

    if ($form->edit_item->item_links_url->value != "") {
      if (!$record->loaded()) {
        $record->item_id = $item->id;
      }
      $record->url = $form->edit_item->item_links_url->value;
      $record->save();
    } else {
      db::build()->delete("item_links")->where("item_id", "=", $item->id)->execute();
    }
  }
}
