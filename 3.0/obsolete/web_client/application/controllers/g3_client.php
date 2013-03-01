<?php defined('SYSPATH') OR die('No direct access allowed.');
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
class G3_Client_Controller extends Template_Controller {
  // Set the name of the template to use
  public $template = 'g3_template.html';

  public function index() {
    $this->template->title = 'G3 Web Client';

    if (Session::instance()->get("g3_client_access_token")) {
      $response = G3Remote::instance()->get_resource("gallery");
      $this->template->content = $this->_get_main_view($response->resource);
    } else {
      $this->template->content = new View('login.html');
      $this->template->content->errors = $this->template->content->form =
        array("user" => "", "password" => "");
    }
  }

  public function login() {
    $form = $errors = array("user" => "", "password" => "");
    $post = new Validation($_POST);
    $post->add_rules("user", "required");
    $post->add_rules("password", "required");
    if ($valid = $post->validate()) {
      try {
        $token = G3Remote::instance()->get_access_token($post["user"], $post["password"]);
        Session::instance()->set("g3_client_access_token", $token);
        $response = G3Remote::instance()->get_resource("gallery");
        $valid = true;
        $content = $this->_get_main_view($response->resource);
      } catch (Exception $e) {
        Kohana_Log::add("error", Kohana_Exception::text($e));
        $valid = false;
      }
    }

    if (!$valid) {
      $content = new View('login.html');
      $content->form = arr::overwrite($form, $post->as_array());
      $content->errors = arr::overwrite($errors, $post->errors());
    }

    $this->auto_render = false;
    print json_encode(array("status" => $valid ? "ok" : "error", "content" => (string)$content));
  }

  public function albums() {
    $path = $this->input->get("path");
    $response = G3Remote::instance()->get_resource("gallery/$path", array("filter" => "album"));
    $this->auto_render = false;
    print $this->_get_album_tree($response->resource);
  }

  public function detail() {
    $path = $this->input->get("path");
    $response = G3Remote::instance()->get_resource("gallery/$path");
    $this->auto_render = false;
    print $this->_get_detail($response->resource);
  }

  public function tagged_album($tags) {
    $response = G3Remote::instance()->get_resource("tag/$tags");
    $this->auto_render = false;
    $v = new View("tag_detail.html");
    $v->resources = $response->resources;
    print $v;
  }

  public function block($type) {
    switch ($type) {
    case "random":
      print $this->_get_image_block();
      break;
    case "tags":
      print "";
      break;
    default:
      print "";
    }
    $this->auto_render = false;
  }

  private function _get_album_tree($resource) {
    $v = new View("tree_part.html");
    $v->element = (object)array("title" => $resource->title, "path" => $resource->path);
    $v->element->children = array();
    foreach ($resource->children as $child) {
      if ($child->type != "album") {
        continue;
      }
      $v->element->children[] = $child;
    }
    return $v;
  }

  private function _get_main_view($resource) {
    $v = new View("main.html");
    $v->album_tree = $this->_get_album_tree($resource);
    $v->detail = $this->_get_detail($resource);
    $v->image_block = $this->_get_image_block();
    $v->tag_block = $this->_get_tag_block();
    return $v;
  }

  private function _get_detail($resource) {
    $v = new View("{$resource->type}_detail.html");
    $v->resource = $resource;
    $v->parent_path = substr($resource->path, 0, -strlen($resource->slug));
    if (strrpos($v->parent_path, "/") == strlen($v->parent_path) - 1) {
      $v->parent_path = substr($v->parent_path, 0, -1);
    }
    return $v;
  }

  private function _get_image_block() {
    $response = G3Remote::instance()->get_resource("image_block", array("type" => "random"));
    if ($response->status == "OK") {
      $v = new View("image_block.html");
      $v->path = $response->resource->path;
      $v->src = $response->resource->thumb_url;
      $v->title = $response->resource->title;
    } else {
      $v = "";
    }
    return $v;
  }

  private function _get_tag_block() {
    $response = G3Remote::instance()->get_resource("tag", array("limit" => "15"));
    if ($response->status == "OK") {
      $v = new View("tag_block.html");
      $v->tags = $response->tags;
      $v->max_count = $response->tags[0]->count;;
    } else {
      $v = "";
    }
    return $v;
  }
} // End G3 Client Controller
