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
class rawphoto_graphics {
  static function detect_dcraw() {
    $dcraw = new stdClass();
    $path = system::find_binary("dcraw");
    if (empty($path)) {
      $dcraw->installed = false;
      $dcraw->error = t("The dcraw tool could not be located on your system.");
    } else {
      if (@is_file($path) && preg_match('/^Raw [Pp]hoto [Dd]ecoder(?: "dcraw")? v(\S+)$/m',
                                        shell_exec($path), $matches)) {
        $dcraw->installed = true;
        $dcraw->path = $path;
        $dcraw->version = $matches[1];
      } else {
        $dcraw->installed = false;
        $dcraw->error = t("The dcraw tool is installed, but PHP's open_basedir restriction " .
                          "prevents Gallery from using it.");
      }
    }
    return $dcraw;
  }

  static function convert($input_file, $output_file) {
    $success = false;
    $dcraw = rawphoto_graphics::detect_dcraw();
    if ($dcraw->installed) {
      // Use dcraw to convert from a raw image to a standard pixmap.
      $cmd = escapeshellcmd($dcraw->path) . " -c -w -W -t 0 ";
      $icc_path = module::get_var("rawphoto", "icc_path");
      if (!empty($icc_path)) {
        $cmd .= "-p " . escapeshellarg($icc_path) . " ";
      }
      $cmd .= escapeshellarg($input_file);

      // Then use the graphics toolkit to convert the stream to a JPEG.
      $cmd .= "| ";
      $toolkit_id = module::get_var("gallery", "graphics_toolkit");
      $toolkit_path = module::get_var("gallery", "graphics_toolkit_path");
      $image_quality = module::get_var("gallery", "image_quality");
      $toolkit_compat = false;
      switch ($toolkit_id) {
        case 'imagemagick':
          $cmd .= escapeshellcmd("$toolkit_path/convert");
          $cmd .= " -quality " . escapeshellarg($image_quality . "%");
          $cmd .= " - " . escapeshellarg($output_file);
          $toolkit_compat = true;
          break;
        case 'graphicsmagick':
          $cmd .= escapeshellcmd("$toolkit_path/gm");
          $cmd .= " convert -quality " . escapeshellarg($image_quality . "%");
          $cmd .= " - " . escapeshellarg($output_file);
          $toolkit_compat = true;
          break;
        default:
          log::warning("rawphoto", "Cannot convert raw photo with graphics toolkit: " .
                                   $toolkit_id->active);
      }

      if ($toolkit_compat) {
        exec($cmd, $output, $return_var);
        $success = ($return_var == 0);
      }
    }
    if (!$success) {
      // Make sure the unmodified output file exists where it's expected to be.
      copy($input_file, $output_file);
    }
  }
}
