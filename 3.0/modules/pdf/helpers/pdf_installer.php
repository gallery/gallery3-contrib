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
class pdf_installer {
  static function install() {
    // Set gs_path variable
    if ($gs_path = exec("which gs")) {
      module::set_var("pdf", "gs_path", $gs_path);
    }
    pdf::find_gs();
    // Set other variables
    module::set_var("pdf", "make_thumb", true);
    module::set_var("pdf", "movie_overlay_hide", false);
    module::set_version("pdf", 1);
  }

  static function can_activate() {
    $messages = array();
    if (module::get_version("gallery") < 56) {
      $messages["warn"][] = t("PDF requires Gallery v3.0.5 or newer.");
    }
    return $messages;
  }

  static function activate() {
    // Fix mime types of all existing PDFs
    pdf::fix_mime_types();
  }

  static function uninstall() {
    // Delete vars from database
    module::clear_all_vars("pdf");
  }
}