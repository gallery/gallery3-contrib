<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class Admin_Photoannotation_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var(
        "photoannotation", "noborder", $form->hoverphoto->noborder->value, true);
      module::set_var(
        "photoannotation", "bordercolor", $form->hoverphoto->bordercolor->value);
      module::set_var(
        "photoannotation", "noclickablehover", $form->hoverclickable->noclickablehover->value, true);
      module::set_var(
        "photoannotation", "clickablehovercolor", $form->hoverclickable->clickablehovercolor->value);
      module::set_var(
        "photoannotation", "nohover", $form->hovernoclickable->nohover->value, true);
      module::set_var(
        "photoannotation", "hovercolor", $form->hovernoclickable->hovercolor->value);
      module::set_var(
        "photoannotation", "showusers", $form->legendsettings->showusers->value, true);
      module::set_var(
        "photoannotation", "showfaces", $form->legendsettings->showfaces->value, true);
      module::set_var(
        "photoannotation", "shownotes", $form->legendsettings->shownotes->value, true);
      module::set_var(
        "photoannotation", "fullname", $form->legendsettings->fullname->value, true);
      message::success(t("Your settings have been saved."));
      url::redirect("admin/photoannotation");
    }
    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_photoannotation.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_form() {
    $form = new Forge("admin/photoannotation/handler", "", "post", array("id" => "g-admin-form"));
    $group = $form->group("hoverphoto")->label(t("Hovering over the photo"));
    $group->checkbox("noborder")->label(t("Don't show borders."))
      ->checked(module::get_var("photoannotation", "noborder", false));	
    $group->input("bordercolor")->label(t('Border color'))
      ->value(module::get_var("photoannotation", "bordercolor", "000000"))
      ->rules("valid_alpha_numeric|length[6]");
    $group = $form->group("hoverclickable")->label(t("Hovering over a clickable annotation"));
    $group->checkbox("noclickablehover")->label(t("Don't show borders."))
      ->checked(module::get_var("photoannotation", "noclickablehover", false));	
    $group->input("clickablehovercolor")->label(t('Border color'))
      ->value(module::get_var("photoannotation", "clickablehovercolor", "00AD00"))
      ->rules("valid_alpha_numeric|length[6]");
    $group = $form->group("hovernoclickable")->label(t("Hovering over a non-clickable annotation"));
    $group->checkbox("nohover")->label(t("Don't show borders."))
      ->checked(module::get_var("photoannotation", "nohover", false));	
    $group->input("hovercolor")->label(t('Border color'))
      ->value(module::get_var("photoannotation", "hovercolor", "990000"))
      ->rules("valid_alpha_numeric|length[6]");
    $group = $form->group("legendsettings")->label(t("Legend settings"));
    $group->checkbox("showusers")->label(t("Show face annotation below photo."))
      ->checked(module::get_var("photoannotation", "showusers", false));	
    $group->checkbox("showfaces")->label(t("Show face annotation below photo."))
      ->checked(module::get_var("photoannotation", "showfaces", false));	
    $group->checkbox("shownotes")->label(t("Show note annotations below photo."))
      ->checked(module::get_var("photoannotation", "shownotes", false));	
    $group->checkbox("fullname")->label(t("Show full name of a user instead of the username on annotations (username will be dispayed for users without a full name)."))
      ->checked(module::get_var("photoannotation", "fullname", false));	
    $form->submit("submit")->value(t("Save"));
    return $form;
  }
}
