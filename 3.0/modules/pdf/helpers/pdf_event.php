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
class pdf_event_Core {

  /**
   * Add PDFs as a type of "movie"
   * (ref: legal_file::get_movie_types_by_extension())
   */
  static function movie_types_by_extension($types_by_extension_wrapper) {
    $types_by_extension_wrapper->types_by_extension["pdf"] = "application/pdf";
  }

  /**
   * Generate the thumbnails for PDFs
   * (ref: graphics::generate())
   */
  static function movie_extract_frame($input_file, $output_file, $movie_options_wrapper=null, $item=null) {
    $RESOLUTION = 300; // (DPI) - we extract high-res frames so resize has plenty to work with.
    if (strtolower(pathinfo($input_file, PATHINFO_EXTENSION)) == "pdf") {
      if (module::get_var("pdf", "make_thumb") && ($path = pdf::find_gs())) {
        // Enabled and gs found - make the thumb.
        $path = module::get_var("pdf", "gs_path");
        $exec_args = " -q -dBATCH -dMaxBitmap=500000000 -dNOPAUSE -dSAFER -sDEVICE=jpeg" .
                     " -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r$RESOLUTION -dFirstPage=1" .
                     " -dLastPage=1 -sOutputFile=";
        $exec_end = " -c quit";
        exec(escapeshellcmd($path) . $exec_args . escapeshellarg($output_file) . " " .
             escapeshellarg($input_file) . $exec_end, $exec_output, $exec_status);
        if (!$exec_status && filesize($output_file)) {
          // Thumb generated without reported errors - we're done.
          return;
        }
      }
      // It's a PDF, but the thumb was blank/nonexistant or had errors, or gs wasn't
      // configured/enabled - copy PDF icon from images instead
      copy(MODPATH . "pdf/images/ico-pdf.jpg", $output_file);
    }
  }

  /**
   * Make resize image to show the PDF object
   * (ref: Item_Model::movie_img())
   */
  static function movie_img($movie_img, $item) {
    if (strtolower(pathinfo($item->name, PATHINFO_EXTENSION)) == "pdf") {
      $view = new View("pdf_movie_img.html");
      $view->url = $item->file_url(true);
      $view->attrs = $movie_img->attrs;
      $view->object_attrs = array("data" => $view->url, "class" => "g-pdf-resize",
                                  "type" => "application/pdf");
      $movie_img->view[] = $view;
    }
  }

  /**
   * Add admin menu
   * (ref: Admin_View::admin_menu())
   */
  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(
        Menu::factory("link")
        ->id("pdf")
        ->label(t("PDF"))
        ->url(url::site("admin/pdf")));
  }

  /**
   * Get PDF file metadata (height and width)
   * (ref: movie::get_file_metadata())
   */
  static function movie_get_file_metadata($file_path, $metadata) {
    if ((strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) == "pdf") && ($path = pdf::find_gs())) {
      // Parsing gs output properly can be a pain.  So, let's go for a reliable albeit inefficient
      // approach: re-extract the frame (into tmp) and get its image size.
      $temp_file = system::temp_filename("pdf_", "jpg");
      pdf_event::movie_extract_frame($file_path, $temp_file, null, null);
      list($metadata->height, $metadata->width) = photo::get_file_metadata($temp_file);
    }
  }
}