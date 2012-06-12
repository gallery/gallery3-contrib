<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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
class Admin_Tag_Cloud_Html5_Controller extends Admin_Controller {
  public function index() {
    // print screen from new form
    $form = $this->_get_admin_form();
    $this->_print_screen($form);
  }

  public function edit() {
    access::verify_csrf();
    $cfg = $this->_get_config();
    $form = $this->_get_admin_form();
    if ($form->validate()) {
      if ($form->options_general->load_defaults->value) {
        // reset all to defaults, redirect with message
        module::install("tag_cloud_html5");
        message::success(t("Tag cloud options reset successfully"));
        url::redirect("admin/tag_cloud_html5");
      } else {
        $valid = true;
        // run checks on various inputs
        $options_general = $form->options_general;
        if ($options_general->height_sidebar->value < 0) {
          $form->options_general->height_sidebar->add_error("not_valid", 1);
          $valid = false;
        }
        foreach ($cfg['groups'] as $groupname => $grouptext) {
          ${"options".$groupname} = $form->{"options".$groupname};
          if ($options_general->{"maxtags".$groupname}->value < 0) {
            $form->options_general->{"maxtags".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if (${"options".$groupname}->{"maxSpeed".$groupname}->value < 0) {
            $form->{"options".$groupname}->{"maxSpeed".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if ((${"options".$groupname}->{"initialX".$groupname}->value < -1) || (${"options".$groupname}->{"initialX".$groupname}->value > 1)) {
            $form->{"options".$groupname}->{"initialX".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if ((${"options".$groupname}->{"initialY".$groupname}->value < -1) || (${"options".$groupname}->{"initialY".$groupname}->value > 1)) {
            $form->{"options".$groupname}->{"initialY".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if ((${"options".$groupname}->{"deadZone".$groupname}->value < 0) || (${"options".$groupname}->{"deadZone".$groupname}->value > 1)) {
            $form->{"options".$groupname}->{"deadZone".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if (${"options".$groupname}->{"zoom".$groupname}->value < 0) {
            $form->{"options".$groupname}->{"zoom".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if ((${"options".$groupname}->{"depth".$groupname}->value < 0) || (${"options".$groupname}->{"depth".$groupname}->value > 1)) {
            $form->{"options".$groupname}->{"depth".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if (${"options".$groupname}->{"outlineOffset".$groupname}->value < 0) {
            $form->{"options".$groupname}->{"outlineOffset".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if (preg_match("/^#[0-9A-Fa-f]{6}$/", ${"options".$groupname}->{"outlineColour".$groupname}->value) == 0) {
            $form->{"options".$groupname}->{"outlineColour".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if ((preg_match("/^#[0-9A-Fa-f]{6}$/", ${"options".$groupname}->{"textColour".$groupname}->value) == 0) && (strcmp(${"options".$groupname}->{"textColour".$groupname}->value, "") != 0) ) {
            $form->{"options".$groupname}->{"textColour".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
          if (${"options".$groupname}->{"textHeight".$groupname}->value < 0) {
            $form->{"options".$groupname}->{"textHeight".$groupname}->add_error("not_valid", 1);
            $valid = false;
          }
        }
        if ($valid) {
          // all inputs passed tests above; save them
          module::set_var("tag_cloud_html5", "show_wholecloud_link", ($options_general->show_wholecloud_link->value == 1));
          module::set_var("tag_cloud_html5", "show_add_tag_form", ($options_general->show_add_tag_form->value == 1));
          module::set_var("tag_cloud_html5", "height_sidebar", $options_general->height_sidebar->value);
          module::set_var("tag_cloud_html5", "show_wholecloud_list", ($options_general->show_wholecloud_list->value == 1));
          foreach ($cfg['groups'] as $groupname => $grouptext) {
            module::set_var("tag_cloud_html5", "maxtags".$groupname, $options_general->{"maxtags".$groupname}->value);
            
            $optionsarray = array();
            $optionsarray['maxSpeed'] = ${"options".$groupname}->{"maxSpeed".$groupname}->value;
            $optionsarray['deadZone'] = ${"options".$groupname}->{"deadZone".$groupname}->value;
            $optionsarray['initial'] = array(${"options".$groupname}->{"initialX".$groupname}->value, ${"options".$groupname}->{"initialY".$groupname}->value);
            $optionsarray['initialDecel'] = (${"options".$groupname}->{"initialDecel".$groupname}->value == 1);
            $optionsarray['zoom'] = ${"options".$groupname}->{"zoom".$groupname}->value;
            $optionsarray['depth'] = ${"options".$groupname}->{"depth".$groupname}->value;
            $optionsarray['outlineMethod'] = ${"options".$groupname}->{"outlineMethod".$groupname}->value;
            $optionsarray['outlineOffset'] = ${"options".$groupname}->{"outlineOffset".$groupname}->value;
            $optionsarray['outlineColour'] = ${"options".$groupname}->{"outlineColour".$groupname}->value;
            $optionsarray['textColour'] = ${"options".$groupname}->{"textColour".$groupname}->value;
            $optionsarray['textFont'] = ${"options".$groupname}->{"textFont".$groupname}->value;
            $optionsarray['textHeight'] = ${"options".$groupname}->{"textHeight".$groupname}->value;
            $optionsarray['frontSelect'] = (${"options".$groupname}->{"frontSelect".$groupname}->value == 1);
            $optionsarray['wheelZoom'] = false; // note that this is locked - otherwise scrolling through the page screws everything up
            module::set_var("tag_cloud_html5", "options".$groupname, json_encode($optionsarray));
          }
          // all done; redirect with message
          message::success(t("Tag cloud options updated successfully"));
          url::redirect("admin/tag_cloud_html5");
        }
      }
    }
    // print screen from existing form - you wind up here if something wasn't validated
    $this->_print_screen($form);
  }

  private function _get_config() {
    // these define the two variable name groups, along with their labels which are always shown with t() for i18n.
    $cfg['groups'] = array("_sidebar"=>"Sidebar", "_wholecloud"=>"Whole cloud");
    // this defines the separator that's used between the group name and the attribute, and is *not* put through t().
    $cfg['sep'] = ": ";
    return $cfg;
  }
  
  private function _print_screen($form) {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tag_cloud_html5.html");
    $view->content->form = $form;
    print $view;
  }

  private function _get_admin_form() {
    $cfg = $this->_get_config();
    $sep = $cfg['sep'];
    
    // Make the form.  This form has three groups: group_general, group_sidebar, and group_wholecloud.
    $form = new Forge("admin/tag_cloud_html5/edit", "", "post", array("id" => "g-tag-cloud-html5-admin-form"));

    // group_general
    $group_general = $form->group("options_general")->label(t("Tag cloud options").$sep.t("General"));
    $group_general->checkbox("load_defaults")
      ->label(t("Reset all to default values"))
      ->checked(false);
    $group_general->checkbox("show_wholecloud_link")
      ->label(t("Show 'View whole cloud' link in sidebar"))
      ->checked(module::get_var("tag_cloud_html5", "show_wholecloud_link", null));
    $group_general->checkbox("show_add_tag_form")
      ->label(t("Show 'Add tag to album' form in sidebar (when permitted and applicable)"))
      ->checked(module::get_var("tag_cloud_html5", "show_add_tag_form", null));
    $group_general->input("height_sidebar")
      ->label(t("Height of sidebar (as fraction of width)"))
      ->value(round(module::get_var("tag_cloud_html5", "height_sidebar", null),3)) // round or else it gets 6 decimal places...
      ->error_message("not_valid", t("Height of sidebar must be a 1-5 digit number"))
      ->rules("required|valid_numeric|length[1,5]");
    $group_general->checkbox("show_wholecloud_list")
      ->label(t("Show tag list under cloud on 'View whole cloud' page"))
      ->checked(module::get_var("tag_cloud_html5", "show_wholecloud_list", null));
   
    foreach ($cfg['groups'] as $groupname => $grouptext) {
      // maxtags - note that this is displayed under group_general!
      $maxtags = module::get_var("tag_cloud_html5", "maxtags".$groupname, null);
      $group_general->input("maxtags".$groupname)
        ->label(t($grouptext).$sep.t("max tags shown"))
        ->value($maxtags)
        ->error_message("not_valid", t("Max tags must be a 1-4 digit number"))
        ->rules("required|valid_numeric|length[1,4]");
      // group_sidebar and group_wholecloud
      $options = json_decode(module::get_var("tag_cloud_html5", "options".$groupname, null),true);
      ${"group".$groupname} = $form->group("options".$groupname)->label(t("Tag cloud options").$sep.t($grouptext));
      ${"group".$groupname}->input("maxSpeed".$groupname)
        ->label(t($grouptext).$sep.t("max speed (typically 0.01-0.20)"))
        ->value($options['maxSpeed'])
        ->error_message("not_valid", t("Max speed must be a 1-5 digit number"))
        ->rules("required|valid_numeric|length[1,5]");
      ${"group".$groupname}->input("initialX".$groupname)
        ->label(t($grouptext).$sep.t("initial horizontal speed (between +/-1.0, as fraction of max speed)"))
        ->value($options['initial'][0])
        ->error_message("not_valid", t("Initial horizontal speed must be a 1-4 digit number"))
        ->rules("required|valid_numeric|length[1,4]");
      ${"group".$groupname}->input("initialY".$groupname)
        ->label(t($grouptext).$sep.t("initial vertical speed (between +/-1.0, as fraction of max speed)"))
        ->value($options['initial'][1])
        ->error_message("not_valid", t("Initial vertical speed must be a 1-4 digit number"))
        ->rules("required|valid_numeric|length[1,4]");
      ${"group".$groupname}->checkbox("initialDecel".$groupname)
        ->label(t($grouptext).$sep.t("initial deceleration (if false, the initial speed is held until a mouseover event)"))
        ->checked($options['initialDecel']);
      ${"group".$groupname}->input("deadZone".$groupname)
        ->label(t($grouptext).$sep.t("dead zone (0.0-1.0, where 0.0 is no dead zone and 1.0 is no active zone)"))
        ->value($options['deadZone'])
        ->error_message("not_valid", t("Dead zone must be a 1-4 digit number"))
        ->rules("required|valid_numeric|length[1,4]");
      ${"group".$groupname}->input("zoom".$groupname)
        ->label(t($grouptext).$sep.t("zoom (<1.0 is zoom out, >1.0 is zoom in)"))
        ->value($options['zoom'])
        ->error_message("not_valid", t("Zoom must be a 1-4 digit number"))
        ->rules("required|valid_numeric|length[1,4]");
      ${"group".$groupname}->input("depth".$groupname)
        ->label(t($grouptext).$sep.t("depth (0.0-1.0)"))
        ->value($options['depth'])
        ->error_message("not_valid", t("Depth must be a 1-4 digit number"))
        ->rules("required|valid_numeric|length[1,4]");
      ${"group".$groupname}->dropdown("outlineMethod".$groupname)
        ->label(t($grouptext).$sep.t("outline method (mouseover event)"))
        ->options(array("colour"=>t("change text color"),"outline"=>t("add outline around text"),"block"=>t("add block behind text")))
        ->selected($options['outlineMethod']);
      ${"group".$groupname}->input("outlineOffset".$groupname)
        ->label(t($grouptext).$sep.t("outline offset (mouseover region size around text, in pixels)"))
        ->value($options['outlineOffset'])
        ->error_message("not_valid", t("Outline offset must be a 1-2 digit number"))
        ->rules("required|valid_numeric|length[1,2]");
      ${"group".$groupname}->input("outlineColour".$groupname)
        ->label(t($grouptext).$sep.t("outline color (mouseover color, as #hhhhhh)"))
        ->value($options['outlineColour'])
        ->error_message("not_valid", t("Outline color must be specified as #hhhhhh"))
        ->rules("required|length[7]");
      ${"group".$groupname}->input("textColour".$groupname)
        ->label(t($grouptext).$sep.t("text color (as #hhhhhh, or empty to use theme color)"))
        ->value($options['textColour'])
        ->error_message("not_valid", t("Text color must be specified as empty or #hhhhhh"))
        ->rules("length[0,7]");
      ${"group".$groupname}->input("textFont".$groupname)
        ->label(t($grouptext).$sep.t("text font family (empty to use theme font)"))
        ->value($options['textFont'])
        ->error_message("not_valid", t("Text font must be empty or a 0-40 character string"))
        ->rules("length[0,40]");
      ${"group".$groupname}->input("textHeight".$groupname)
        ->label(t($grouptext).$sep.t("text height (in pixels)"))
        ->value($options['textHeight'])
        ->error_message("not_valid", t("Text height must be a 1-2 digit number"))
        ->rules("required|valid_numeric|length[1,2]");
      ${"group".$groupname}->checkbox("frontSelect".$groupname)
        ->label(t($grouptext).$sep.t("only allow tags in front to be selected"))
        ->checked($options['frontSelect']);
    
    }

    $form->submit("")->value(t("Save"));

    return $form;
  }

}
