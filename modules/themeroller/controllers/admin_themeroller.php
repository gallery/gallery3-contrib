<?php defined("SYSPATH") or die("No direct script access.");/**
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
class Admin_Themeroller_Controller extends Admin_Controller {
  public function form_add() {
    $v = new View("admin_themeroller.html");

    list ($v->form, $v->errors) = $this->_get_theme_form();
    $v->is_writable = is_writable(THEMEPATH);
    $v->action = "admin/themeroller/upload";
    $submit_class = "ui-state-default ui-corner-all submit g-left";

    if ($v->not_writable = !is_writable(THEMEPATH)) {
      $submit_class .= " ui-state-disabled";
    }
    $v->submit_class = $submit_class;
    $v->script_data = array(
      "g3sid" => Session::instance()->id(),
      "user_agent" => Input::instance()->server("HTTP_USER_AGENT"),
      "csrf" => access::csrf_token());
    print json_encode(array("form" => (string) $v));
  }

  public function upload() {
    access::verify_csrf();

    list ($v->form, $v->errors) = $this->_get_theme_form();

    Kohana_Log::add("error", Kohana::debug($_POST));
    Kohana_Log::add("error", Kohana::debug($_FILES));
    $validation = new Validation(array_merge($_POST, $_FILES));
    $validation->add_rules("name", "required");
    $validation->add_rules("display_name", "required");
    $validation->add_rules("description", "required");
    $validation->add_rules("zip_file", "upload::valid",  "upload::required", "upload::type[zip]");
    $validation->add_callbacks("name", array($this, "_is_theme_defined"));
    if ($validation->validate()) {
      print "FILEID: {$v->form['name']}";
    } else {
      arr::overwrite($form, $validation->as_array());
      arr::overwrite($errors, $validation->errors());
    }
  }

  public function _is_theme_defined(Validation $post, $field) {
    $theme_name = strtolower(strtr($post[$field], " ", "_"));
    if (file_exists(THEMEPATH . "$theme_name/theme.info")) {
      $post->add_error($field, "theme_exists");
    }
  }

  private function _get_theme_form() {
    $form = array("name" => "", "display_name" => "", "description" => "", "is_admin" => array(),
                  "zip_file" => "");
    $errors = array_fill_keys(array_keys($form), "");
    return array($form, $errors);
  }
}