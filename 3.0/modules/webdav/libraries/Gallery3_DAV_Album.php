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
class Gallery3_DAV_Album extends Sabre_DAV_Directory {
  private $item;
  private $stat;
  private $path;

  function __construct($path) {
    $this->cache = Gallery3_DAV_Cache::instance();
    $this->path = $path;
    $this->item = $this->cache->to_item($path);
  }

  function getName() {
    return $this->item->name;
  }

  function getChildren() {
    $return = array();
    foreach ($this->item->viewable()->children() as $child) {
      $return[] = $this->getChild($child->name);
    }
    return $return;
  }

  function getChild($name) {
    $rp = "{$this->path}/$name";
    $child = $this->cache->to_item($rp);

    if (!access::can("view", $child)) {
      throw new Sabre_DAV_Exception_FileNotFound("Access denied");
    }

    if ($child->is_album()) {
      return new Gallery3_DAV_Album($rp);
    } else {
      return new Gallery3_DAV_File($rp);
    }
  }

  public function createFile($name, $data=null) {
    try {
      access::required("view", $this->item);
      access::required("add", $this->item);
    } catch (Kohana_404_Exception $e) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    }
    if (substr($name, 0, 1) == ".") {
      return true;
    };

    try {
      $tempfile = tempnam(TMPPATH, "dav");
      $target = fopen($tempfile, "wb");
      stream_copy_to_stream($data, $target);
      fclose($target);

      $item = ORM::factory("item");
      $item->name = $name;
      $item->title = item::convert_filename_to_title($item->name);
      $item->description = "";
      $item->parent_id = $this->item->id;
      $item->set_data_file($tempfile);
      $item->type = "photo";
      $item->save();
    } catch (Exception $e) {
      unlink($tempfile);
      throw $e;
    }
  }

  public function createDirectory($name) {
    try {
      access::required("view", $this->item);
      access::required("add", $this->item);
    } catch (Kohana_404_Exception $e) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    }

    $album = ORM::factory("item");
    $album->type = "album";
    $album->parent_id = $this->item->id;
    $album->name = $name;
    $album->title = $name;
    $album->description = "";
    $album->save();

    // Refresh MPTT pointers
    $this->item->reload();
  }

  function getLastModified() {
    return $this->item->updated;
  }

  function setName($name) {
    if (!access::can("edit", $this->item)) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    };

    $this->item->name = $name;
    $this->item->save();
  }

  public function delete() {
    if (!access::can("edit", $this->item)) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    };
    $this->item->delete();
  }
}
