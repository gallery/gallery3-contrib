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
 
class Admin_FotomotorW_Controller extends Admin_Controller {
  public function index() {
    // Display the admin page.
    $view = new Admin_View("admin.html");
    $view->page_title = t("Fotomoto");
    $view->content = new View("admin_fotomotorw.html");

    // Generate a form to allow the user to choose which links to display under photos.
    $form = new Forge("admin/fotomotorw/savedisplay", "", "post",
                      array("id" => "g-fotomotorw-admin-display-prefs"));

    $display_links_group = $form->group("fotomoto_display_links_group");
    $link_options["fotomoto_buy_prints"] = array(t("Buy Prints"), module::get_var("fotomotorw", "fotomoto_buy_prints"));
    $link_options["fotomoto_buy_cards"] = array(t("Buy Cards"), module::get_var("fotomotorw", "fotomoto_buy_cards"));
    $link_options["fotomoto_buy_download"] = array(t("Download"), module::get_var("fotomotorw", "fotomoto_buy_download"));
    $link_options["fotomoto_share_ecard"] = array(t("Send eCard"), module::get_var("fotomotorw", "fotomoto_share_ecard"));
    $link_options["fotomoto_share_facebook"] = array(t("Share on Facebook"), module::get_var("fotomotorw", "fotomoto_share_facebook"));
    $link_options["fotomoto_share_twitter"] = array(t("Share on Twitter"), module::get_var("fotomotorw", "fotomoto_share_twitter"));
    $link_options["fotomoto_share_digg"] = array(t("Share on Digg"), module::get_var("fotomotorw", "fotomoto_share_digg"));

    // Turn the array into a series of checkboxes.
    $display_links_group->checklist("fotomoto_display_links")
      ->options($link_options);

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    $view->content->display_form = $form;
    print $view;
  }

  public function reset_private_key() {
    // Generate a new (random) private key.
    module::set_var("fotomotorw", "fotomoto_private_key", md5(random::hash() . access::private_key()));
    message::success(t("Your Photomoto private key has been reset."));
    url::redirect("admin/fotomotorw");
  }

  public function savedisplay() {
    // Save the admin's preferences for which fotomoto links to display under each photo.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out which boxes where checked
    $linkOptions_array = Input::instance()->post("fotomoto_display_links");
    $buy_prints = false;
    $buy_cards = false;
    $buy_download = false;
    $share_ecard = false;
    $share_facebook = false;
    $share_twitter = false;
    $share_digg = false;
    for ($i = 0; $i < count($linkOptions_array); $i++) {
      if ($linkOptions_array[$i] == "fotomoto_buy_prints") {
        $buy_prints = true;
      }
      if ($linkOptions_array[$i] == "fotomoto_buy_cards") {
        $buy_cards = true;
      }
      if ($linkOptions_array[$i] == "fotomoto_buy_download") {
        $buy_download = true;
      }
      if ($linkOptions_array[$i] == "fotomoto_share_ecard") {
        $share_ecard = true;
      }
      if ($linkOptions_array[$i] == "fotomoto_share_facebook") {
        $share_facebook = true;
      }
      if ($linkOptions_array[$i] == "fotomoto_share_twitter") {
        $share_twitter = true;
      }
      if ($linkOptions_array[$i] == "fotomoto_share_digg") {
        $share_digg = true;
      }
    }

    // Save Settings.
    module::set_var("fotomotorw", "fotomoto_buy_prints", $buy_prints);
    module::set_var("fotomotorw", "fotomoto_buy_cards", $buy_cards);
    module::set_var("fotomotorw", "fotomoto_buy_download", $buy_download);
    module::set_var("fotomotorw", "fotomoto_share_ecard", $share_ecard);
    module::set_var("fotomotorw", "fotomoto_share_facebook", $share_facebook);
    module::set_var("fotomotorw", "fotomoto_share_twitter", $share_twitter);
    module::set_var("fotomotorw", "fotomoto_share_digg", $share_digg);

    // Display a success message and reload the admin page.
    message::success(t("Your Settings Have Been Saved."));
    url::redirect("admin/fotomotorw");
  }
}
