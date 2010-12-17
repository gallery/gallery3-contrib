<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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
require_once(MODPATH . "webdav/vendor/Sabre/autoload.php");

class WebDAV_Controller extends Controller {
  public function gallery() {
    $root = new Gallery3_Album("");
    $tree = new Gallery3_DAV_Tree($root);

    // Skip the lock plugin for now, we don't want Finder to get write support for the time being.
    // $lock_backend = new Sabre_DAV_Locks_Backend_FS(TMPPATH . "sabredav");
    // $lock = new Sabre_DAV_Locks_Plugin($lock_backend);
    $filter = new Sabre_DAV_TemporaryFileFilterPlugin(TMPPATH . "sabredav");

    $server = new Sabre_DAV_Server($tree);
    $server->setBaseUri(url::site("webdav/gallery"));
    // $server->addPlugin($lock);
    $server->addPlugin($filter);

    if ($this->_authenticate()) {
      $server->exec();
    }
  }

  private function _authenticate() {
    $auth = new Sabre_HTTP_BasicAuth();
    $auth->setRealm(item::root()->title);
    $authResult = $auth->getUserPass();
    list($username, $password) = $authResult;

    if (!$username || !$password) {
      $auth->requireLogin();
      return false;
    }

    $user = identity::lookup_user_by_name($username);
    if (empty($user) || !identity::is_correct_password($user, $password)) {
      $auth->requireLogin();
      return false;
    }

    identity::set_active_user($user);
    return true;
  }
}

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
      return new Gallery3_Album($path);
    } else {
      return new Gallery3_File($path);
    }
  }
}

class Gallery3_Album extends Sabre_DAV_Directory {
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
      return new Gallery3_Album($rp);
    } else {
      return new Gallery3_File($rp);
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

class Gallery3_File extends Sabre_DAV_File {
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
