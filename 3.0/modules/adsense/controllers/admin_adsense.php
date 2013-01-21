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
class Admin_adsense_Controller extends Admin_Controller {
  public function index() {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Adsense settings");
    $view->content = new View("admin_adsense.html");
    $view->content->form = $this->_get_admin_form();
    print $view;
  }

  public function save() {
    access::verify_csrf();
    $form = $this->_get_admin_form();
    if ($form->validate()) {
      module::set_var("adsense", "code", $form->adsense->code->value);
	  module::set_var("adsense", "location", $form->adsense->location->value);
      message::success(t("Adsense settings updated"));
      url::redirect("admin/adsense");
    } else {
      print $form;
    }
  }

  private function _get_admin_form() {
    $form = new Forge("admin/adsense/save", "", "post", array("id" => "g-adsense-admin-form"));
    $adsense_settings = $form->group("adsense")->label(t("Adsense settings"));
    $adsense_settings->textarea("code")->label(t("Adsense code"))
      ->value(module::get_var("adsense", "code"));
    $adsense_settings->dropdown("location")
      ->label(t("Where should the ads be displayed?"))
      ->options(array("header" => t("In the header"),
                      "sidebar" => t("In the sidebar"),
					  "footer" => t("In the footer")))
      ->selected(module::get_var("adsense", "location"));	  
    $adsense_settings->submit("save")->value(t("Save"));
    return $form;
  }
}

