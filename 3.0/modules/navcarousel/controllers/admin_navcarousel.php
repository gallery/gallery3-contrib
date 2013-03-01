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
class Admin_Navcarousel_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      $scrollsize = intval($form->navcarousel->scrollsize->value);
      $showelements = intval($form->navcarousel->showelements->value);
      $carouselwidth = intval($form->navcarousel->carouselwidth->value);
      $thumbsize = intval($form->thumbsettings->thumbsize->value);
      if ($showelements < 1) {
        $showelements = 1;
        message::error(t("You must show at least one item."));
      }
      if ($scrollsize < 1) {
        $scrollsize = 1;
        message::error(t("You must scroll by at least one item."));
      }
      if ($thumbsize > 150 || $thumbsize < 25) {
        $thumbsize = 50;
        message::error(t("The size of the thumbnails must be between 25 and 150 pixel."));
      }
      if ($carouselwidth < ($thumbsize + 75) && $carouselwidth > 0) {
        $carouselwidth = $thumbsize + 75;
        message::error(t("The carousel must be at least %pixel wide.", array("pixel" => $carouselwidth)));
      }
      if ($carouselwidth > 0) {
        if ($carouselwidth < ((($thumbsize + 11) * $showelements) + 64)) {
          $showelements = ($carouselwidth - 64) / ($thumbsize + 11);
          $showelements = intval(floor($showelements));
          message::error(t("With the selected carousel width and thumbnail size you can show a maximum of %itemno items.", array("itemno" => $showelements)));
        }
      } else {
          message::warning(t("The maximum number of displayable items cannot be calculated when the carousel width is set to 0."));
      }
      if ($scrollsize > $showelements) {
        $scrollsize = $showelements;
        message::error(t("The number of items to scroll must not exceed the number of items to show."));
      }
      module::set_var(
        "navcarousel", "scrollsize", $scrollsize);
      module::set_var(
        "navcarousel", "showelements", $showelements);
      module::set_var(
        "navcarousel", "carouselwidth", $carouselwidth);
      module::set_var(
        "navcarousel", "thumbsize", $thumbsize);
      module::set_var(
        "navcarousel", "abovephoto", $form->navcarousel->abovephoto->value, true);
      module::set_var(
        "navcarousel", "noajax", $form->navcarousel->noajax->value, true);
      module::set_var(
        "navcarousel", "showondomready", $form->navcarousel->showondomready->value, true);
      module::set_var(
        "navcarousel", "maintainaspect", $form->thumbsettings->maintainaspect->value, true);
      module::set_var(
        "navcarousel", "nomouseover", $form->thumbsettings->nomouseover->value, true);
      module::set_var(
        "navcarousel", "noresize", $form->thumbsettings->noresize->value, true);
        
      message::success(t("Your settings have been saved."));
      url::redirect("admin/navcarousel");
    }
    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_navcarousel.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_form() {
    $form = new Forge("admin/navcarousel/handler", "", "post", array("id" => "g-admin-form"));
    
    $group = $form->group("navcarousel")->label(t("Navigation carousel settings"));
    $group->input("scrollsize")->label(t('Enter how many items you want to scroll when clicking next or previous'))
      ->value(module::get_var("navcarousel", "scrollsize", "7"))
      ->rules("valid_numeric|length[1,2]");
    $group->input("showelements")->label(t('Enter how many items you want to be visible'))
      ->value(module::get_var("navcarousel", "showelements", "7"))
      ->rules("valid_numeric|length[1,2]");
    $group->input("carouselwidth")->label(t('Carousel width (in pixel). If set to 0 the carousel will use the full available width.'))
      ->value(module::get_var("navcarousel", "carouselwidth", "600"))
      ->rules("valid_numeric|length[1,3]");
    $group->checkbox("abovephoto")->label(t("Show carousel above photo"))
      ->checked(module::get_var("navcarousel", "abovephoto", false));	
    $group->checkbox("noajax")->label(t("Disable dynamic loading of thumbnails (might be slow for big albums)"))
      ->checked(module::get_var("navcarousel", "noajax", false));	
    $group->checkbox("showondomready")->label(t("Show carousel before all items are loaded (faster loading on large albums but might cause too early display on Chrome and Opera)"))
      ->checked(module::get_var("navcarousel", "showondomready", false));	
   
    $group = $form->group("thumbsettings")->label(t("Change how thumnails are displayed"));
    $group->input("thumbsize")->label(t('Thumbnail size (in pixel)'))
      ->value(module::get_var("navcarousel", "thumbsize", "50"))
      ->rules("valid_numeric|length[1,3]");
    $group->checkbox("nomouseover")->label(t("Do not show item title and number on mouse over"))
      ->checked(module::get_var("navcarousel", "nomouseover", false));	
    $group->checkbox("noresize")->label(t("Crop thumbails instead of resizing them."))
      ->onClick("changeaspectstate()")
      ->id("noresize")
      ->checked(module::get_var("navcarousel", "noresize", false));	
    $group->checkbox("maintainaspect")->label(t("Maintain aspect ratio of the items for the thumbnails."))
      ->id("maintainaspect")
      ->checked(module::get_var("navcarousel", "maintainaspect", false));	
   
	$form->submit("submit")->value(t("Save"));
    return $form;
  }
}
