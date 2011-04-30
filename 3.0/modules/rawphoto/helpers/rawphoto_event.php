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
      $output_file = system::tempnam(TMPPATH, "rawphoto-", ".jpg");
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

  static function legal_file_extensions($extensions_wrapper) {
    array_push($extensions_wrapper->extensions,
               "bay", "bmq", "cr2", "crw", "cs1", "dc2", "dcr", "dng", "fff", "k25", "kdc",
               "mos", "mrw", "nef", "orf", "pef", "raf", "raw", "rdc", "srf", "x3f");
  }

  static function graphics_toolkit_change($toolkit_id) {
    rawphoto_graphics::report_ppm_support($toolkit_id);
  }
}
