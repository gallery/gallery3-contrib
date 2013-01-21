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

class Admin_Star_Controller extends Admin_Controller {
  
  public function index() {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Star settings");
    $view->content = new View("admin_star.html");
    $view->content->form = $this->_get_admin_form();
    $view->content->title = $view->page_title;
    print $view;
  }

  public function save() {
    access::verify_csrf();
    $form = $this->_get_admin_form();
    $form->validate();
    module::set_var("star", "show",
                    $form->show->value);
    message::success(t("Star settings updated"));
    url::redirect("admin/star");
  }

  private function _get_admin_form() {
    $form = new Forge("admin/star/save", "", "post",
                      array("id" => "g-star-admin-form"));
    $form->dropdown("show")
      ->label(t("Default to showing..."))
      ->options(array(0 => "All",1 => "Starred"))
      ->selected(module::get_var("star", "show"));
    $form->submit("save")->value(t("Save"));
    return $form;
  }
}
