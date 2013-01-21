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
class Admin_SharePhoto_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_sharephoto.html");
    $view->content->sharephoto_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    $form = $this->_get_admin_form();
	
    // Figure out which boxes where checked
    $shareOpts_array = Input::instance()->post("ShareOptions");
 
    $IconsButton = false;
	$HTMLLinksButton = false;
    
    for ($i = 0; $i < count($shareOpts_array); $i++) {
      if ($shareOpts_array[$i] == "Icons") {
        $IconsButton = true;
      }
      if ($shareOpts_array[$i] == "HTMLLinks") {
        $HTMLLinksButton = true;
      }
    }  
	
    // Save Settings.
    module::set_var("sharephoto", "Icons", $IconsButton);
	module::set_var("sharephoto", "HTMLLinks", $HTMLLinksButton);
    message::success(t("Your Selection Has Been Saved."));
    
    // Load Admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_sharephoto.html");
    $view->content->sharephoto_form = $form;
    print $view;
  }

  private function _get_admin_form() {
    // New Form.
    $form = new Forge("admin/sharephoto/saveprefs", "", "post",
                      array("id" => "g-sharephoto-adminForm"));

    // Select what to show on the Photo page.
	$shareTypes["Icons"] = array(t("Show Icons &nbsp;&nbsp;"), module::get_var("sharephoto", "Icons"));
    $shareTypes["HTMLLinks"] = array(t("Show HTML Links"), module::get_var("sharephoto", "HTMLLinks"));
	
    // Checkboxes
    $add_links = $form->group("SharePhoto");
    $add_links->checklist("ShareOptions")
      ->options($shareTypes);

    // Save button 
    $add_links->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}