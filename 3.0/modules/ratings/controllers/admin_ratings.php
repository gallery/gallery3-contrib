<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
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

class Admin_Ratings_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_ratings.html");
    $view->content->ratings_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Save Settings.
    module::set_var("ratings", "showunderphoto", Input::instance()->post("showunderphoto"));
    module::set_var("ratings", "showinsidebar", Input::instance()->post("showinsidebar"));
    module::set_var("ratings", "imageword", Input::instance()->post("imageword"));
    module::set_var("ratings", "votestring", Input::instance()->post("votestring"));
    module::set_var("ratings", "castyourvotestring", Input::instance()->post("castyourvotestring"));
#    module::set_var("ratings", "bgcolor", Input::instance()->post("bgcolor"));
    module::set_var("ratings", "fillcolor", Input::instance()->post("fillcolor"));
    module::set_var("ratings", "votedcolor", Input::instance()->post("votedcolor"));
    module::set_var("ratings", "hovercolor", Input::instance()->post("hovercolor"));
    module::set_var("ratings", "textcolor", Input::instance()->post("textcolor"));
    module::set_var("ratings", "regonly", Input::instance()->post("regonly"));
    
    $iconset = Input::instance()->post("iconset");
    $iconset = preg_replace("/\/index\.php/","",$iconset);
    module::set_var("ratings", "iconset", $iconset);

    message::success(t("Your Settings Have Been Saved."));
    site_status::clear("ratings_configuration");

    // Load Admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_ratings.html");
    $view->content->ratings_form = $this->_get_admin_form();
    print $view;
  }

  private function _get_admin_form() {
    if($showunderphoto = module::get_var("ratings", "showunderphoto")){ $showunderphoto = 1; };
    if($showinsidebar = module::get_var("ratings", "showinsidebar")){ $showinsidebar = 1; };

    if(!$fillcolor = module::get_var("ratings","fillcolor")){ $fillcolor = "#FF0000"; }
    if(!$votedcolor = module::get_var("ratings","votedcolor")){ $votedcolor = "#0069FF"; }
    if(!$hovercolor = module::get_var("ratings","hovercolor")){ $hovercolor = "#FFA800"; }
    if(!$castyourvotestring = module::get_var("ratings","castyourvotestring")){ $castyourvotestring="Click on a heart to cast your vote:"; }

    $iconsets[url::file("modules/ratings/vendor/img/")."hearts.png"] = 1;
    $iconsets[url::file("modules/ratings/vendor/img/")."filmstrip.png"] = 2;
    $iconsets[url::file("modules/ratings/vendor/img/")."stars.png"] = 3;
    $iconsets[url::file("modules/ratings/vendor/img/")."camera.png"] = 4;

    $regonlyvote[1] = "true";
    $regonlyvote[0] = "false";

    // Make a new Form.
    $form = new Forge("admin/ratings/saveprefs", "", "post",
                      array("id" => "g-ratings-api-adminForm"));

    // Set up some text boxes for the site owners Name, email and the
    //   text for the contact link.
    $ratings_form = $form->group("RatingsBlockSettings");
    $ratings_form->dropdown("regonly")
      ->label(t("Allow only registered users to vote"))
      ->options($regonlyvote)
      ->selected(module::get_var("ratings","regonly"));
    $ratings_form->dropdown("iconset")
      ->label(t("Choose an Icon Set"))
      ->options($iconsets)
      ->selected(module::get_var("ratings","iconset"));
#     $ratings_form->input("bgcolor")
#      ->label(t("Background Color (behind icon set) [--  not yet working --]"))
#      ->class("js_color {hash:true}")
#      ->value(module::get_var("ratings","bgcolor"));
     $ratings_form->input("fillcolor")
      ->label(t("Fill Color"))
      ->class("js_color {hash:true}")
      ->value(module::get_var("ratings","fillcolor"));
     $ratings_form->input("hovercolor")
      ->label(t("Hover Fill Color"))
      ->class("js_color {hash:true}")
      ->value(module::get_var("ratings","hovercolor"));
     $ratings_form->input("votedcolor")
      ->label(t("Once Voted Fill Color"))
      ->class("js_color {hash:true}")
      ->value(module::get_var("ratings","votedcolor"));
    $ratings_form->checkbox("showunderphoto")
      ->label(t("Show block under photo"))
      ->class("g-unique g-button-text")
      ->checked($showunderphoto);
    $ratings_form->checkbox("showinsidebar")
      ->label(t("Show block in the sidebar"))
      ->class("g-unique g-button-text")
      ->checked($showinsidebar);
    $ratings_form->input("imageword")
      ->label(t("Word to descibe the rating icon (IE: heart or star or filmstrip)"))
      ->class("g-button-text")
      ->value(module::get_var("ratings", "imageword"));
    $ratings_form->input("votestring")
      ->label(t("Word for 'vote'"))
      ->class("g-button-text")
      ->value(module::get_var("ratings", "votestring"));
    $ratings_form->input("castyourvotestring")
      ->label(t("Wording for 'Click on a heart to cast your vote:'"))
      ->class("g-button-text")
      ->value(module::get_var("ratings", "castyourvotestring"));
     $ratings_form->input("textcolor")
      ->label(t("Text Color"))
      ->class("js_color {hash:true}")
      ->value(module::get_var("ratings","textcolor"));

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}
