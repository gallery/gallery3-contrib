<?php
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
include("Mail.php");
include("Mail/mime.php");
include("HTTP/Request.php");

class Gallery3 {
  var $url;
  var $token;
  var $data;
  var $changed_data;
  var $file;
  var $parent;

  public function __construct() {
    $this->data = new stdClass();
    $this->changed_data = new stdClass();
  }

  /**
   * Connect to a remote Gallery3 instance
   *
   * @param   string Gallery 3 API url, eg http://example.com/gallery3/index.php/rest
   * @param   string username
   * @param   string password
   * @return  object Gallery3
   */
  static function connect($url, $user, $pass) {
    $response = Gallery3_Helper::request(
      "post", $url, null, array("user" => $user, "password" => $pass));

    return self::factory($url, $response, null);
  }

  /**
   * Create a new Gallery3 instance associated with a remote resource
   * @param string   the url
   * @param string   security token
   * @param object   parent object
   * @return object  Gallery3
   */
  static function factory($url, $token, $parent) {
    $resource = new Gallery3();
    $resource->url = $url;
    $resource->token = $token;
    $resource->parent = $parent;
    return $resource;
  }

  /**
   * Retrieve a remote resource, by url.
   *
   * @param string   the path relative to the current resource
   * @return object  Gallery3
   */
  public function get($relative_path, $params=array()) {
    $query = "";
    if ($params) {
      foreach ($params as $key => $value) {
        $query[] = rawurlencode($key) . "=" . rawurlencode($value);
      }
      if ($query) {
        $query = "?" . join("&", $query);
      }
    }

    return self::factory("$this->url/$relative_path$query", $this->token, $this)->load();
  }

  /**
   * Set a value on the remote resource
   *
   * @param string   key
   * @param string   value
   * @return object  Gallery3
   */
  public function set_value($key, $value) {
    $this->changed_data->$key = $value;
    return $this;
  }

  /**
   * Get a value from the remote resource.
   *
   * @param string   $key
   * @return string  value
   */
  public function __get($key) {
    if (property_exists($this->changed_data, $key)) {
      return $this->changed_data->$key;
    }
    return $this->data->$key;
  }

  /**
   * Get the list of members from the remote resource
   *
   * @return array  member urls
   */
  public function members() {
    return $this->members;
  }

  /**
   * Attach a file to the remote resource.
   *
   * @param string   path to a local file (eg: /tmp/foo.jpg)
   * @return object  Gallery3
   */
  public function set_file($file) {
    $this->file = $file;
    return $this;
  }

  /**
   * Add a new member to a collection.  You must call save() for it to be created in the
   * remote Gallery 3.
   *
   * @return object  Gallery3
   */
  public function add() {
    return Gallery3::factory(null, $this->token, $this);
  }

  /**
   * Save any local changes made to this resource.
   *
   * @return object  Gallery3
   */
  public function save() {
    if ($this->url) {
      $response = Gallery3_Helper::request("put", $this->url, $this->token, $this->changed_data);
    } else {
      $response = Gallery3_Helper::request(
        "post", $this->parent->url, $this->token, $this->changed_data, $this->file);
      $this->parent->load();
    }

    return $this->load(!empty($response->url) ? $response->url : null);
  }

  /**
   * Delete the remote resource.
   *
   * @return object  Gallery3
   */
  public function delete() {
    Gallery3_Helper::request("delete", $this->url, $this->token);
    $this->reset();
  }

  /**
   * Remove a member from the remote collection.
   *
   * @return object  Gallery3
   */
  public function remove($url) {
    Gallery3_Helper::request("delete", $this->url, $this->token, array("url" => $url));
    $this->load();
  }

  /**
   * Reload the resource from a given url.  This is useful after the remote resource has been
   * modified.
   *
   * @param  string   optional url, only necessary if the url changes.
   * @return object   Gallery3
   */
  protected function load($url=null) {
    if ($url) {
      $this->url = $url;
    }
    $response = Gallery3_Helper::request("get", $this->url, $this->token);

    $this->data = isset($response->resource) ? $response->resource : new stdClass();
    $this->members = isset($response->members) ? $response->members : array();
    $chis->changed_data = new stdClass();
    return $this;
  }

  /**
   * Reset all data for this reference, essentially disconnecting it from the remote resource.
   *
   * @return object   Gallery3
   */
  protected function reset() {
    $this->data = array();
    $this->changed_data = array();
    $this->url = null;
    return $this;
  }
}

class Gallery3_Helper {
  static function request($method, $url, $token=null, $params=array(), $file=null) {
    $req = new HTTP_Request($url);
    $req->setMethod($method == "get" ? HTTP_REQUEST_METHOD_GET : HTTP_REQUEST_METHOD_POST);
    $req->addHeader("X-Gallery-Request-Method", $method);
    if ($token) {
      $req->addHeader("X-Gallery-Request-Key", $token);
    }
    foreach ($params as $key => $value) {
      $req->addPostData($key, $value);
    }
    if ($file) {
      $req->addFile("file", $file, mime_content_type($file));
    }
    $req->sendRequest();

    switch ($req->getResponseCode()) {
    case 200:
      return json_decode($req->getResponseBody());

    case 403:
      throw new Gallery3_Forbidden_Exception($req->getResponseBody());

    default:
      throw new Gallery3_Exception($req->getResponseBody());
    }
  }
}

class Gallery3_Exception extends Exception {
}

class Gallery3_Forbidden_Exception extends Gallery3_Exception {
}