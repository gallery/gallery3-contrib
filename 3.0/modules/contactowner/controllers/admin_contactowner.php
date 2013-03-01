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

class Admin_ContactOwner_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_contactowner.html");
    $view->content->contactowner_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out which boxes where checked
    $linkOptions_array = Input::instance()->post("ContactOwnerLinkTypes");
    $ownerLink = false;
    $userLink = false;
    for ($i = 0; $i < count($linkOptions_array); $i++) {
      if ($linkOptions_array[$i] == "ContactOwner") {
        $ownerLink = true;
      }
      if ($linkOptions_array[$i] == "ContactUser") {
        $userLink = true;
      }
    }

    // Figure out the values of the text boxes
    $str_contactbutton = Input::instance()->post("owner_button_text");
    $str_contactemail = Input::instance()->post("owner_email");
    $str_contactname = Input::instance()->post("owner_name");
    $str_messageheader = Input::instance()->post("message_header");

    // Save Settings.
    module::set_var("contactowner", "contact_owner_link", $ownerLink);
    module::set_var("contactowner", "contact_user_link", $userLink);
    module::set_var("contactowner", "contact_button_text", $str_contactbutton);
    module::set_var("contactowner", "contact_owner_email", $str_contactemail);
    module::set_var("contactowner", "contact_owner_name", $str_contactname);
    module::set_var("contactowner", "contact_owner_header", $str_messageheader);
    message::success(t("Your Settings Have Been Saved."));

    // Load Admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_contactowner.html");
    $view->content->contactowner_form = $this->_get_admin_form();
    print $view;
  }

  private function _get_admin_form() {
    // Make a new Form.
    $form = new Forge("admin/contactowner/saveprefs", "", "post",
                      array("id" => "g-contact-owner-adminForm"));

    // Make an array for the different types of link codes.
    $add_contactlinks = $form->group("contactOwnerLinks");
    $linkOptions["ContactOwner"] = array("Display Contact Site Owner Link", 
                                   module::get_var("contactowner", "contact_owner_link"));
    $linkOptions["ContactUser"] = array("Display Contact Item Owner Link", 
                                  module::get_var("contactowner", "contact_user_link"));
                                  
    // Turn the array into a series of checkboxes.
    $add_contactlinks->checklist("ContactOwnerLinkTypes")
      ->options($linkOptions);

    // Set up some text boxes for the site owners Name, email and the
    //   text for the contact link.
    $add_contacts = $form->group("contactOwner");
    $add_contacts->input("owner_button_text")->label(t("Contact Owner Link Text"))->value(module::get_var("contactowner", "contact_button_text"));
    $add_contacts->input("owner_email")->label(t("Owner Email Address"))->value(module::get_var("contactowner", "contact_owner_email"));
    $add_contacts->input("owner_name")->label(t("Owner Name"))->value(module::get_var("contactowner", "contact_owner_name"));

    $message_prefs = $form->group("messagePrefs");
    $message_prefs->input("message_header")->label(t("Email Message Header"))->value(module::get_var("contactowner", "contact_owner_header"));      

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}