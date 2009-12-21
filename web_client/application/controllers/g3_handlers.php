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
class G3_Handlers_Controller extends Controller {
  public function edit($type) {
    $path = $this->input->get("path");
    if ($_POST) {
      try {
        unset($_POST["submit"]);
        $result = G3Remote::instance()->update_resource("gallery/$path", $_POST);
        if ($result->status == "OK") {
          $form = null;
          $result = "success";
        } else {
          $form = g3_client::get_form($type, false, $path, (object)$_POST);
          foreach (array_keys($_POST) as $field) {
            if (isset($result->fields->$field)) {
              $form->errors[$field] = $result->fields->$field;
            }
          }
          $result = "display";
        }
      } catch (Exception $e) {
        $form = g3_client::get_form($type, false, $path, (object)$_POST);
        $form->errors["form_error"] = $e->getMessage();
        $result = "error";
      }
    } else {
      $response = G3Remote::instance()->get_resource("gallery/$path");
      $form = g3_client::get_form($type, false, $path, $response->resource);
      $result = "display";
    }

    print g3_client::format_response($type, $path, $form, $result);
  }

  public function add($type) {
    $path = $this->input->get("path");
    if ($_POST) {
        unset($_POST["submit"]);
        $_POST["name"] = empty($_POST["name"]) ? $_POST["title"] : $_POST["name"];
        $_POST["name"] = g3_client::sanitize_filename($_POST["name"]);
        $_POST["slug"] = empty($_POST["slug"]) ? $_POST["title"] : $_POST["slug"];
        $_POST["slug"] = g3_client::sanitize_slug($_POST["slug"]);
        $result = G3Remote::instance()->add_resource("gallery/$path/{$_POST['slug']}", $_POST);
        if ($result->status == "OK") {
          $form = null;
          $result = "success";
        } else {
          $form = g3_client::get_form($type, true, $path, (object)$_POST);
          foreach (array_keys($_POST) as $field) {
            if (isset($result->fields->$field)) {
              $form->errors[$field] = $result->fields->$field;
            }
          }
          $result = "display";
        }
    } else {
      $form = g3_client::get_form($type, true, $path);
      $result = "display";
    }

    print g3_client::format_response($type, $path, $form, $result);
  }

  public function delete($type) {
    $path = $this->input->get("path");
    if ($_POST) {
      try {
        $response = G3Remote::instance()->delete_resource("gallery/$path");
        print json_encode(array("result" => "success", "path" => $response->resource->parent_path,
                                "type" => $type));
      } catch (Exception $e) {
        print json_encode(array("result" => "fail", "message" => $e->getMessage()));
      }
      return;
    } else {
      $response = G3Remote::instance()->get_resource("gallery/$path");
      $v = new View("delete.html");
      $v->title = $response->resource->title;
      $v->path = "delete_album/?path=$path";
    }

    print json_encode(array("form" => (string)$v));
  }
} // End G3 Album Controller
