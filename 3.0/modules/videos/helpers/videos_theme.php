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
 
// rWatcher Edit:  This file used to be server_add_theme.php.
// All occurences of server_add have been replaced with videos.
//  Additionally, the head function has been reworked to provide a 
//  download link for unplayable videos and references to admin.js are now admin_videos.js.

class videos_theme_Core {
  static function head($theme) {
    $buf = "";
    if (identity::active_user()->admin) {
      $buf .= $theme->css("videos.css");
      $buf .= $theme->script("videos.js");
    }

    $item = $theme->item();
    if ($item && $item->is_movie()) {
      $items_video = ORM::factory("items_video")
      ->where("item_id", "=", $item->id)
      ->find();
      if (($items_video->loaded()) && (!file_exists($item->resize_path() . ".flv"))) {
        $buf .= $theme->script("videos_download.js");
      }
    }
    return $buf;
  }

  static function admin_head($theme) {
    $buf = "";
    if (strpos(Router::$current_uri, "admin/videos") !== false) {
      $buf .= $theme->css("videos.css")
        . $theme->css("jquery.autocomplete.css");
      $base = url::site("__ARGS__");
      $csrf = access::csrf_token();
      $buf .= "<script type=\"text/javascript\"> var base_url = \"$base\"; var csrf = \"$csrf\";</script>";

      $buf .= $theme->script("jquery.autocomplete.js")
        . $theme->script("admin_videos.js"); // rWatcher edit.
    }

    return $buf;
  }
}
