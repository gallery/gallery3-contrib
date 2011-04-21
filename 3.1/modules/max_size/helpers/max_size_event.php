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

class max_size_event_Core {
  static function item_before_create($item) {
    $max_size = module::get_var("max_size", "max_size", 600);
    if ($item->is_photo()) {
      list ($width, $height, $mime_type) = photo::get_file_metadata($item->data_file);
      if ($width > $max_size || $height > $max_size) {
        $tempnam = tempnam(TMPPATH, "size");
        $tmpfile = $tempnam . "." . pathinfo($item->data_file, PATHINFO_EXTENSION);
        gallery_graphics::resize(
          $item->data_file, $tmpfile,
          array("width" => $max_size, "height" => $max_size, "master" => Image::AUTO),
          $item);
        rename($tmpfile, $item->data_file);
        unlink($tempnam);
      }
    }
  }
}