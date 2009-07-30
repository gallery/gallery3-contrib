<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
  static function item_before_delete($item) {
    if ($item->is_photo()) {
      $original_file = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item->file_path());
      if (file_exists($original_file)) {
        unlink($original_file);
      }
    }
    if ($item->is_album()) {
      $original_file = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item->file_path());
      if (file_exists($original_file)) {
        @dir::unlink($original_file);
      }
    }
  }
  static function item_updated($old, $new) {
    if ($old->is_photo() || $old->is_album()) {
      if ($old->file_path() != $new->file_path()) {
        $old_original = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $old->file_path());
        $new_original = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $new->file_path());
        if (file_exists($old_original)) {
          rename($old_original, $new_original);
        }
      }
    }
  }
}