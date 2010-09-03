<?php defined("SYSPATH") or die("No direct script access.");
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
class Admin_Photoannotation_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var(
        "photoannotation", "showfaces", $form->photoannotation->showfaces->value, true);
      module::set_var(
        "photoannotation", "shownotes", $form->photoannotation->shownotes->value, true);
      message::success(t("Your settings have been saved."));
      url::redirect("admin/photoannotation");
    }
    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_photoannotation.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_form() {
    $form = new Forge("admin/photoannotation/handler", "", "post", array("id" => "g-admin-form"));
    $group = $form->group("photoannotation")->label(t("Photo annotation settings"));
    $group->checkbox("showfaces")->label(t("Show face annotation below photo."))
      ->checked(module::get_var("photoannotation", "showfaces", false));	
    $group->checkbox("shownotes")->label(t("Show note annotations below photo."))
      ->checked(module::get_var("photoannotation", "shownotes", false));	
    $form->submit("submit")->value(t("Save"));
    return $form;
  }
}
