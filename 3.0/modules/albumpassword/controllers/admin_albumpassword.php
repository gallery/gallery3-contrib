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

class Admin_Albumpassword_Controller extends Admin_Controller {
  public function index() {

    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_albumpassword.html");

    // Generate a form for controlling the admin section.
    $view->content->albumpassword_form = $this->_get_admin_form();

    // Display the page.
    print $view;
  }

  private function _get_admin_form() {
    // Make a new form for changing admin settings for this module.
    $form = new Forge("admin/albumpassword/saveprefs", "", "post",
                      array("id" => "g-album-password-admin-form"));

    // Should protected items be hidden, or completely in-accessable?
    $albumpassword_group = $form->group("album_password_group");
    $albumpassword_group->checkbox("hideonly")
                        ->label(t("Do not require passwords"))
                        ->checked(module::get_var("albumpassword", "hideonly"));

    // Add a save button to the form.
    $albumpassword_group->submit("save_settings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
  
  public function saveprefs() {
    // Save user specified preferences.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Retrieve submitted form data.
    if (Input::instance()->post("hideonly") == false) {
      module::set_var("albumpassword", "hideonly", false);
    } else {
      module::set_var("albumpassword", "hideonly", true);
    }
      // Display a success message and redirect back to the TagsMap admin page.
      message::success(t("Your settings have been saved."));
      url::redirect("admin/albumpassword");
  }
}
