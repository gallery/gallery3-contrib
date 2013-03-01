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
class Admin_Image_Optimizer_Controller extends Admin_Controller {

  public function index() {
    // print screen from new form
    $form = $this->_get_admin_form();
    $this->_print_screen($form);
  }

  public function save() {
    access::verify_csrf();
    $form = $this->_get_admin_form();
    if ($form->validate()) {
      foreach (array('jpg', 'png', 'gif') as $type) {
        module::set_var("image_optimizer", "path_".$type, $form->paths->{"path_".$type}->value);
        module::set_var("image_optimizer", "optlevel_thumb_".$type, $form->thumb->{"optlevel_thumb_".$type}->value);
        module::set_var("image_optimizer", "optlevel_resize_".$type, $form->resize->{"optlevel_resize_".$type}->value);
      }
      module::set_var("image_optimizer", "rotate_jpg", ($form->rotate->rotate_jpg->value == 1));
      foreach (array('thumb', 'resize') as $target) {
        module::set_var("image_optimizer", "convert_".$target."_gif", $form->$target->{"convert_".$target."_gif"}->value);
        module::set_var("image_optimizer", "convert_".$target."_png", $form->$target->{"convert_".$target."_png"}->value);
        module::set_var("image_optimizer", "metastrip_".$target, ($form->$target->{"metastrip_".$target}->value == 1));
        module::set_var("image_optimizer", "progressive_".$target, ($form->$target->{"progressive_".$target}->value == 1));
        // deal with enable changes
        $enable_old = module::get_var("image_optimizer", "enable_".$target);
        $enable_new = ($form->$target->{"enable_".$target}->value == 1);
        if ($enable_new && !$enable_old) {
          image_optimizer::add_image_optimizer_rule($target);
          module::set_var("image_optimizer", "enable_".$target, true);
        } elseif (!$enable_new && $enable_old) {
          image_optimizer::remove_image_optimizer_rule($target);
          module::set_var("image_optimizer", "enable_".$target, false);
        }
        // deal with update mode changes
        $update_mode_old = module::get_var("image_optimizer", "update_mode_".$target);
        $update_mode_new = ($form->$target->{"update_mode_".$target}->value == 1);
        if ($update_mode_new && !$update_mode_old) {
          image_optimizer::activate_update_mode($target);
        } elseif (!$update_mode_new && $update_mode_old) {
          image_optimizer::deactivate_update_mode($target);
        }
        // dirty images if needed
        if ($form->$target->{"rebuild_".$target}->value == 1) {
          image_optimizer::dirty($target);
        }
      }
      // all done; redirect with message
      message::success(t("Image optimizer settings updated successfully"));
      url::redirect("admin/image_optimizer");
    }
    // not valid - print screen from existing form
    $this->_print_screen($form);
  }
  
  private function _print_screen($form) {
    // this part is a bit of a hack, but Forge doesn't seem to allow set_attr() for groups.
    $form = $form->render();
    $form = preg_replace("/<fieldset>/","<fieldset class=\"g-image-optimizer-admin-form-top\">",$form,2);
    $form = preg_replace("/<fieldset>/","<fieldset class=\"g-image-optimizer-admin-form-left\">",$form,1);
    $form = preg_replace("/<fieldset>/","<fieldset class=\"g-image-optimizer-admin-form-right\">",$form,1);
    // make and print view
    $view = new Admin_View("admin.html");
    $view->page_title = t("Image optimizer settings");
    $view->content = new View("admin_image_optimizer.html");
    $view->content->form = $form;
    // get module parameters
    foreach (array('jpg', 'png', 'gif') as $type) {
      $view->content->{"installed_path_".$type} = image_optimizer::tool_installed_path($type);
      $view->content->{"version_".$type} = image_optimizer::tool_version($type);
    }
    print $view;
  }
  
