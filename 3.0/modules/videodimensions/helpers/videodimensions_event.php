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
class videodimensions_event_Core {
  static function item_edit_form($item, $form) {
    // Retrieve the existing height and width and display it on the form.
    if ($item->is_movie()) {
      $form->edit_item->input("vidheight")->label(t("Video Height"))
           ->value($item->height);
      $form->edit_item->input("vidwidth")->label(t("Video Width"))
           ->value($item->width);
    }
  }

  static function item_edit_form_completed($item, $form) {
    // Save the new height and width to the database.
    if ($item->is_movie()) {
      $item->height = $form->edit_item->vidheight->value;
      $item->width = $form->edit_item->vidwidth->value;
      $item->save();
    }
  }
}
