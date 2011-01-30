<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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

class Admin_EmbedLinks_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_embedlinks.html");
    $view->content->embedlinks_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out which boxes where checked
    $linkOpts_array = Input::instance()->post("LinkCodeTypeOptions");
    $displayType_array = Input::instance()->post("LinkDisplayType");

    $HTMLButton = false;
    $BBCodeButton = false;
    $FullURLButton = false;
    $InPageLinks = false;
    
    for ($i = 0; $i < count($linkOpts_array); $i++) {
      if ($linkOpts_array[$i] == "HTMLCode") {
        $HTMLButton = true;
      }
      if ($linkOpts_array[$i] == "BBCode") {
        $BBCodeButton = true;
      }
      if ($linkOpts_array[$i] == "FullURL") {
        $FullURLButton = true;
      }
    }          
    for ($i = 0; $i < count($displayType_array); $i++) {
      if ($displayType_array[$i] == "InPageLinks") {
        $InPageLinks = true;
      }
    }
    
    // Save Settings.
    module::set_var("embedlinks", "HTMLCode", $HTMLButton);
    module::set_var("embedlinks", "BBCode", $BBCodeButton);
    module::set_var("embedlinks", "FullURL", $FullURLButton);
    module::set_var("embedlinks", "InPageLinks", $InPageLinks);
    message::success(t("Your Selection Has Been Saved."));
    
    // Load Admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_embedlinks.html");
    $view->content->embedlinks_form = $this->_get_admin_form();
    print $view;
  }

  private function _get_admin_form() {
    // Make a new Form.
    $form = new Forge("admin/embedlinks/saveprefs", "", "post",
                      array("id" => "g-embed-links-adminForm"));

    // Make an array for the different types of link codes.
    $linkCodes["HTMLCode"] = array(t("Show HTML Links"), module::get_var("embedlinks", "HTMLCode"));
    $linkCodes["BBCode"] = array(t("Show BBCode Links"), module::get_var("embedlinks", "BBCode"));
    $linkCodes["FullURL"] = array(t("Show the full URL"), module::get_var("embedlinks", "FullURL"));

    // Make an array for the different methods of displaying the links.
    $linkDisplays["InPageLinks"] = array(t("Show Links In The Actual Page"), module::get_var("embedlinks", "InPageLinks"));
    
    // Setup a few checkboxes on the form.
    $add_links = $form->group("EmbedLinks");
    $add_links->checklist("LinkCodeTypeOptions")
      ->options($linkCodes);
    $add_links->checklist("LinkDisplayType")
      ->options($linkDisplays);

    // Add a save button to the form.
    $add_links->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}