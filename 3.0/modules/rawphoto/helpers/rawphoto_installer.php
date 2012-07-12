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
class rawphoto_installer {
  static function install() {
    module::set_version("rawphoto", 3);
  }

  static function activate() {
    rawphoto_version::report_item_conversion_support();
    $dcraw = rawphoto_graphics::detect_dcraw();
    rawphoto_graphics::report_dcraw_support($dcraw);
    $toolkit_id = module::get_var("gallery", "graphics_toolkit");
    rawphoto_graphics::report_ppm_support($toolkit_id);
  }

  static function deactivate() {
    site_status::clear("rawphoto_needs_item_conversion_support");
    site_status::clear("rawphoto_needs_dcraw");
    site_status::clear("rawphoto_needs_ppm_support");
  }
}
