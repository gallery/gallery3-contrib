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
class auto_date {
  static function set_auto_date($item) {

    if (!$item->is_album() && !$item->captured) {
      $base_name = str_ireplace(array(".flv", ".jpg", ".gif"), "", $item->name);
      $date_format = module::get_var("auto_date", "template");
      $time = strptime($base_name, $date_format);
      if ($time) {
        $item->captured = mktime($time['tm_hour'], $time['tm_min'], $time['tm_sec'], $time['tm_mon']+1, $time['tm_mday'], ($time['tm_year'] + 1900));
        $item->save();
      }
    }
    return;
  }
}
