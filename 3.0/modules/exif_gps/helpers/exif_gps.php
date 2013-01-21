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
class exif_gps_Core {
  protected static $exif_keys;

  static function extract($item) {
    $keys = array();
    // Extract Latitude and Longitude from the image (if they exist).
    if ($item->is_photo() && $item->mime_type == "image/jpeg") {
      $data = array();
      require_once(MODPATH . "exif/lib/exif.php");
      $exif_raw = read_exif_data_raw($item->file_path(), false);
      if (isset($exif_raw['ValidEXIFData'])) {
        foreach(self::_keys() as $field => $exifvar) {
          if (isset($exif_raw[$exifvar[0]][$exifvar[1]])) {
            $value = $exif_raw[$exifvar[0]][$exifvar[1]];
            if (function_exists("mb_detect_encoding") && mb_detect_encoding($value) != "UTF-8") {
              $value = utf8_encode($value);
            }
            $keys[$field] = Input::clean($value);
          }
        }
      }
    }

    // If coordinates were extracted, save them to the database.
    if (isset($keys["Latitude"]) && isset($keys["Longitude"])) {
      $record = ORM::factory("exif_coordinate");
      $record->item_id = $item->id;
      $record->latitude = str_replace(",", ".", $keys["Latitude"]);
      $record->longitude = str_replace(",", ".", $keys["Longitude"]);
      // Represent N/S/E/W as postive and negative numbers
      if (substr(strtoupper($keys["Latitude Reference"]), 0, 1) == "S") {
        $record->latitude = "-" . $record->latitude;
      }
      if (substr(strtoupper($keys["Longitude Reference"]), 0, 1) == "W") {
        $record->longitude = "-" . $record->longitude;
      }
      $record->save();
    }
  }

  private static function _keys() {
    // EXIF fields to extract.
    if (!isset(self::$exif_keys)) {
      self::$exif_keys = array(
        "Latitude Reference"     => array("GPS",    "Latitude Reference",       t("GPS: Latitude Reference"), ),
        "Longitude Reference"    => array("GPS",    "Longitude Reference",      t("GPS: Longitude Reference"),),
        "Latitude"        => array("GPS",    "Latitude",          t("GPS: Latitude"),    ),
        "Longitude"       => array("GPS",    "Longitude",         t("GPS: Longitude"),   )
      );
    }
    return self::$exif_keys;
  }
}
