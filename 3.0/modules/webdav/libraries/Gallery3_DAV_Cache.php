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
class Gallery3_DAV_Cache {
  private static $cache;
  private static $instance;

  private function __construct() {
    self::$cache = array();
  }

  public static function instance() {
    if (!isset(self::$instance)) {
      self::$instance = new Gallery3_DAV_Cache();
    }
    return self::$instance;
  }

  private function encode_path($path) {
    $path = trim($path, "/");
    $encoded_array = array();
    foreach (explode("/", $path) as $part) {
      $encoded_array[] = rawurlencode($part);
    }

    return join("/", $encoded_array);
  }

  public function to_album($path) {
    $path = substr($path, 0, strrpos($path, "/"));
    return $this->to_item($path);
  }

  public function to_item($path) {
    $path = trim($path, "/");
    $path = $this->encode_path($path);

    if (!isset(self::$cache[$path])) {
      self::$cache[$path] = ORM::factory("item")
        ->viewable()
        ->where("relative_path_cache", "=", $path)
        ->find();
    }

    return self::$cache[$path];
  }

  public function __clone() {
  }
}
