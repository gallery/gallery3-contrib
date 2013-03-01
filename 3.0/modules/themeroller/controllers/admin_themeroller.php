<?php defined("SYSPATH") or die("No direct script access.");/**
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
class Admin_Themeroller_Controller extends Admin_Controller {
  public function form_upload() {
    $v = new View("admin_themeroller_upload.html");

    list ($v->form, $v->errors) = $this->_get_upload_form();
    $v->is_writable = is_writable(THEMEPATH);
    $v->action = "admin/themeroller/form_create";
    $submit_class = "ui-state-default ui-corner-all submit g-left";

    if ($v->not_writable = !is_writable(THEMEPATH)) {
      $submit_class .= " ui-state-disabled";
    }
    $v->submit_class = $submit_class;
    $v->script_data = array(
      "g3sid" => Session::instance()->id(),
      "user_agent" => Input::instance()->server("HTTP_USER_AGENT"),
      "csrf" => access::csrf_token());
    json::reply(array("html" => (string) $v));
  }

  public function form_create() {
    $theme_name = Session::instance()->get_once("theme_name");
    json::reply(array("html" => (string) $this->_get_theme_form($theme_name)));
  }

  public function upload() {
    access::verify_csrf();

    $validation = new Validation(array_merge($_POST, $_FILES));
    $validation->add_rules("zip_file", "upload::valid",  "upload::required", "upload::type[zip]");
    $validation->add_rules("is_admin", "chars[0,1]");
    $validation->add_callbacks("zip_file", array($this, "_unload_zip"));
    if ($validation->validate()) {
      $session = Session::instance();
      $themeroller_name = $session->get("themeroller_name");
      $is_admin = $validation["is_admin"];
      $counter = 0;
      $theme_name_generated = $theme_name = ($is_admin ? "admin_" : "") . $themeroller_name;
      while (file_exists(THEMEPATH . "$theme_name_generated/theme.info")) {
        $counter++;
        $theme_name_generated = "{$theme_name}_{$counter}";
      }

      $theme_name = strtolower(strtr($theme_name_generated, " ", "_"));
      $session->set("theme_name", $theme_name);
      $session->set("themeroller_is_admin", $is_admin);
      print "FILEID: {$validation["zip_file"]["tmp_name"]}";
    } else {
      header("HTTP/1.1 400 Bad Request");
      print "ERROR: " . t("Invalid zip archive");
    }
  }

  public function create() {
    access::verify_csrf();

    $form = $this->_get_theme_form();
    if ($form->validate()) {
      $session = Session::instance();
      $extract_path = $session->get_once("theme_extract_path");
      $v = new View("admin_themeroller_progress.html");

      $task_def = Task_Definition::factory()
        ->callback("themeroller_task::create_theme")
        ->description(t("Generate theme from a themeroller archive"))
        ->name(t("Generate theme"));

      $v->task = task::create($task_def,
         array("path" => $extract_path,
               "user_name" => SafeString::purify(identity::active_user()->name),
               "original_name" => SafeString::purify($form->theme->original->value),
               "theme_name" => SafeString::purify($form->theme->theme_name->value),
               "display_name" => SafeString::purify($form->theme->display_name->value),
               "description" => SafeString::purify($form->theme->description->value),
               "author_url" => SafeString::purify($form->theme->author_url->value),
               "info_url" => SafeString::purify($form->theme->info_url->value),
               "discuss_url" => SafeString::purify($form->theme->discuss_url->value),
               "is_admin" => $session->get("themeroller_is_admin")));

      json::reply(array("html" => (string) $v));
    } else {
      json::reply(array("result" => "error", "html" => (string) $form));
    }
  }

  /**
   * Run the task of creating the theme
   */
  static function run($task_id) {
    access::verify_csrf();

    $task = ORM::factory("task", $task_id);
    if (!$task->loaded() || $task->owner_id != identity::active_user()->id) {
      access::forbidden();
    }

    $task = task::run($task_id);

    // Prevent the JavaScript code from breaking by forcing a period as
    // decimal separator for all locales with sprintf("%F", $value).
    json::reply(array("done" => (bool)$task->done,
                      "status" => (string)$task->status,
                      "percent_complete" => sprintf("%F", $task->percent_complete)));
  }

  static function _is_theme_defined($name) {
    $theme_name = strtolower(strtr($name->value, " ", "_"));
    if (file_exists(THEMEPATH . "$theme_name/theme.info")) {
      $name->add_error("conflict", 1);
    }
  }

  public function _unload_zip(Validation $post, $field) {
    $zipfile = $post["zip_file"]["tmp_name"];
    if (false !== ($extract_path = themeroller::extract_zip_file($zipfile))) {
      $theme_name = themeroller::get_theme_name($extract_path);
      if (!empty($theme_name)) {
        Session::instance()->set("themeroller_name", $theme_name);
      } else {
        Kohana_Log::add("error", "zip file: css directory not found");
        $post->add_error($field, "invalid zipfile");
      }
    } else {
      Kohana_Log::add("error", "zip file: open failed");
      $post->add_error($field, "invalid zipfile");
    }
    if (file_exists($zipfile)) {
      unlink($zipfile);
    }
  }

  private function _get_theme_form($theme_name=null) {
    $session = Session::instance();
    $form = new Forge("admin/themeroller/create", "", "post", array("id" => "g-themeroller-create-form"));
    $form_group = $form->group("theme")->label(t("Create theme"));
    $original_name = $form_group->hidden("original");
    $name_field = $form_group->input("theme_name")->label(t("Theme Name"))->id("g-name")
      ->rules("required")
      ->callback("Admin_Themeroller_Controller::_is_theme_defined")
      ->error_messages("conflict", t("There is already a theme with that name"))
      ->error_messages("required", t("You must enter a theme name"));
    $display_name = $form_group->input("display_name")->label(t("Display Name"))
      ->id("g-display-name")
      ->rules("required")
      ->error_messages("required", t("You must enter a theme display name"));
    if (!empty($theme_name)) {
      $name_field->value($theme_name);
      $is_admin = $session->get("themeroller_is_admin");
      $themeroller_name = $session->get("themeroller_name");
      $display_name->value(ucwords($is_admin ? t("%name administration theme",
                                                 array("name" => str_replace("-", " ", $themeroller_name))) :
                                               t("%name theme",
                                                 array("name" => str_replace("-", " ", $themeroller_name)))));
     $original_name->hidden("original")->value(Session::instance()->get("themeroller_name"));
    }
    $form_group->textarea("description")->label(t("Description"))
      ->id("g-description")
      ->value(t("A generated theme based on the ui themeroller '%name' styling",
              array("name" => str_replace("admin_", "", $theme_name))))
      ->rules("required")
      ->error_messages("required", t("You must enter a theme description name"));
    $form_group->input("author_url")->label(t("Author url"))->id("g-author-url");
    $form_group->input("info_url")->label(t("Info url"))->id("g-info-url");
    $form_group->input("discuss_url")->label(t("Theme Name"))->id("g-discuss-url");
    $form_group->submit("")->value(t("Create"));

    return $form;
  }

  private function _get_upload_form() {
    $form = array("zip_file" => "", "is_admin" => "");
    $errors = array_fill_keys(array_keys($form), "");
    return array($form, $errors);
  }
}