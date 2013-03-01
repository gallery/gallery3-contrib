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
class jhead_event_Core {
  static function item_created($item) {
    // Only works on photos
    if ( ! $item->is_photo()) {
      return;
    }

    // Locate jhead
    if ( ! is_file($path = exec('which jhead'))) {
      // @todo throw an exception ?
      Kohana::log('error', 'jhead is not installed');
    }
    $binary  = str_replace('\\', '/', realpath(dirname($path)));
    $binary .= '/jhead';
    $binary .= (PHP_SHLIB_SUFFIX === 'dll') ? '.exe' : '';

    if ( ! is_file($binary)) {
      // @todo throw an exception ?
      Kohana::log('error', 'Unable to locate jhead binary');
    }

    // Invoke jhead
    if ($error = exec(escapeshellcmd($binary).' -q -autorot '.escapeshellarg($item->file_path()))) {
      // @todo throw an exception ?
      Kohana::log('error', 'Error during execution of jhead');
    }

    // Update item
    $image_info = getimagesize($item->file_path());
    $item->width = $image_info[0];
    $item->height = $image_info[1];
    $item->resize_dirty = 1;
    $item->thumb_dirty = 1;
    $item->save();
    graphics::generate($item);
  }
}
