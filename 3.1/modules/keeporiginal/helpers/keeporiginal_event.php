<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class keeporiginal_event_Core {
  static function graphics_rotate($input_file, $output_file, $options) {
    // Make a copy of the original fullsized image before rotating it.

    //   If $input_file is located in VARPATH/albums/ then assume its a fullsize photo.
    if (strncmp($input_file, VARPATH . "albums/", strlen(VARPATH . "albums/")) == 0) {
      // Figure out where the original copy should be stashed at.
      $temp_path = str_replace(VARPATH . "albums/", "", $input_file);
      $original_image = VARPATH . "original/" . $temp_path;
      $individual_dirs = split("[/\]", "original/" . $temp_path);
      // If any original file does not already exist, then create a folder structure
      //   similar to that found in VARPATH/albums/ and copy the photo over before
      //   rotating it.
      if (!file_exists($original_image)) {
        $new_img_path = VARPATH;
        for($i = 0; $i < count($individual_dirs)-1; $i++) {
          $new_img_path = $new_img_path . "/" . $individual_dirs[$i];
          if(!file_exists($new_img_path)) {
            @mkdir($new_img_path);
          }
        }
        if (!@copy($input_file, $original_image)) {
          // If the copy failed, display an error message.
          message::error(t("Your original image was not backed up!"));
        }
      }
    }
  }

  static function item_before_delete($item) {
    // If deleting a photo, make sure the original is deleted as well, if it exists.
    if ($item->is_photo()) {
      $original_file = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item->file_path());
      if (file_exists($original_file)) {
        @unlink($original_file);
      }
    }

    // When deleting an album, make sure its corresponding location in
    //   VARPATH/original/ is deleted as well, if it exists.
    if ($item->is_album()) {
      $original_file = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item->file_path());
      if (file_exists($original_file)) {
        @dir::unlink($original_file);
      }
    }
  }

  static function item_updated($old, $new) {
    // When updating an item, check and see if the file name is being changed.
    //  If so, check for and modify any corresponding file/folder in
    //  VARPATH/original/ as well.

    if ($old->is_photo() || $old->is_album()) {
      if ($old->file_path() != $new->file_path()) {
        $old_original = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $old->file_path());
        $new_original = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $new->file_path());
        if (file_exists($old_original)) {
          @rename($old_original, $new_original);
        }
      }
    }
  }

  static function item_moved($item, $old_parent) {
    // When moving an item, check and see if a corresponding file exists
    //   in VARPATH/original/.  If so, move that item to a similar directory
    //   in original as well.

    if ($item->is_photo() || $item->is_album()) {
      $old_item_path = $old_parent->file_path() . "/" . $item->name;
      if ($item->file_path() != $old_item_path) {
        $old_original = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $old_item_path);
        $new_original = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item->file_path());

        if (file_exists($old_original)) {

          // Make sure the new folder exists, create it if it doesn't.
          $individual_dirs = split("[/\]", "original/" . str_replace(VARPATH . "albums/", "", $item->file_path()));
          $new_img_path = VARPATH;
          for($i = 0; $i < count($individual_dirs)-1; $i++) {
            $new_img_path = $new_img_path . "/" . $individual_dirs[$i];
            if(!file_exists($new_img_path)) {
              @mkdir($new_img_path);
            }
          }

          // Move the file to its new location.
          @rename($old_original, $new_original);
        }
      }
    }
  }

  static function site_menu($menu, $theme) {
    // Create a menu option to restore the original photo.
    if ($item = $theme->item()) {
      if ((access::can("view", $item)) && (access::can("edit", $item))) {
        $original_image = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item->file_path());

        if ($item->is_photo() && file_exists($original_image)) {
          $menu->get("options_menu")
            ->append(Menu::factory("link")
                     ->id("restore")
                     ->label(t("Restore original"))
                     ->css_id("g-keep-originals-link")
                     ->url(url::site("keeporiginal/restore/" . $item->id)));
        }
      }
    }
  }
}