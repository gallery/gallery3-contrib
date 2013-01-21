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
class Admin_Dynamic_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    $errors = array_fill_keys(array_keys($form), "");
    if ($_POST) {
      $post = new Validation($_POST);
      $post->add_rules("updates_enabled", array("valid", "numeric"));
      $post->add_rules("popular_enabled", array("valid", "numeric"));
      $post->add_rules("updates_limit", array("valid", "numeric"));
      $post->add_rules("popular_limit", array("valid", "numeric"));
      $post->add_rules("updates_description", "length[0,2048]");
      $post->add_rules("popular_description", "length[0,2048]");
      if ($post->validate()) {
        foreach (array("updates", "popular") as $album) {
          $album_defn = unserialize(module::get_var("dynamic", $album));
          $album_defn->enabled = $post["{$album}_enabled"];
          $album_defn->description = $post["{$album}_description"];
          $album_defn->limit = $post["{$album}_limit"] === "" ? null : $post["{$album}_limit"];
          module::set_var("dynamic", $album, serialize($album_defn));
        }

        message::success(t("Dynamic Albums Configured"));

        url::redirect("admin/dynamic");
      } else {
        $form = arr::overwrite($form, $post->as_array());
        $errors = arr::overwrite($errors, $post->errors());
      }
    }

    print $this->_get_view($form, $errors);
  }

  private function _get_view($form=null, $errors=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_dynamic.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    $v->content->tabs = array("updates" => t("Recent changes"), "popular" => t("Most viewed"));
    $v->content->errors = $errors;
    return $v;
  }

  private function _get_form() {
    $form = array();
    foreach (array("updates", "popular") as $album) {
      $album_defn = unserialize(module::get_var("dynamic", $album));
      $form["{$album}_enabled"] = $album_defn->enabled;
      $form["{$album}_limit"] = $album_defn->limit;
      $form["{$album}_description"] = $album_defn->description;
    }

    return $form;
  }

  private function _get_form2() {

    $form = new Forge("admin/dynamic/handler", "", "post",
                      array("id" => "g-admin-form"));

    foreach (array("updates", "popular") as $album) {
      $album_defn = unserialize(module::get_var("dynamic", $album));

      $group = $form->group($album)->label(t($album_defn->title));
      $group->checkbox("{$album}_enabled")
        ->label(t("Enable"))
        ->value(1)
        ->checked($album_defn->enabled);
      $group->input("{$album}_limit")
        ->label(t("Limit (leave empty for unlimited)"))
        ->value(empty($album_defn->limit) ? "" : $album_defn->limit)
        ->rules("valid_numeric");
      $group->textarea("{$album}_description")
        ->label(t("Description"))
        ->rules("length[0,2048]")
        ->value($album_defn->description);
    }

    $form->submit("submit")->value(t("Submit"));

    return $form;
  }
}