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
class Gallery3_DAV_File extends Sabre_DAV_File {
  private $item;
  private $stat;
  private $path;

  function __construct($path) {
    $this->cache = Gallery3_DAV_Cache::instance();
    $this->item = $this->cache->to_item($path);

    if (access::can("view_full", $this->item)) {
      $this->stat = stat($this->item->file_path());
      $this->path = $this->item->file_path();
    } else {
      $this->stat = stat($this->item->resize_path());
      $this->path = $this->item->resize_path();
    }
  }

  public function delete() {
    if (!access::can("edit", $this->item)) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    };
    $this->item->delete();
  }

  function setName($name) {
    if (!access::can("edit", $this->item)) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    };
    $this->item->name = $name;
    $this->item->save();
  }

  public function getLastModified() {
    return $this->item->updated;
  }

  function get() {
    if (!access::can("view", $this->item)) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    };
    return fopen($this->path, "r");
  }

  function getSize() {
    return $this->stat[7];
  }

  function getName() {
    return $this->item->name;
  }

  function getETag() {
    if (!access::can("view", $this->item)) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    };
    return "'" . md5($this->item->file_path()) . "'";
  }
}
