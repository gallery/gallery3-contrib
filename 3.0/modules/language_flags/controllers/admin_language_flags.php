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

class Admin_Language_Flags_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_language_flags.html");
    $view->content->preferences_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Save Settings
    module::set_var("language_flags", "flag_shape", Input::instance()->post("flag_shape"));

    // Load Admin page.
    message::success(t("Your Selection Has Been Saved."));
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_language_flags.html");
    $view->content->preferences_form = $this->_get_admin_form();
    print $view;
  }

  private function _get_admin_form() {
    // Make a new Form.
    $form = new Forge("admin/language_flags/saveprefs", "", "post",
                      array("id" => "g-language-flags-adminForm"));

    // Figure out what type of flags to display.
    $group_flag_types = $form->group("flag_types");
    $group_flag_types->dropdown('flag_shape')
                             ->label(t("Flag Shape:"))
                             ->options(array('rectangular'=>'Rectangular', 'round'=>'Round', 'square'=>'Square', 'custom'=>'Custom'))
                             ->selected(module::get_var("language_flags", "flag_shape"));

    // Add a save button to the form.
    $form->submit("SavePrefs")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}