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
class rectangle_thumbs_installer {
  static function install() {
    // Insert a rule into the thumbnail generation pipeline that converts the source image to the
    // right aspect ratio such that when we resize it down, it comes out to the right dimensions.
    graphics::add_rule(
      "rectangle_thumbs", "thumb", "rectangle_thumbs_graphics::crop_to_aspect_ratio", array(), 50);
    module::set_var("rectangle_thumbs", "aspect_ratio", "3:1");
    module::set_version("rectangle_thumbs", 1);
  }
}
