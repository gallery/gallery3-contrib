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

class Admin_DownloadFullsize_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_downloadfullsize.html");
    $view->content->downloadlinks_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out which boxes where checked
    $dlLinks_array = Input::instance()->post("DownloadLinkOptions");
    $fButton = false;
    $download_original_button = false;
    for ($i = 0; $i < count($dlLinks_array); $i++) {
      if ($dlLinks_array[$i] == "fButton") {
        $fButton = true;
      }
    }

    if (module::is_active("keeporiginal")) {
      $keeporiginal_array = Input::instance()->post("DownloadOriginalOptions");
      for ($i = 0; $i < count($keeporiginal_array); $i++) {
        if ($keeporiginal_array[$i] == "DownloadOriginalImage") {
          $download_original_button = true;
        }
      }
      module::set_var("downloadfullsize", "DownloadOriginalImage", $download_original_button);
    }

    // Save Settings.
    module::set_var("downloadfullsize", "fButton", $fButton);
    message::success(t("Your Selection Has Been Saved."));

    // Load Admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_downloadfullsize.html");
    $view->content->downloadlinks_form = $this->_get_admin_form();
    print $view;

  }

  private function _get_admin_form() {
    // Make a new Form.
    $form = new Forge("admin/downloadfullsize/saveprefs", "", "post",
                      array("id" => "g-download-fullsize-adminForm"));

    // Make an array for the different types of download links.
    $linkOptions["fButton"] = array(t("Show Floppy Disk Picture Link"), module::get_var("downloadfullsize", "fButton"));

    // Setup a few checkboxes on the form.
    $add_links = $form->group("DownloadLinks");
    $add_links->checklist("DownloadLinkOptions")
      ->options($linkOptions);

    if (module::is_active("keeporiginal")) {
      $KeepOriginalOptions["DownloadOriginalImage"] = array(t("Allow visitors to download the original image when available?"), module::get_var("downloadfullsize", "DownloadOriginalImage"));
      $keeporiginal_group = $form->group("KeepOriginalPrefs")
                               ->label(t("KeepOriginal Preferences"));
      $keeporiginal_group->checklist("DownloadOriginalOptions")
                       ->options($KeepOriginalOptions);
    }

    // Add a save button to the form.
    $form->submit("SaveLinks")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}