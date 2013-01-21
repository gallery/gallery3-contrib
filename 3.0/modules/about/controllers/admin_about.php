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
class Admin_About_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var(
        "about", "code", $form->about->about_code->value);
      module::set_var(
        "about", "title", $form->about->about_title->value);
	  module::set_var (
	    "about", "hidden", $form->about->about_hidden->value);
      message::success(t("Your settings have been saved."));
      url::redirect("admin/about");
    }

    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_about.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_form() {
    $form = new Forge("admin/about/handler", "", "post", array("id" => "g-admin-form"));
    $group = $form->group("about");
    $group->input("about_title")->label(t('Enter the headline.'))->value(module::get_var("about", "title"));
    $group->textarea("about_code")->label(t('Enter the standard HTML code you want on the page.'))->value(module::get_var("about", "code"));
    $group->checkbox("about_hidden")->label(t("Hide link"))
    	->checked(module::get_var("about", "hidden", false) == 1);
    $group->submit("submit")->value(t("Save"));

    return $form;
  }
}