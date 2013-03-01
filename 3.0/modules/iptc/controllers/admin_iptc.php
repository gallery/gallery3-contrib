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

class Admin_Iptc_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_iptc.html");
    $view->content->iptc_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Save user preferences to the database.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Make sure the user filled out the form properly.
    $form = $this->_get_admin_form();
    if ($form->validate()) {
      Kohana_Log::add("error",print_r($form,1));

      // Save settings to Gallery's database.
      foreach (iptc::keys() as $keyword => $iptcvar) {
        $checkbox = false;
        for ($i = 0; $i < count($form->Global->$keyword); $i++) {
          if ($form->Global->$keyword->value[$i] == $keyword) {
            $checkbox = true;
          }
        }
        module::set_var("iptc", "show_".$keyword, $checkbox);
      }
      // Display a success message and redirect back to the TagsMap admin page.
      message::success(t("Your settings have been saved."));
      url::redirect("admin/iptc");
    }

    // Else show the page with errors
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_iptc.html");
    $view->content->iptc_form = $form;
    print $view;
  }

  private function _get_admin_form() {
    // Make a new Form.
    $form = new Forge("admin/iptc/saveprefs", "", "post", array("id" => "g-iptc-adminForm"));

    // Create group for display settings
    $iptc_display_group = $form->group("Global")
      ->label(t("Display Settings"));

    $show = t("Show");
    foreach (iptc::keys() as $keyword => $iptcvar) {
      unset($checkbox);
      $checkbox[$keyword] = array($show." \"".$iptcvar[1]."\" ?", module::get_var("iptc", "show_".$keyword));
      $iptc_display_group->checklist($keyword)
        ->options($checkbox);
    }
    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}
