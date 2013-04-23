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
class photoannotation_theme_Core {
  static function head($theme) {
    $v = $theme->css("photoannotation.css");
    if ($theme->page_subtype == "photo") {
      $v .= $theme->script("jquery.annotate.min.js");
      $noborder = module::get_var("photoannotation", "noborder", false);
      $noclickablehover = module::get_var("photoannotation", "noclickablehover", false);
      $nohover = module::get_var("photoannotation", "nohover", false);
      $bordercolor = "#". module::get_var("photoannotation", "bordercolor", "000000");
      $v .= "<style type=\"text/css\">\n";
      $v .= ".photoannotation-del-button {\n
              border:1px solid ". $bordercolor ." !important;\n
              }\n";
      $v .= ".photoannotation-edit-button {\n
              border:1px solid ". $bordercolor ." !important;\n
              }";
      if ($noborder) {
        $border_thickness = "2px";
      } else {
        $border_thickness = "1px";
      }
      if (!$noborder || !$noclickablehover || !$nohover) {
        if (!$noborder) {
          $v .= ".image-annotate-area {\n
                border: 1px solid ". $bordercolor .";\n
                }\n";
          $v .= ".image-annotate-area div {\n
                  border: 1px solid #FFFFFF;\n
                  }\n";
        }
        if (!$noclickablehover) {
          $clickablehovercolor = "#". module::get_var("photoannotation", "clickablehovercolor", "00AD00");
          $v .= ".image-annotate-area-editable-hover div {\n
                  border: ". $border_thickness ." solid ". $clickablehovercolor ." !important;\n
                  }\n";
        }
        if (!$nohover) {
          $hovercolor = "#". module::get_var("photoannotation", "hovercolor", "990000");
          $v .= ".image-annotate-area-hover div {\n
                  border: ". $border_thickness ." solid ". $hovercolor ." !important;\n
                  }\n";
        }
      }
      $v .= "</style>\n";
      return $v;
    }
  }

  static function resize_bottom($theme) {
    if ($theme->page_subtype == "photo") {
      return new View("photoannotation_highlight_block.html");
    }
  }
  
  static function admin_head($theme) {
    if (strpos($theme->content->kohana_filename, "admin_photoannotation.html.php")) {
      return $theme->css("colorpicker.css")
        . $theme->script("jquery.colorpicker.min.js");
    }
  }

}
