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

class Admin_Hide_Controller extends Admin_Controller {
  
  public function index() {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Item hiding settings");
    $view->content = new View("admin_hide.html");
    $view->content->form = $this->_get_admin_form();
    $view->content->title = $view->page_title;
    print $view;
  }

  public function save() {
    access::verify_csrf();
    $form = $this->_get_admin_form();
    $form->validate();
    module::set_var("hide", "access_permissions",
                    $form->access_permissions->value);
    message::success(t("Item hiding settings updated"));
    url::redirect("admin/hide");
  }

  private function _get_admin_form() {
    $form = new Forge("admin/hide/save", "", "post",
                      array("id" => "g-hide-admin-form"));
    $form->dropdown("access_permissions")
      ->label(t("Who can see hidden items?"))
      ->options(hide::get_groups_as_dropdown_options())
      ->selected(module::get_var("hide", "access_permissions"));
    $form->submit("save")->value(t("Save"));
    return $form;
  }
}
