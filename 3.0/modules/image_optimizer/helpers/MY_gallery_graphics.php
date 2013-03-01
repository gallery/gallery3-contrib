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
class gallery_graphics extends gallery_graphics_Core {
  
  // this is copied from modules/gallery/helpers/gallery_graphics.php
  
  /**
   * Rotate an image.  Valid options are degrees
   *
   * @param string     $input_file
   * @param string     $output_file
   * @param array      $options
   */
  static function rotate($input_file, $output_file, $options) {
    graphics::init_toolkit();

    module::event("graphics_rotate", $input_file, $output_file, $options);

    // BEGIN mod to original function
    $image_info = getimagesize($input_file); // [0]=w, [1]=h, [2]=type (1=GIF, 2=JPG, 3=PNG)
    if (module::get_var("image_optimizer","rotate_jpg") || $image_info[2] == 2) {
      // rotate_jpg enabled, the file is a jpg.  get args
      $path = module::get_var("image_optimizer", "path_jpg");
      $exec_args  = " -rotate ";
      $exec_args .= $options["degrees"] > 0 ? $options["degrees"] : $options["degrees"]+360;
      $exec_args .= " -copy all -optimize -outfile ";
      // run it - from input_file to tmp_file
      $tmp_file = image_optimizer::make_temp_name($output_file);
      exec(escapeshellcmd($path) . $exec_args . escapeshellarg($tmp_file) . " " . escapeshellarg($input_file), $exec_output, $exec_status);
      if ($exec_status || !filesize($tmp_file)) {
        // either a blank/nonexistant file or an error - log an error and pass to normal function
        Kohana_Log::add("error", "image_optimizer rotation failed on ".$output_file);
        unlink($tmp_file);
      } else {
        // worked - move temp to output
        rename($tmp_file, $output_file);
        $status = true;
      }
    }
    if (!$status) {
      // we got here if we weren't supposed to use jpegtran or if jpegtran failed
    // END mod to original function
    
      Image::factory($input_file)
        ->quality(module::get_var("gallery", "image_quality"))
        ->rotate($options["degrees"])
        ->save($output_file);

    // BEGIN mod to original function
    }
    // END mod to original function

    module::event("graphics_rotate_completed", $input_file, $output_file, $options);
  }
}