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

class Admin_Minislideshow_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_minislideshow.html");
    $view->content->minislideshow_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Process the admin form.
     
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Save user specified settings to the database.
    $str_slideshow_url = Input::instance()->post("slideshow_url");
    module::set_var("minislideshow", "slideshow_url", $str_slideshow_url);
    
    $str_slideshow_shuffle = Input::instance()->post("shuffle");
    module::set_var("minislideshow", "shuffle", $str_slideshow_shuffle);
    
    $str_slideshow_dropshadow = Input::instance()->post("dropshadow");
    module::set_var("minislideshow", "dropshadow", $str_slideshow_dropshadow);
    
    $str_slideshow_show_title = Input::instance()->post("show_title");
    module::set_var("minislideshow", "show_title", $str_slideshow_show_title);
    
    $str_slideshow_trans_in_type = Input::instance()->post("trans_in_type");
    module::set_var("minislideshow", "trans_in_type", $str_slideshow_trans_in_type);

    $str_slideshow_trans_out_type = Input::instance()->post("trans_out_type");
    module::set_var("minislideshow", "trans_out_type", $str_slideshow_trans_out_type);

    $str_slideshow_mask = Input::instance()->post("mask");
    module::set_var("minislideshow", "mask", $str_slideshow_mask);
    
    $str_slideshow_use_full_image = Input::instance()->post("use_full_image");
    module::set_var("minislideshow", "use_full_image", $str_slideshow_use_full_image);
    
    $str_slideshow_delay = Input::instance()->post("delay");
    module::set_var("minislideshow", "delay", $str_slideshow_delay);
        
    // Display a success message and load the admin screen.
    message::success(t("Your Settings Have Been Saved."));    
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_minislideshow.html");
    $view->content->minislideshow_form = $this->_get_admin_form();
    print $view;
  }

  private function _get_admin_form() {
    // Generate a form for configuring the slideshow.
    
    // Make a new Form.
    $form = new Forge("admin/minislideshow/saveprefs", "", "post",
                      array("id" => "g-mini-slideshow-admin-form"));

    // Get location of slideshow files.
    $group_slideshow_files = $form->group("Minislideshow");
    $group_slideshow_files->input("slideshow_url")
                          ->label(t("URL to your minislideshow.swf"))
                          ->value(module::get_var("minislideshow", "slideshow_url"));

    // Get additional settings for the slideshow.
    $group_slideshow_settings = $form->group("MinislideshowSettings");
    $group_slideshow_settings->label(t("MiniSlide Show Settings"));
    $group_slideshow_settings->dropdown('shuffle')
                             ->label(t("Shuffle:"))
                             ->options(array('true'=>'True', ''=>'False'))
                             ->selected(module::get_var("minislideshow", "shuffle"));
    $group_slideshow_settings->dropdown('dropshadow')
                             ->label(t("Drop Shadow:"))
                             ->options(array('true'=>'True', ''=>'False'))
                             ->selected(module::get_var("minislideshow", "dropshadow"));
    $group_slideshow_settings->dropdown('show_title')
                             ->label(t("Show Title:"))
                             ->options(array('top'=>'Top', 'bottom'=>'Bottom', ''=>'False'))
                             ->selected(module::get_var("minislideshow", "show_title"));
    $group_slideshow_settings->dropdown('trans_in_type')
                             ->label(t("Transition In:"))
                             ->options(array('Blinds'=>'Blinds', ''=>'Fade', 'Fly'=>'Fly', 'Iris'=>'Iris', 'Photo'=>'Photo', 'PixelDissolve'=>'Pixel Dissolve', 'Rotate'=>'Rotate', 'Squeeze'=>'Squeeze', 'Wipe'=>'Wipe', 'Zoom'=>'Zoom', 'Random'=>'Random'))
                             ->selected(module::get_var("minislideshow", "trans_in_type"));
    $group_slideshow_settings->dropdown('trans_out_type')
                             ->label(t("Transition Out:"))
                             ->options(array('Blinds'=>'Blinds', ''=>'Fade', 'Fly'=>'Fly', 'Iris'=>'Iris', 'Photo'=>'Photo', 'PixelDissolve'=>'Pixel Dissolve', 'Rotate'=>'Rotate', 'Squeeze'=>'Squeeze', 'Wipe'=>'Wipe', 'Zoom'=>'Zoom', 'Random'=>'Random'))
                             ->selected(module::get_var("minislideshow", "trans_out_type"));
    $group_slideshow_settings->dropdown('mask')
                             ->label(t("Mask:"))
                             ->options(array(''=>'None', 'circleMask'=>'Circle', 'roundedMask'=>'Rounded Corners', 'starMask'=>'Star'))
                             ->selected(module::get_var("minislideshow", "mask"));
    $group_slideshow_settings->dropdown('use_full_image')
                             ->label(t("Use Full Image:"))
                             ->options(array('true', 'false', 'Use Resize'))
                             ->selected(module::get_var("minislideshow", "use_full_image"));
    $group_slideshow_settings->input("delay")
                          ->label(t("Delay:"))
                          ->value(module::get_var("minislideshow", "delay"));

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}
