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
class tagfaces_event_Core {
  static function module_change($changes) {
    // See if the Tags module is installed,
    //   tell the user to install it if it isn't.
    if (!module::is_active("tag") || in_array("tag", $changes->deactivate)) {
      site_status::warning(
        t("The TagFaces module requires the Tags module.  " .
          "<a href=\"%url\">Activate the Tags module now</a>",
          array("url" => url::site("admin/modules"))),
        "tagfaces_needs_tag");
    } else {
      site_status::clear("tagfaces_needs_tag");
    }
  }

  static function site_menu($menu, $theme) {
    // Create a menu option for adding face data.
    if (!$theme->item()) {
      return;
    }

    $item = $theme->item();

    if ($item->is_photo()) {
      if ((access::can("view", $item)) && (access::can("edit", $item))) {
        $menu->get("options_menu")
             ->append(Menu::factory("link")
             ->id("tagfaces")
             ->label(t("Tag faces"))
             ->css_id("g-tag-faces-link")
             ->url(url::site("tagfaces/drawfaces/" . $item->id)));
      }
    }
  }

  static function item_deleted($item) {
    // Check for and delete existing Faces and Notes.
    $existingFaces = ORM::factory("items_face")
                          ->where("item_id", "=", $item->id)
                          ->find_all();
    if (count($existingFaces) > 0) {
      db::build()->delete("items_faces")->where("item_id", "=", $item->id)->execute();
    }

    $existingNotes = ORM::factory("items_note")
                          ->where("item_id", "=", $item->id)
                          ->find_all();
    if (count($existingNotes) > 0) {
      db::build()->delete("items_notes")->where("item_id", "=", $item->id)->execute();
    }
  }
}
