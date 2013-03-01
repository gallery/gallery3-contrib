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
class itemchecksum_Controller extends Controller {
  public function albumcount($album_id) {
    // Display the number of non-album items (photos and videos)
    //   in the specified album ($album_id).
    $item = ORM::factory("item")
      ->viewable()
      ->where("parent_id", "=", $album_id)
      ->where("type", "!=", "album")
      ->find_all();

    print count($item);
  }

  public function md5($album_id, $file_name) {
    // Locate an item with $file_name in the album $album_id
    //   and display it's md5 checksum.
    $item = ORM::factory("item")
      ->where("parent_id", "=", $album_id)
      ->where("name", "=", $file_name)
      ->find_all();

    if (count($item) > 0) {
      access::required("view_full", $item[0]);

      // If the KeepOriginal module is active, check for/use the
      //   original image instead of the gallery edited version.
      if (module::is_active("keeporiginal")) {
        $original_image = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item[0]->file_path());
        if ($item[0]->is_photo() && file_exists($original_image)) {
          print md5_file($original_image);
        } else {
          print md5_file($item[0]->file_path());
        }
      } else {
        print md5_file($item[0]->file_path());
      }
    } else {
      print "0";
    }
  }

  public function sha1($album_id, $file_name) {
    // Locate an item with $file_name in the album $album_id
    //   and display it's sha-1 checksum.

    $item = ORM::factory("item")
      ->where("parent_id", "=", $album_id)
      ->where("name", "=", $file_name)
      ->find_all();

    if (count($item) > 0) {
      access::required("view_full", $item[0]);

      // If the KeepOriginal module is active, check for/use the
      //   original image instead of the gallery edited version.
      if (module::is_active("keeporiginal")) {
        $original_image = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item[0]->file_path());
        if ($item[0]->is_photo() && file_exists($original_image)) {
          print sha1_file($original_image);
        } else {
          print sha1_file($item[0]->file_path());
        }
      } else {
        print sha1_file($item[0]->file_path());
      }
    } else {
      print "0";
    }
  }
}
