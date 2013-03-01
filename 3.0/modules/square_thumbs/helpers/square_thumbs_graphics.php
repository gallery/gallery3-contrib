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
class square_thumbs_graphics_Core {
  /**
   * Crop the input image so that it's square.  Focus on the center of the image.
   *
   * @param string     $input_file
   * @param string     $output_file
   * @param array      $options
   */
  static function crop_to_square($input_file, $output_file, $options) {
    graphics::init_toolkit();

    if (@filesize($input_file) == 0) {
      throw new Exception("@todo EMPTY_INPUT_FILE");
    }

    $size = module::get_var("gallery", "thumb_size");
    $dims = getimagesize($input_file);
    Image::factory($input_file)
      ->crop(min($dims[0], $dims[1]), min($dims[0], $dims[1]))
      ->quality(module::get_var("gallery", "image_quality"))
      ->save($output_file);
  }
}
