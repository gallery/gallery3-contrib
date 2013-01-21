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
class tagfaces_theme_Core {
  static function photo_bottom($theme) {
    // Check and see if the current photo has any tagged faces
    //   or notes associated with it.
    $item = $theme->item;

    $existingFaces = ORM::factory("items_face")
                          ->where("item_id", "=", $item->id)
                          ->find_all();
    $existingNotes = ORM::factory("items_note")
                          ->where("item_id", "=", $item->id)
                          ->find_all();

    // If it does, add an image map to the page to display them.
    if ((count($existingFaces) > 0) || (count($existingNotes) > 0)) {
      return new View("drawfaces_highlight_block.html");
    }
  }
}
