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
      if ($form->general->reset_defaults->value) {
        // reset all to defaults, redirect with message
        module::install("tag_cloud_html5");
        message::success(t("Tag cloud options reset successfully"));
        url::redirect("admin/tag_cloud_html5");
      }
      // save the new inputs
      module::set_var("tag_cloud_html5", "show_wholecloud_link", ($form->general->show_wholecloud_link->value == 1));
      module::set_var("tag_cloud_html5", "show_add_tag_form", ($form->general->show_add_tag_form->value == 1));
      module::set_var("tag_cloud_html5", "show_wholecloud_list", ($form->general->show_wholecloud_list->value == 1));
      foreach ($cfg['groups'] as $groupname => $grouptext) {
        module::set_var("tag_cloud_html5", "maxtags".$groupname, $form->{"size".$groupname}->{"maxtags".$groupname}->value);
        module::set_var("tag_cloud_html5", "width".$groupname, $form->{"size".$groupname}->{"width".$groupname}->value);
        module::set_var("tag_cloud_html5", "height".$groupname, $form->{"size".$groupname}->{"height".$groupname}->value);
        
        $optionsarray = array();
        // group size
        $optionsarray['shape'] = $form->{"size".$groupname}->{"shape".$groupname}->value;
        $optionsarray['zoom'] = $form->{"size".$groupname}->{"zoom".$groupname}->value;
        $optionsarray['stretchX'] = $form->{"size".$groupname}->{"stretchX".$groupname}->value;
        $optionsarray['stretchY'] = $form->{"size".$groupname}->{"stretchY".$groupname}->value;
        // group motion
        $optionsarray['maxSpeed'] = $form->{"motion".$groupname}->{"maxSpeed".$groupname}->value;
        $optionsarray['minSpeed'] = $form->{"motion".$groupname}->{"minSpeed".$groupname}->value;
        $optionsarray['deadZone'] = $form->{"motion".$groupname}->{"deadZone".$groupname}->value;
        $optionsarray['decel'] = $form->{"motion".$groupname}->{"decel".$groupname}->value;
        $optionsarray['initial'] = array($form->{"motion".$groupname}->{"initialX".$groupname}->value, $form->{"motion".$groupname}->{"initialY".$groupname}->value);
        $optionsarray['maxInputZone'] = $form->{"motion".$groupname}->{"maxInputZone".$groupname}->value;
        // group select
        $optionsarray['outlineMethod'] = $form->{"select".$groupname}->{"outlineMethod".$groupname}->value;
        $optionsarray['outlineOffset'] = $form->{"select".$groupname}->{"outlineOffset".$groupname}->value;
        $optionsarray['outlineColour'] = $form->{"select".$groupname}->{"outlineColour".$groupname}->value;
        $optionsarray['frontSelect'] = ($form->{"select".$groupname}->{"frontSelect".$groupname}->value == 1);
        // group appearance
        $optionsarray['textHeight'] = $form->{"appearance".$groupname}->{"textHeight".$groupname}->value;
        $optionsarray['textColour'] = $form->{"appearance".$groupname}->{"textColour".$groupname}->value;
        $optionsarray['textFont'] = $form->{"appearance".$groupname}->{"textFont".$groupname}->value;
        $optionsarray['depth'] = $form->{"appearance".$groupname}->{"depth".$groupname}->value;
        // options that are not explicitly defined in admin menu
        $optionsarray['wheelZoom'] = false; // otherwise scrolling through the page screws everything up (was a problem in v1)
        $optionsarray['initialDecel'] = true; // this was an option in v4, but it's sorta useless - use minSpeed for a related but better effect
        $optionsarray['physModel'] = true; // this is the big enhancement for v5, and is a major modification that I did to TagCanvas
        switch ($optionsarray['shape']) {
          case "hcylinder":
            // keep it horizontal - lock x-axis rotation
            $optionsarray['lock'] = "x";
            break;
          case "vcylinder":
            // keep it vertical - lock y-axis rotation
            $optionsarray['lock'] = "y";
            break;
          default:
            // do not lock either axis
            $optionsarray['lock'] = "";
        }
        module::set_var("tag_cloud_html5", "options".$groupname, json_encode($optionsarray));
      }
      // all done; redirect with message
      message::success(t("Tag cloud options updated successfully"));
      url::redirect("admin/tag_cloud_html5");
    }
    // not valid - print screen from existing form
    $this->_print_screen($form);
  }

  private function _get_config() {
    // these define the two variable name groups, along with their labels which are always shown with t() for i18n.
    $cfg['groups'] = array("_sidebar"=>t("Sidebar"), "_wholecloud"=>t("Whole cloud"));
    // this defines the separator that's used between the group name and the attribute, and is *not* put through t().
    $cfg['sep'] = " : ";
    // this is used in the labels of the width/height parameters
    $cfg['size'] = array("_sidebar"=>t("as fraction of sidebar width, e.g. 'g-block-content' class"), "_wholecloud"=>t("as fraction of browser window height"));
    return $cfg;
  }
  
  private function _print_screen($form) {
    // this part is a bit of a hack, but Forge doesn't seem to allow set_attr() for groups.
    $form = $form->render();
    $form = preg_replace("/<fieldset>/","<fieldset class=\"g-tag-cloud-html5-admin-form-top\">",$form,1);
    $form = preg_replace("/<fieldset>/","<fieldset class=\"g-tag-cloud-html5-admin-form-left\">",$form,4);
    $form = preg_replace("/<fieldset>/","<fieldset class=\"g-tag-cloud-html5-admin-form-right\">",$form,4);

    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tag_cloud_html5.html");
    $view->content->form = $form;
    print $view;
  }

  private function _get_admin_form() {
    $cfg = $this->_get_config();
    $sep = $cfg['sep'];
    
    // Make the main form.  This form has *nine* groups: general, then size, motion, select, and appearance for _sidebar and _wholecloud.
    $form = new Forge("admin/tag_cloud_html5/edit", "", "post", array("id" => "g-tag-cloud-html5-admin-form"));

    // group general
    $group_general = $form->group("general")->label(t("General"))->set_attr("id","g-tag-cloud-html5-admin-form-general");
    $group_general->checkbox("reset_defaults")
      ->label(t("Reset all to default values"))
      ->checked(false);
    $group_general->checkbox("show_wholecloud_link")
      ->label(t("Show 'View whole cloud' link in sidebar"))
      ->checked(module::get_var("tag_cloud_html5", "show_wholecloud_link", null));
    $group_general->checkbox("show_add_tag_form")
      ->label(t("Show 'Add tag to album' form in sidebar (when permitted and applicable)"))
      ->checked(module::get_var("tag_cloud_html5", "show_add_tag_form", null));
    $group_general->checkbox("show_wholecloud_list")
      ->label(t("Show inline tag list under cloud on 'View whole cloud' page")." {hideTags}")
      ->checked(module::get_var("tag_cloud_html5", "show_wholecloud_list", null));
    
    foreach ($cfg['groups'] as $groupname => $grouptext) {
      $maxtags =      strval(module::get_var("tag_cloud_html5", "maxtags".$groupname, null));
      $width =        strval(module::get_var("tag_cloud_html5", "width".$groupname, null));
      $height =       strval(module::get_var("tag_cloud_html5", "height".$groupname, null));
      $options = json_decode(module::get_var("tag_cloud_html5", "options".$groupname, null),true);

      // group size/shape
      ${"group_size".$groupname} = $form->group("size".$groupname)->label(t("Size and shape").$sep.$grouptext);
      ${"group_size".$groupname}->input("maxtags".$groupname)
        ->label(t("maximum tags shown"))
        ->value($maxtags)
        ->rules("required|numrange[0]");
      ${"group_size".$groupname}->input("width".$groupname)
        ->label(t("width")." (".$cfg['size'][$groupname].")")
        ->value($width)
        ->rules("required|numrange[0]");
      ${"group_size".$groupname}->input("height".$groupname)
        ->label(t("height")." (".$cfg['size'][$groupname].")")
        ->value($height)
        ->rules("required|numrange[0]");
      ${"group_size".$groupname}->dropdown("shape".$groupname)
        ->label(t("shape of cloud")." {shape,lock}")
        ->options(array("sphere"=>t("sphere"),"hcylinder"=>t("horizontal cylinder"),"vcylinder"=>t("vertical cylinder")))
        ->selected($options['shape']);
      ${"group_size".$groupname}->input("zoom".$groupname)
        ->label(t("zoom (<1.0 is zoom out, >1.0 is zoom in)")." {zoom}")
        ->value($options['zoom'])
        ->rules("required|numrange[0]");
      ${"group_size".$groupname}->input("stretchX".$groupname)
        ->label(t("x-axis stretch factor (<1.0 squishes, >1.0 stretches)")." {stretchX}")
        ->value($options['stretchX'])
        ->rules("required|numrange[0]");
      ${"group_size".$groupname}->input("stretchY".$groupname)
        ->label(t("y-axis stretch factor (<1.0 squishes, >1.0 stretches)")." {stretchY}")
        ->value($options['stretchY'])
        ->rules("required|numrange[0]");

      // group motion
      ${"group_motion".$groupname} = $form->group("motion".$groupname)->label(t("Motion").$sep.$grouptext);
      ${"group_motion".$groupname}->input("maxSpeed".$groupname)
        ->label(t("max speed (typically 0.01-0.20)")." {maxSpeed}")
        ->value($options['maxSpeed'])
        ->rules("required|numrange[0]");
      ${"group_motion".$groupname}->input("minSpeed".$groupname)
        ->label(t("no mouseover speed (typically 0.00-0.01)")." {minSpeed}")
        ->value($options['minSpeed'])
        ->rules("required|numrange[0]");
      ${"group_motion".$groupname}->input("deadZone".$groupname)
        ->label(t("dead zone size (0.0-1.0 - 0.0 is none and 1.0 is entire cloud)")." {deadZone}")
        ->value($options['deadZone'])
        ->rules("required|numrange[0,1]");
      ${"group_motion".$groupname}->input("decel".$groupname)
        ->label(t("inertia (0.0-1.0 - 0.0 changes velocity instantly and 1.0 never changes)")." {decel}")
        ->value($options['decel'])
        ->rules("required|numrange[0,1]");
      ${"group_motion".$groupname}->input("initialX".$groupname)
        ->label(t("initial horizontal speed (between +/-1.0, as fraction of max speed)")." {initial}")
        ->value($options['initial'][0])
        ->rules("required|numrange[-1,1]");
      ${"group_motion".$groupname}->input("initialY".$groupname)
        ->label(t("initial vertical speed (between +/-1.0, as fraction of max speed)")." {initial}")
        ->value($options['initial'][1])
        ->rules("required|numrange[-1,1]");
      ${"group_motion".$groupname}->input("maxInputZone".$groupname)
        ->label(t("mouseover region beyond cloud (as fraction of cloud - 0.0 is tight around cloud)")." {maxInputZone}")
        ->value($options['maxInputZone'])
        ->rules("required|numrange[0]");

      // group select
      ${"group_select".$groupname} = $form->group("select".$groupname)->label(t("Tag selection").$sep.$grouptext);
      ${"group_select".$groupname}->dropdown("outlineMethod".$groupname)
        ->label(t("change of display for selected tag")." {outlineMethod}")
        ->options(array("colour"=>t("change text color"),"outline"=>t("add outline around text"),"block"=>t("add block behind text")))
        ->selected($options['outlineMethod']);
      ${"group_select".$groupname}->input("outlineOffset".$groupname)
        ->label(t("mouseover region around tag text (in pixels - 0 is tight around text)")." {outlineOffset}")
        ->value($options['outlineOffset'])
        ->rules("required|numrange[0]");
      ${"group_select".$groupname}->input("outlineColour".$groupname)
        ->label(t("color used for change of display (as #hhhhhh)")." {outlineColour}")
        ->value($options['outlineColour'])
        ->rules('required|color');
      ${"group_select".$groupname}->checkbox("frontSelect".$groupname)
        ->label(t("only allow tags in front to be selected")." {frontSelect}")
        ->checked($options['frontSelect']);

      // group appearance
      ${"group_appearance".$groupname} = $form->group("appearance".$groupname)->label(t("Appearance").$sep.$grouptext);
      ${"group_appearance".$groupname}->input("textHeight".$groupname)
        ->label(t("text height (in pixels)")." {textHeight}")
        ->value($options['textHeight'])
        ->rules("required|numrange[0]");
      ${"group_appearance".$groupname}->input("textColour".$groupname)
        ->label(t("text color (as #hhhhhh, or empty to use theme color)")." {textColour}")
        ->value($options['textColour'])
        ->rules('color');
      ${"group_appearance".$groupname}->input("textFont".$groupname)
        ->label(t("text font family (empty to use theme font family)")." {textFont}")
        ->value($options['textFont'])
        ->rules("length[0,60]");
      ${"group_appearance".$groupname}->input("depth".$groupname)
        ->label(t("depth/perspective of cloud (0.0-1.0 - 0.0 is none and >0.9 gets strange)")." {depth}")
        ->value($options['depth'])
        ->rules("required|numrange[0,1]");
    }
    $form->submit("")->value(t("Save"));

    return $form;
  }

}
