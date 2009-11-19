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

class Admin_theme_3nids_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_theme_3nids.html");

    // Generate a form for Google Maps Settings.
    $view->content->theme_form = $this->_get_3nids_form();
    

    // Display the page.
    print $view;
  }


  private function _get_3nids_form() {
    // Make a new form for inputing information associated with google maps.
    $form = new Forge("admin/theme_3nids/save3nidsprefs", "", "post",
                      array("id" => "gTagsMapAdminForm"));

    // Input box for the Maps API Key
    $form->input("title")
                 ->label(t("item title : parent or item."))
                 ->value(module::get_var("theme_3nids", "title"));
    $form->input("description")
                 ->label(t("item description : tags or item or parent or nothing. If item description chosen and not available, then parent description is used."))
                 ->value(module::get_var("theme_3nids", "description"));
    $form->input("photo_size")
                 ->label(t("Photo size: resize or full."))
                 ->value(module::get_var("theme_3nids", "photo_size"));

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
  
  public function save3nidsprefs() {
    // Save information associated with Google Maps to the database.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out the values of the text boxes
    $description = Input::instance()->post("description");
    $title = Input::instance()->post("title");
    $photo_size = Input::instance()->post("photo_size");
    
    // Save Settings.
    module::set_var("theme_3nids", "description", $description);
    module::set_var("theme_3nids", "title", $title);
    module::set_var("theme_3nids", "photo_size", $photo_size);

    // Display a success message and redirect back to the TagsMap admin page.
    message::success(t("Your Settings Have Been Saved."));
    url::redirect("admin/theme_3nids");
  }
}