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
class panorama_event_Core {
  static function item_edit_form($item, $form) {
    if ($item->is_photo()) {
      $panorama = ORM::factory("panorama")->where("item_id", "=", $item->id)->find();
      $form->edit_item->checkbox("panorama_panorama")
        ->label(t("Display as a panorama"))
        ->id("g-panorama-panorama")
        ->checked($panorama->checked);
      $form->edit_item->input("panorama_HFOV")
        ->label(t("Horizontal FOV"))
        ->id("g-panorama-HFOV")
        ->value($panorama->HFOV);
      $form->edit_item->input("panorama_VFOV")
        ->label(t("Vertical FOV"))
        ->id("g-panorama-VFOV")
        ->value($panorama->VFOV);
    }
  }

  static function item_edit_form_completed($item, $form) {
    $panorama = ORM::factory("panorama")->where("item_id", "=", $item->id)->find();
    if (!($panorama->loaded())) {
      $panorama->item_id = $item->id;
    }
    $panorama->checked= $form->edit_item->panorama_panorama->checked;
    $panorama->HFOV= $form->edit_item->panorama_HFOV->value;
    $panorama->VFOV= $form->edit_item->panorama_VFOV->value;
    /* If unspecified, we'll assume it's a full 360 panorama. Otherwise, we assume HFOV is accurate. In either case, we calculate the other value from the given one plus the image ratio */
    if (!($panorama->HFOV) && !($panorama->VFOV)) {
      $panorama->HFOV = 360;
      $panorama->VFOV = $panorama->HFOV / $item->width * $item->height;
    } else if ($panorama->HFOV) {
      $panorama->VFOV = $panorama->HFOV / $item->width * $item->height;
    } else {
      $panorama->HFOV = $panorama->VFOV * $item->width / $item->height;
    }

    $panorama->save();
  }

}

