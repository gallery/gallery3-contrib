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
class g3_client_Core {
  static function get_form($type, $add_form, $path, $data=null) {
    $form = new stdClass();
    $form->adding = $add_form;
    $form->form = array("title" => (object)array("value" => "", "label" => "Title"),
                        "name" =>  (object)array("value" => "", "label" => "Name"),
                        "description" => (object)array("value" => "", "label" => "Description"),
                        "slug" => (object)array("value" => "", "label" => "Internet Address"));
    // @todo add sort column sort order fields
    $form->errors = array("title" => "", "name" => "", "description" => "", "slug" => "");
    if ($type != "album" && $add_form) {
      $form->form["image"] = (object)array("value" => "", "label" => "Image File");
      $form->errors["image"] = "";
    }

    if (empty($path) && !$add_form) {
      $form->form["name"]->readonly = $form->form["slug"]->readonly = "readonly";
    }

    if ($data) {
      foreach (array_keys($form->form) as $field) {
        if (isset($data->$field)) {
          $form->form[$field]->value = $data->$field;
        }
      }
    }
    return $form;
  }

  static function format_response($type, $path, $form, $result) {
    $json = (object)array("result" => $result);
    if ($result != "success") {
      $json->form = new View("edit.html");
      $json->form->title = ($form->adding ? "Add " : "Update ") . ucwords($type);
      $json->form->url = $form->adding ? "add" : "edit";
      $json->form->button_text = $form->adding ? "Add" : "Update";
      $json->form->path = $path;
      $json->form->type = $type;
      $json->form->form = (object)$form->form;
      $json->form->errors = (object)$form->errors;
      $json->form = (string)$json->form;
    } else {
      $json->path = $path;
      $json->type = $type;
    }

    return json_encode($json);
  }

  /**
   * Sanitize a filename into something safe
   * @param string $filename
   * @return string sanitized filename
   */
  static function sanitize_title($field, $title) {
    $result = preg_replace("/[^A-Za-z0-9-_]+/", "-", empty($field) ? $title : $field);
    return trim($result, "-");
  }
}
