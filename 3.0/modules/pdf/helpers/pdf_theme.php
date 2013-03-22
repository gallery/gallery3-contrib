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
class pdf_theme_Core {
  // Add CSS for PDF resize view
  static function head($theme) {
    $buf = $theme->css("pdf_resize.css");
    if (module::get_var("pdf", "movie_overlay_hide", false)) {
      $buf .= $theme->css("pdf_thumb.css");
    }
    return $buf;
  }

  // Add class for thumb view (useful for CSS)
  static function thumb_bottom($theme, $child) {
    if (strtolower(pathinfo($child->name, PATHINFO_EXTENSION)) == "pdf") {
      $id = "g-item-id-" . $child->id;
      $class = "g-pdf-thumb";
      return "<script type=\"text/javascript\">$(\"#$id\").addClass(\"$class\");</script>";
    }
  }
}