  private function _get_admin_form() {
    $form = new Forge("admin/image_optimizer/save", "", "post", array("id" => "g-image-optimizer-admin-form"));
    
    $group_paths = $form->group("paths")->label(t("Toolkit paths"))->set_attr("id","g-image-optimizer-admin-form-paths");
    foreach (array('jpg', 'png', 'gif') as $type) {
      $path = strval(module::get_var("image_optimizer", "path_".$type, null));
      $group_paths->input("path_".$type)
        ->label(t("Path for")." ".image_optimizer::tool_name($type)." (".t("no symlinks, default")." ".MODPATH."image_optimizer/lib/".image_optimizer::tool_name($type).")")
        ->value($path);
    }
    
    $group_rotate = $form->group("rotate")->label(t("Full-size image rotation"))->set_attr("id","g-image-optimizer-admin-form-rotate");
    $group_rotate->checkbox("rotate_jpg")
      ->label(t("Override default toolkit and use")." ".image_optimizer::tool_name('jpg')." ".t("for rotation"))
      ->checked(module::get_var("image_optimizer", "rotate_jpg", null));
    
    foreach (array('thumb', 'resize') as $target) {
      ${'group_'.$target} = $form->group($target)->label(ucfirst($target)." ".t("images optimization"))->set_attr("id","g-image-optimizer-admin-form-".$target);
      ${'group_'.$target}->checkbox("enable_".$target)
        ->label(t("Enable optimization"))
        ->checked(module::get_var("image_optimizer", "enable_".$target, null));
      ${'group_'.$target}->checkbox("update_mode_".$target)
        ->label(t("Enable update mode - deactivates all other graphics rules to allow fast optimization on existing images; MUST deactivate this after initial rebuild!"))
        ->checked(module::get_var("image_optimizer", "update_mode_".$target, null));
      ${'group_'.$target}->checkbox("rebuild_".$target)
        ->label(t("Mark all existing images for rebuild - afterward, go to Maintenace | Rebuild Images"))
        ->checked(false); // always set as false
      ${'group_'.$target}->dropdown("convert_".$target."_png")
        ->label(t("PNG conversion"))
        ->options(array(0=>t("none"),
                    "jpg"=>("JPG ".t("(not lossless)"))))
        ->selected(module::get_var("image_optimizer", "convert_".$target."_png", null));
      ${'group_'.$target}->dropdown("convert_".$target."_gif")
        ->label(t("GIF conversion"))
        ->options(array(0=>t("none"),
                    "jpg"=>("JPG ".t("(not lossless)")),
                    "png"=>("PNG ".t("(lossless)"))))
        ->selected(module::get_var("image_optimizer", "convert_".$target."_gif", null));
      ${'group_'.$target}->dropdown("optlevel_".$target."_jpg")
        ->label(t("JPG compression optimization (default: enabled)"))
        ->options(array(0=>t("disabled"),
                        1=>t("enabled")))
        ->selected(module::get_var("image_optimizer", "optlevel_".$target."_jpg", null));
      ${'group_'.$target}->dropdown("optlevel_".$target."_png")
        ->label(t("PNG compression optimization (default: level 2)"))
        ->options(array(0=>t("disabled"),
                        1=>t("level 1: 1 trial"),
                        2=>t("level 2: 8 trials"),
                        3=>t("level 3: 16 trials"),
                        4=>t("level 4: 24 trials"),
                        5=>t("level 5: 48 trials"),
                        6=>t("level 6: 120 trials"),
                        7=>t("level 7: 240 trials")))
        ->selected(module::get_var("image_optimizer", "optlevel_".$target."_png", null));
      ${'group_'.$target}->dropdown("optlevel_".$target."_gif")
        ->label(t("GIF compression optimization (default: enabled)"))
        ->options(array(0=>t("disabled"),
                        1=>t("enabled")))
        ->selected(module::get_var("image_optimizer", "optlevel_".$target."_gif", null));
      ${'group_'.$target}->checkbox("metastrip_".$target)
        ->label(t("Remove all meta data"))
        ->checked(module::get_var("image_optimizer", "metastrip_".$target, null));
      ${'group_'.$target}->checkbox("progressive_".$target)
        ->label(t("Make images progressive/interlaced"))
        ->checked(module::get_var("image_optimizer", "progressive_".$target, null));
    }

    $form->submit("")->value(t("Save"));
    return $form;
  }
}