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
class Gallery3_DAV_Tree extends Sabre_DAV_Tree {
  protected $root_node;

  public function __construct(Sabre_DAV_ICollection $root_node) {
    $this->cache = Gallery3_DAV_Cache::instance();
    $this->root_node = $root_node;
  }

  public function move($source, $target) {
    $source_item = $this->cache->to_item($source);
    $target_item = $this->cache->to_album($target);

    try {
      access::required("view", $source_item);
      access::required("edit", $source_item);
      access::required("view", $target_item);
      access::required("edit", $target_item);
    } catch (Kohana_404_Exception $e) {
      throw new Sabre_DAV_Exception_Forbidden("Access denied");
    }

    $source_item->parent_id = $target_item->id;
    $source_item->save();
    return true;
  }

  public function getNodeForPath($path) {
    $path = trim($path,"/");
    $item = $this->cache->to_item($path);

    if (!$item->loaded()) {
      throw new Sabre_DAV_Exception_FileNotFound("Could not find node at path: $path");
    }

    if ($item->is_album()) {
      return new Gallery3_DAV_Album($path);
    } else {
      return new Gallery3_DAV_File($path);
    }
  }
}
