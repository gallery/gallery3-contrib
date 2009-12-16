<?php defined('SYSPATH') OR die('No direct access allowed.');
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
class G3_Client_Controller extends Template_Controller {
  // Set the name of the template to use
  public $template = 'g3_template.html';

  public function index() {
    $this->template->title = 'G3 Web Client';

    if (Session::instance()->get("g3_client_access_token")) {
      $resource = G3Remote::instance()->get_resource("gallery");
      $this->template->content = $this->_get_main_view($resource);
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
    if ($valid =$post->validate()) {
      try {
        $token = G3Remote::instance()->get_access_token($post["user"], $post["password"]);
        Session::instance()->set("g3_client_access_token", $token);
        $resource = G3Remote::instance()->get_resource("gallery");
        $valid = true;
        $content = $this->_get_main_view($resource);
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
    $resource = G3Remote::instance()->get_resource("gallery/$path", "album");
    $this->auto_render = false;
    print $this->_get_album_tree($resource);
  }

  public function detail() {
    $path = $this->input->get("path");
    $resource = G3Remote::instance()->get_resource("gallery/$path");
    $this->auto_render = false;
    print $this->_get_detail($resource);
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
    return $v;
  }

  private function _get_detail($resource) {
    $v = new View("{$resource->type}_detail.html");
    $v->resource = $resource;
    return $v;
  }
} // End G3 Client Controller
