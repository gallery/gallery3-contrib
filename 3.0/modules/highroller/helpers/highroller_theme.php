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
class highroller_theme_Core {
  static function head($theme) {
    return $theme->script("highroller.js")
      . sprintf("<script type=\"text/javascript\"> var PICK_THEME_URL = '%s'; </script>", url::site("highroller/pick_theme"));
  }

  static function header_top($theme) {
    $session = Session::instance();
    $highroller_theme = $session->get("highroller_theme", "");
    if ($highroller_theme) {
      print html::stylesheet(
        url::abs_file("modules/highroller/themes/$highroller_theme/theme.css"));
    }
  }

  static function footer($theme) {
    $base = MODPATH . "highroller/themes/";
    $base_len = strlen($base);
    $options[] = "<none>";
    foreach (glob("$base*") as $theme) {
      $name = substr($theme, $base_len);
      $options[$name] = $name;
    }
    $session = Session::instance();
    $highroller_theme = $session->get("highroller_theme");
    print '<div style="float: right"> Theme: ' .
      form::dropdown("", $options, $highroller_theme,
                     'style="display: inline" onchange="pick_theme(this.value)"') .
      '</div>';
  }
}