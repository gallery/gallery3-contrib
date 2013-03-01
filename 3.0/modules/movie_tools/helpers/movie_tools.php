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
class movie_tools_Core {
  /**
   * Return an array containing all of the data for the optional movie formats.  This is used
   * many other places in the module.
   */
  static function get_formats() {
    $data = array();
    $data["fp3"]     = array("name"  => t("Supported by Flowplayer 3"),
                             "desc"  => t("Formats that should be viewable with Gallery's standard movie player"),
                             "types" => array("mov" => "video/quicktime",
                                              "f4v" => "video/x-m4v"));
    $data["html5"]   = array("name"  => t("HTML5 video"),
                             "desc"  => t("Along with MP4, generally considered 'HTML5-compatible' formats"),
                             "types" => array("webm" => "video/webm",
                                              "ogv" => "video/ogg"));
    $data["mts"]     = array("name"  => t("MPEG transport stream"),
                             "desc"  => t("Not a standard web format, but still supported by FFMpeg"),
                             "types" => array("ts" => "video/mp2t",
                                              "mts" => "video/mp2t",
                                              "m2ts" => "video/mp2t"));
    $data["mpeg"]    = array("name"  => t("MPEG-1 and MPEG-2"),
                             "desc"  => t("Not a standard web format, but still supported by FFMpeg"),
                             "types" => array("mpe" => "video/mpeg",
                                              "mpeg" => "video/mpeg",
                                              "mpg" => "video/mpeg",
                                              "m1v" => "video/mpeg",
                                              "m2v" => "video/mpeg"));
    $data["mpeg4"]   = array("name"  => t("Additional MPEG-4"),
                             "desc"  => t("Not a standard web format, but still supported by FFMpeg"),
                             "types" => array("mp4v" => "video/mp4",
                                              "mpg4" => "video/mp4"));
    $data["msapple"] = array("name"  => t("Microsoft and Apple"),
                             "desc"  => t("Not a standard web format, but still supported by FFMpeg"),
                             "types" => array("qt" => "video/quicktime",
                                              "wmv" => "video/x-ms-wmv",
                                              "avi" => "video/x-msvideo",
                                              "asf" => "video/x-ms-asf"));
    $data["3gphone"] = array("name"  => t("3G mobile phone video"),
                             "desc"  => t("Not a standard web format, but still supported by FFMpeg"),
                             "types" => array("3gp" => "video/3gpp",
                                              "3g2" => "video/3gpp2"));
    return $data;
  }

  /**
   * Return data similar to that of the above function but for the default Gallery formats.
   * These formats are not available as options; they're just used for display purposes.
   */
  static function get_default_formats() {
    $data = array();
    $data["default"] = array("name"  => t("Gallery's defaults"),
                             "desc"  => t("Enabled by default on all Gallery installations"),
                             "types" => array("mp4" => "video/mp4",
                                              "flv" => "video/x-flv",
                                              "m4v" => "video/x-m4v"));
    return $data;
  }

  /**
   * Return formats string converted to array, e.g. convert "mp4 (video/mp4), webm (video/webm)" to
   * array("mp4" => "video/mp4", "webm" => "video/webm") if the string is formatted correctly;
   * return null if not.
   */
  static function formats_string_to_array($input) {
    $format_strings = explode(",", $input);
    $formats = array();

    foreach ($format_strings as $format_string) {
      $format_string = strtolower(trim($format_string));
      if (!$format_string) {
        // It's blank - skip it.
        continue;
      }
      if (preg_match("|([0-9a-z]+)\s*\(([0-9a-z]+/[0-9a-z-]+)\)|", $format_string, $matches)) {
        // It's valid - include it.
        $formats[$matches[1]] = $matches[2];
      } else {
        // It's invalid - break out of function and return null.
        return null;
      }
    }

    return $formats;
  }

  /**
   * Return formats array as string, i.e. the inverse of the function above.
   */
  static function formats_array_to_string($input) {
    $formats = array();
    if (!$input) {
      return array();
    }
    foreach ($input as $extension => $mime_type) {
      $formats[] = "$extension ($mime_type)";
    }
    return implode(", ", $formats);
  }

  /**
   * Wrapper around formats_string_to_array() to convert straight to json and handle empty inputs.
   */
  static function formats_string_to_json($input) {
    $array = movie_tools::formats_string_to_array($input);
    if ($array) {
      return json_encode($array);
    } else {
      return "";
    }
  }

  /**
   * Wrapper around formats_array_to_string() to convert straight from json and handle empty inputs.
   */
  static function formats_json_to_string($input) {
    if ($input) {
      return movie_tools::formats_array_to_string(json_decode($input, true));
    } else {
      return "";
    }
  }
}
