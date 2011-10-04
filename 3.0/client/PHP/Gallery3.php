<?php
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
  var $file;

  protected $original_entity;

  /**
   * Connect to a remote Gallery3 instance
   *
   * @param   string Gallery 3 API url, eg http://example.com/gallery3/index.php/rest
   * @param   string username
   * @param   string password
   * @return  string authentication token
   */
  static function login($url, $user, $pass) {
    $response = Gallery3_Helper::request(
      "post", $url, null, array("user" => $user, "password" => $pass));
    return $response;
  }

  /**
   * Construct a new Gallery3 instance associated with a remote resource
   * @param   string remote url
   * @param   string authentication token
   * @return  object Gallery3
   */
  static function factory($url=null, $token=null) {
    $obj = new Gallery3();
    $obj->token = $token;
    $obj->url = $url;
    if ($url && $token) {
      $obj->load();
    }
    return $obj;
  }

  /**
   * Constructor.
   */
  public function __construct() {
    $this->data = new stdClass();
    $this->token = null;
    $this->url = null;
  }

  /**
   * Set a value on the remote resource's entity.  You must call save for it to take effect.
   *
   * @param string   key
   * @param string   value
   * @return object  Gallery3
   * @chainable
   */
  public function set($key, $value) {
    $this->data->entity->$key = $value;
    return $this;
  }

  /**
   * Replace the members for the remote resource
   *
   * @param array    members
   * @return object  Gallery3
   * @chainable
   */
  public function set_members($members) {
    $this->data->members = $members;
    return $this;
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
   * Save any local changes made to this resource.  If this is an existing resource, we'll return
   * the resource itself.  If we're creating a new resource, return the newly created resource.
   *
   * @return object  Gallery3
   */
  public function create($url, $token) {
    if (!is_string($url)) {
      throw new Gallery3_Exception("Invalid url: " . var_export($url));
    }

    $response = Gallery3_Helper::request(
      "post", $url, $token, array("entity" => $this->data->entity), $this->file);
    $this->url = $response->url;
    $this->token = $token;
    return $this->load();
  }

  /**
   * Save any local changes made to this resource.  If this is an existing resource, we'll return
   * the resource itself.  If we're creating a new resource, return the newly created resource.
   *
   * @return object  Gallery3
   */
  public function save() {
    $data = array();
    $data["entity"] = array_diff((array)$this->data->entity, $this->original_entity);
    if (isset($this->data->members)) {
      $data["members"] = $this->data->members;
    }
    if ($this->file) {
      $response = Gallery3_Helper::request("put", $this->url, $this->token, $data, $this->file);
    } else {
      $response = Gallery3_Helper::request("put", $this->url, $this->token, $data);
    }
    return $this->load();
  }

  /**
   * Delete the remote resource.
   *
   * @return object  Gallery3
   */
  public function delete() {
    Gallery3_Helper::request("delete", $this->url, $this->token);
    $this->data = array();
    $this->url = null;
    return $this;
  }

  /**
   * Reload the resource from a given url.  This is useful after the remote resource has been
   * modified.
   *
   * @return object   Gallery3
   */
  public function load() {
    $response = Gallery3_Helper::request("get", $this->url, $this->token);
    $this->data = $response;
    $this->original_entity = isset($response->entity) ? (array)$response->entity : null;
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
      $req->addPostData($key, is_string($value) ? $value : json_encode($value));
    }
    if ($file) {
      $req->addFile("file", $file, mime_content_type($file));
    }
    $req->sendRequest();

    switch ($req->getResponseCode()) {
    case 200:
    case 201:
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