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
class navcarousel_theme_Core {
  static function head($theme) {
   if ($theme->page_type == "item") {
      if (locales::is_rtl()) {
        $rtl_support = "rtl: true,\n";
      } else {
        $rtl_support = "rtl: false,\n";
      }
      $carouselwidth = module::get_var("navcarousel", "carouselwidth", "600");
      if ($carouselwidth == 0) {
        $carouselwidth = "100%";
        $containerwidth = "";
      } else {
        $carouselwidth = $carouselwidth ."px";
        $containerwidth = ".jcarousel-skin-tango .jcarousel-container-horizontal {\n
                    width: ". $carouselwidth .";\n
                }\n";
      }
      $thumbsize = module::get_var("navcarousel", "thumbsize", "50");
      $showelements = module::get_var("navcarousel", "showelements", "7");
      $childcount = $theme->item->parent()->viewable()->children_count();
      $itemoffset = intval(floor($showelements / 2));
      if ($childcount <= $showelements) {
        $itemoffset = 1;
      } else {
        $itempos = $theme->item->parent()->get_position($theme->item);
        $itemoffset = $itempos - $itemoffset;
        if ($itemoffset < 1) {
          $itemoffset = 1;
        }
        if (($itemoffset + $showelements) > $childcount) {
          $itemoffset = $childcount - $showelements + 1;
        }
      }
      if (module::get_var("navcarousel", "noajax", false)) {
        $ajaxhandler = "";
      } else {
        $ajaxhandler = "itemLoadCallback: navcarousel_itemLoadCallback,\n";
      }
      if (module::get_var("navcarousel", "showondomready", false)) {
        $onwinload = "";
      } else {
        $onwinload = "});\n
                  $(window).load(function () {\n";
      }
      return
        $theme->script("jquery.jcarousel.min.js")
        . $theme->css("skin.css")
        . "\n<!-- Navcarousel -->
                <style type=\"text/css\">\n
                ". $containerwidth ."
                .jcarousel-skin-tango .jcarousel-clip-horizontal {\n
                    width:  ". $carouselwidth .";\n
                    height: ". ($thumbsize + 25) ."px;\n
                }\n
                .jcarousel-skin-tango .jcarousel-item {\n
                    width: ". ($thumbsize + 25) ."px;\n
                    height: ". ($thumbsize + 25) ."px;\n
                }\n
                #navcarousel-loader {\n
                    height: ". ($thumbsize + 25) ."px;\n
                }\n
                .jcarousel-skin-tango .jcarousel-next-horizontal {
                    top: ". (intval(floor($thumbsize / 2.8))) ."px;
                }
                .jcarousel-skin-tango .jcarousel-prev-horizontal {
                    top: ". (intval(floor($thumbsize / 2.8))) ."px;
                }
                </style>\n
                <script type=\"text/javascript\">\n
                  jQuery(document).ready(function() {\n
                    jQuery('#navcarousel').jcarousel({\n
                        ". $ajaxhandler ."
                        itemFallbackDimension: ". ($thumbsize + 25) .",\n
                        start: ". $itemoffset .",\n
                        size: ". $childcount .",\n
                        visible: ". $showelements .",\n
                        ". $rtl_support ."
                        scroll: ". module::get_var("navcarousel", "scrollsize", "7") ."\n
                    });\n
                  ". $onwinload ."
                    $(\".jcarousel-prev-horizontal\").css(\"visibility\", \"visible\");\n
                    $(\".jcarousel-next-horizontal\").css(\"visibility\", \"visible\");\n
                    $(\"#navcarousel\").css(\"visibility\", \"visible\");\n
                    $(\"#navcarousel-wrapper\").css(\"background\", \"none\");\n
                    $(\"#navcarousel-wrapper\").css(\"float\", \"left\");\n
                  });\n
                </script>\n
                <!-- Navcaoursel -->";
    }
  }
  
  static function photo_bottom($theme) {
    if (!module::get_var("navcarousel", "abovephoto", false)) {
      if ($theme->page_type == "item") {
        return new View("navcarousel.html");
      }
    }
  }
  
  static function photo_top($theme) {
    if (module::get_var("navcarousel", "abovephoto", false)) {
      if ($theme->page_type == "item") {
        return new View("navcarousel.html");
      }
    }
  }
}
