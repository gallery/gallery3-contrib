<?php defined("SYSPATH") or die("No direct script access.");
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
class Admin_Tag_Cloud_Controller extends Admin_Controller {
  public function index() {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tag_cloud.html");
    $view->content->form = $this->_get_admin_form();

    print $view;
  }

  public function edit() {
    access::verify_csrf();

    $form = $this->_get_admin_form();
    if ($form->validate()) {
      $options = $form->tag_cloud_options;
      $valid = true;
      if (preg_match("/^0x[0-9A-Fa-f]{6}$/", $options->tagcolor->value) == 0) {
        $options->tagcolor->add_error("not_valid", 1);
        $valid = false;
      }
      if (preg_match("/^0x[0-9A-Fa-f]{6}$/", $options->background_color->value) == 0) {
        $options->background_color->add_error("not_valid", 1);
        $valid = false;
      }
      if ($valid) {
        module::set_var("tag_cloud", "tagcolor", $options->tagcolor->value);
        module::set_var("tag_cloud", "mouseover", $options->mouseover->value);
        module::set_var("tag_cloud", "background_color", $options->background_color->value);
        module::set_var("tag_cloud", "transparent", $options->transparent->value);
        module::set_var("tag_cloud", "speed", $options->speed->value);
        module::set_var("tag_cloud", "distribution", $options->distribution->value);
        message::success(t("Tag cloud options updated successfully"));
        url::redirect("admin/tag_cloud");
      }
    }

    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tag_cloud.html");
    $view->content->form = $form;

    print $view;
  }

  private function _get_admin_form() {
    $form = new Forge("admin/tag_cloud/edit", "", "post",
                      array("id" => "g-tag-cloud-admin-form"));
    $group = $form->group("tag_cloud_options")->label(t("Tag Cloud Options"));
    $group->input("tagcolor")    ->label(t("Tag color"))
      ->value(module::get_var("tag_cloud", "tagcolor", "0x333333"))
      ->error_message("not_valid", t("The color value must be specified as '0xhhhhhh'"))
      ->rules("required|length[8]");
    $group->input("mouseover")    ->label(t("Tag mouseover color"))
      ->value(module::get_var("tag_cloud", "mouseover", "0x000000"))
      ->error_message("not_valid", t("The color value must be specified as '0xhhhhhh'"))
      ->rules("required|length[8]");
    $group->input("background_color")->label(t("Background color"))
      ->value(module::get_var("tag_cloud", "background_color", "0xffffff"))
      ->error_message("not_valid", t("The color value must be specified as '0xhhhhhh'"))
      ->rules("required|length[8]");
    $group->checkbox("transparent")->label(t("Transparent mode"))
      ->checked(module::get_var("tag_cloud", "transparent", 0) == 1);
    $group->input("speed")->label(t("Rotation speed"))
      ->value(module::get_var("tag_cloud", "speed", "100"))
      ->rules("required|valid_numeric|length[1,3]");
    $group->checkbox("distribution")->label(t("Distribute tags evenly"))
      ->checked(module::get_var("tag_cloud", "distribution", 1) == 1);
    $group->submit("")->value(t("Save"));

    return $form;
  }
}
