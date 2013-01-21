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
 
class FotomotorW_Controller extends Controller {
  public function resize($str_checksum, $item_id) {
    // Displayed the "resized" version of an image by it's ID number.
    //  This both gives fotomoto access to resizes regardless of permissions
    //  and forces fotomoto to track images by unique id instead of file name
    //  which is necessary for auto-pickup.

    // Load the photo from the provided id#.  If invalid, display a 404 error.
    $item = ORM::factory("item", $item_id);
    if (!$item->loaded()) {
      throw new Kohana_404_Exception();
    }

    // Make sure checksum matches, if not, throw a 404 error.
    if ($str_checksum != md5($item->created)) {
      throw new Kohana_404_Exception();
    }

    // If the resize file doesn't exist for some reason, display a 404 error.
    if (!file_exists($item->resize_path())) {
      throw new Kohana_404_Exception();
    }

    // Display the image.
    header("Content-Type: {$item->mime_type}");
    Kohana::close_buffers(false);
    $fd = fopen($item->resize_path(), "rb");
    fpassthru($fd);
    fclose($fd);
  }

  public function print_proxy($site_key, $file_id) {
    // This function retrieves the full-sized image for fotomoto.
    //   As this function by-passes normal Gallery security, a private 
    //   site-key is used to try and prevent people other then fotomoto
    //   from finding the URL.

    // If the site key doesn't match, display a 404 error.
    if ($site_key != module::get_var("fotomotorw", "fotomoto_private_key")) {
      throw new Kohana_404_Exception();
    }

    // Load the photo from the provided id.  If the id# is invalid, display a 404 error.
    $item = ORM::factory("item", $file_id);
    if (!$item->loaded()) {
      throw new Kohana_404_Exception();
    }

    // If the image file doesn't exist for some reason, display a 404 error.
    if (!file_exists($item->file_path())) {
      throw new Kohana_404_Exception();
    }

    // Display the image.
    header("Content-Type: {$item->mime_type}");
    Kohana::close_buffers(false);
    $fd = fopen($item->file_path(), "rb");
    fpassthru($fd);
    fclose($fd);
  }
}
