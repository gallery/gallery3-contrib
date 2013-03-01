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
class rectangle_thumbs_graphics_Core {
  /**
   * Crop the input image so that it matches the aspect ratio specified in the
   * rectangle_thumbs.aspect_ratio module setting.  Focus on the center of the image and crop out
   * the biggest piece that we can.
   *
   * @param string     $input_file
   * @param string     $output_file
   * @param array      $options
   */
  static function crop_to_aspect_ratio($input_file, $output_file, $options) {
    graphics::init_toolkit();

    if (@filesize($input_file) == 0) {
      throw new Exception("@todo EMPTY_INPUT_FILE");
    }

    list ($desired_width, $desired_height) =
      explode(":", module::get_var("rectangle_thumbs", "aspect_ratio"));
    $desired_ratio = $desired_width / $desired_height;

    // Crop the largest rectangular section we can out of the original image.  Start with a
    // rectangular section that's guaranteed to be too large, then shrink it horizontally to just
    // barely fit.  If it's still too tall vertically, shrink both dimensions proportionally until
    // the horizontal edge fits as well.
    $dims = getimagesize($input_file);
    if ($desired_ratio == 1) {
      $new_width = $new_height = min($dims[0], $dims[1]);
    } else if ($desired_ratio < 1) {
      list ($new_width, $new_height) = array($dims[0], $dims[0] / $desired_ratio);
    } else {
      list ($new_width, $new_height) = array($dims[1] * $desired_ratio, $dims[1]);
    }

    if ($new_width > $dims[0]) {
      // Too wide, scale it down
      list ($new_width, $new_height) = array($dims[0], $dims[0] / $desired_ratio);
    }

    if ($new_height > $dims[1]) {
      // Too tall, scale it down some more
      $new_width = min($dims[0], $dims[1] * $desired_ratio);
      $new_height = $new_width / $desired_ratio;
    }
    $new_width = round($new_width);
    $new_height = round($new_height);

    Image::factory($input_file)
      ->crop($new_width, $new_height)
      ->quality(module::get_var("gallery", "image_quality"))
      ->save($output_file);
  }
}
