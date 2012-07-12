<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2011 Chad Parry
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
class rawphoto_event_Core {
  static function item_created($item) {
    if ($item->is_photo()) {
      $input_file = $item->file_path();
      $output_file = system::temp_filename("rawphoto-", "jpg");
      $success = rawphoto_graphics::convert($input_file, $output_file);
      if ($success) {
        $item->set_data_file($output_file);
        $item->save();
        unlink($output_file);
      }
    }
  }

  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
         ->append(Menu::factory("link")
         ->id("rawphoto")
         ->label(t("Raw Photos"))
         ->url(url::site("admin/rawphoto")));
  }

  static function legal_photo_extensions($extensions_wrapper) {
    array_push($extensions_wrapper->extensions,
               "3fr", "arw", "bay", "bmq", "cr2", "crw", "cs1", "dc2", "dcr", "dng", "erf",
               "fff", "k25", "kdc", "mef", "mos", "mrw", "nef", "orf", "pef", "raf", "raw",
               "rdc", "rw2", "sr2", "srf", "x3f");
  }

  static function legal_photo_types($types_wrapper) {
    array_push($types_wrapper->types,
               // Most raw photos are detected as TIFF.
               "image/tiff",
               // Minolta raw photos are mis-detected as wireless bitmap format.
               "image/vnd.wap.wbmp",
               // All other raw photos have unrecognized formats.
               "");
  }

  static function module_change($changes) {
    rawphoto_version::report_item_conversion_support();
  }

  static function graphics_toolkit_change($toolkit_id) {
    rawphoto_graphics::report_ppm_support($toolkit_id);
  }
}
