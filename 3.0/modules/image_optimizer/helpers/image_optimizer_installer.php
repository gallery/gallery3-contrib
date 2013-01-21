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
class image_optimizer_installer {
  static function install() {
    $defaults = array('jpg' => '1', 'png' => '2', 'gif' => '1');
    foreach ($defaults as $type => $optlevel) {
      // set default path as the pre-compiled versions in the lib
      module::set_var("image_optimizer", "path_".$type, MODPATH."image_optimizer/lib/".image_optimizer::tool_name($type));
      // check config status (also sets configstatus_ variables and ensures that the permissions are set correctly)
      image_optimizer::tool_status($type);
      // set default optimization levels
      module::set_var("image_optimizer", "optlevel_thumb_".$type, $optlevel);
      module::set_var("image_optimizer", "optlevel_resize_".$type, $optlevel);
    }
    module::set_var("image_optimizer", "rotate_jpg", true);
    module::set_var("image_optimizer", "enable_thumb", true);
    module::set_var("image_optimizer", "enable_resize", true);
    module::set_var("image_optimizer", "update_mode_thumb", false);
    module::set_var("image_optimizer", "update_mode_resize", false);
    module::set_var("image_optimizer", "metastrip_thumb", true);
    module::set_var("image_optimizer", "convert_thumb_png", "jpg");
    module::set_var("image_optimizer", "convert_resize_png", false);
    module::set_var("image_optimizer", "convert_thumb_gif", "jpg");
    module::set_var("image_optimizer", "convert_resize_gif", false);
    module::set_var("image_optimizer", "metastrip_resize", false);
    module::set_var("image_optimizer", "progressive_thumb", false);
    module::set_var("image_optimizer", "progressive_resize", true);
    module::set_version("image_optimizer", 1);
    image_optimizer::add_image_optimizer_rule("thumb");
    image_optimizer::add_image_optimizer_rule("resize");
  }
  
  static function activate() {
    // add graphics rules if enabled
    if (module::get_var("image_optimizer", "enable_thumb")) {
      image_optimizer::add_image_optimizer_rule("thumb");
    }
    if (module::get_var("image_optimizer", "enable_resize")) {
      image_optimizer::add_image_optimizer_rule("resize");
    }
  }

  static function deactivate() {
    // ensure that update modes are disabled
    image_optimizer::deactivate_update_mode("thumb");
    image_optimizer::deactivate_update_mode("resize");
    // remove graphics rules
    image_optimizer::remove_image_optimizer_rule("thumb");
    image_optimizer::remove_image_optimizer_rule("resize");
  }
  
  static function uninstall() {
    // deactivate
    module::deactivate("image_optimizer");
    // delete vars from database
    module::clear_all_vars("image_optimizer");
  }
}