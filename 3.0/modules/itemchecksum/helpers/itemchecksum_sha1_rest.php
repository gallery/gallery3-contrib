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
class itemchecksum_sha1_rest_Core {
  static function get($request) {
    $item = rest::resolve($request->url);
    access::required("view", $item);
    $checksum = "0";
    // If the KeepOriginal module is active, check for/use the
    //   original image instead of the gallery edited version.
    if (module::is_active("keeporiginal")) {
      $original_image = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item->file_path());
      if ($item->is_photo() && file_exists($original_image)) {
        $checksum = sha1_file($original_image);
      } else {
        $checksum = sha1_file($item->file_path());
      }
    } else {
      $checksum = sha1_file($item->file_path());
    }
    $data = array("checksum" => $checksum);

    return array(
      "url" => $request->url,
      "entity" => $data);
  }

  static function resolve($id) {
    $item = ORM::factory("item", $id);
    if (!access::can("view", $item)) {
      throw new Kohana_404_Exception();
    }
    return $item;
  }

  static function url($item) {
    return url::abs_site("rest/itemchecksum_sha1/{$item->id}");
  }
}
