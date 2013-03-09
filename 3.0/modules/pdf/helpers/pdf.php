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
class pdf_Core {
  /**
   * Return the path to the gs binary if one exists and is executable, or null.
   * (ref: movie::find_ffmpeg())
   */
  static function find_gs() {
    if (!($gs_path = module::get_var("pdf", "gs_path")) || !@is_executable($gs_path)) {
      $gs_path = system::find_binary("gs", module::get_var("gallery", "graphics_toolkit_path"));
      module::set_var("pdf", "gs_path", $gs_path);
    }
    return $gs_path;
  }

  /**
   * Return version number of gs if found, an empty string if not.
   * (ref: movie::get_ffmpeg_version())
   */
  static function get_gs_version() {
    if (pdf::find_gs()) {
      $path = module::get_var("pdf", "gs_path");
      exec(escapeshellcmd($path)." -version", $output);
      if (stristr($output[0], "ghostscript")) {
        // Found "ghostscript" in the response - it's valid.
        return $output[0];
      }
    }
    return "";
  }

  /**
   * Mark all PDF thumbs as dirty
   * (ref: graphics::mark_dirty())
   */
  static function mark_dirty() {
    graphics::mark_dirty(true, false, "movie", "application/pdf");
  }

  /**
   * Fix mime types of all existing PDFs (could be set incorrectly before this module was installed)
   */
  static function fix_mime_types() {
    $db = db::build()
      ->update("items")
      ->set("mime_type", "application/pdf")
      ->where("name", "REGEXP", "\.pdf$") // ends in pdf
      ->execute();
  }
}