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
class three_nids_event_Core {
  static function theme_edit_form($form) {
    $group = $form->group("three_nids")->label(t("3nids Theme Settings"));
    $group->input("title")
      ->rules("required")
      ->label(t("item title : parent or item."))
      ->value(module::get_var("three_nids", "title"));
    $group->input("description")
      ->rules("required")
      ->label(t("item description : tags or item or parent or nothing. If item description chosen and not available, then parent description is used."))
      ->value(module::get_var("three_nids", "description"));
    $group->input("photo_size")
      ->rules("required")
      ->label(t("Photo size: resize or full."))
      ->value(module::get_var("three_nids", "photo_size"));
  }

  static function theme_edit_form_completed($form) {
    module::set_var("three_nids", "description", $form->three_nids->description->value);
    module::set_var("three_nids", "title", $form->three_nids->title->value);
    module::set_var("three_nids", "photo_size", $form->three_nids->photo_size->value);
  }
}