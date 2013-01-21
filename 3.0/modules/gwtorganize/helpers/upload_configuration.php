<?php
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
class upload_configuration_Core {

  static function get_configure_form() {
    $form = new Forge("admin/upload_configure", "", "post", array("id" => "gConfigureForm"));
    $group = $form->group("configure")->label(t("Configure Upload Options"));
    $group->checkbox("resize")->label(t("Resize before upload"))->id("gResize");
    $group->input("max_width")->label(t("Max Width"))->id("gMaxWidth");
    $group->input("max_height")->label(t("Max Height"))->id("gMax Height");
    $group->submit("")->value(t("Save"));
    return $form;
  }

  static function populateForm($form){
      $form->configure->resize->checked(upload_configuration::isResize());
      $form->configure->max_width->value(upload_configuration::getMaxWidth());
      $form->configure->max_height->value(upload_configuration::getMaxHeight());
  }

  static function extractForm($form){
      $resize = $form->configure->resize->value;
      $max_width = $form->configure->max_width->value;
      $max_height= $form->configure->max_height->value;
      upload_configuration::setResize($resize);
      upload_configuration::setMaxWidth($max_width);
      upload_configuration::setMaxHeight($max_height);
  }

  static function isResize(){
    return module::get_var("gwtorganise","resize");
  }

  static function getMaxWidth(){
    return intval(module::get_var("gwtorganise","max_width"));
  }

  static function getMaxHeight(){
    return intval(module::get_var("gwtorganise","max_height"));
  }

  static function setResize($isResize){
    module::set_var("gwtorganise","resize",$isResize);
  }

  static function setMaxWidth($max_width){
    module::set_var("gwtorganise","max_width",$max_width);
  }

  static function setMaxHeight($max_height){
    module::set_var("gwtorganise","max_height",$max_height);
  }
}