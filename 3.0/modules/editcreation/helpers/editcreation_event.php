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
class editcreation_event_Core {
  static function item_edit_form($item, $form) {
    // Add a couple of drop-down boxes to allow the user to edit the date
    // that $item was created on.
        
    // Add the datecreated element to the form.
    $form->edit_item->dateselect("datecreated")
                    ->label(t("Created"))
                    ->minutes(1)
                    ->years(1970, date('Y')+1)
                    ->value($item->created);
  }

  static function item_edit_form_completed($item, $form) {
    // Change the item's created field to the specified value.
    $item->created = $form->edit_item->datecreated->value;
    $item->save();
  }
}
