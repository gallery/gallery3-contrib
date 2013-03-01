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
class embed_videos_theme_Core {
  static function page_top($theme) {
    $item = $theme->item();
    if ($item && $item->is_photo()) {
      $embedded_video = ORM::factory("embedded_video")
      ->where("item_id", "=", $item->id)
      ->find();
      if ($embedded_video->loaded()) {
        $view = new View("embed_video_js.html");
        $view->embed_code = addslashes($embedded_video->embed_code);
        return $view;
      }
    }
  }
}