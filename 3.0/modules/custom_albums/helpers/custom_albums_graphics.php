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
class custom_albums_graphics_Core {
  static function build_thumb($input_file, $output_file, $options) {
    $albumCustom = ORM::factory("custom_album")->where("album_id", "=", $options["parent_id"])->find();

    // If this album has custom data, build the thumbnail at the specified size
    if ($albumCustom->loaded()) {
      $options["width"] = $albumCustom->thumb_size;
      $options["height"] = $albumCustom->thumb_size;
    }

    gallery_graphics::resize($input_file, $output_file, $options);
  }
  
  static function build_resize($input_file, $output_file, $options) {
    gallery_graphics::resize($input_file, $output_file, $options);
  }
}
