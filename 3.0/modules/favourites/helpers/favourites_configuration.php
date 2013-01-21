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

class favourites_configuration
{
   static function get_configure_form() {
    $form = new Forge("admin/favourites_configure", "", "post", array("id" => "g-configure-form"));

    $group = $form->group("configure")->label(t("Configure Favourites"));
    $group->dropdown("select_allow")
        ->label(t("Please choose what a user can select as a favourite"))
        ->options(Array(1=>t("Items only"), 2=>"albums only", 3=>"Both"));
    $group->input("fromemail")->label(t("From Email address for site emails"))->id("g-from-email-address");
    $group->checkbox("email_admin")->label(t("Email site owner every saved favourites list"))->id("g-email-admin");
    $group->input("email")->label(t("Email address of Site Owner"))->id("g-owner-email-address");
    $group->input("owner")->label(t("Site Owners name"))->id("g-owner-name");
    $group->checkbox("users_only")->label(t("Only Registered users can create favourites"))->id("g-users-only");
    $group->textarea("email_template")->label(t("Email Template"))->id("g-email-template");
    $group->submit("")->value(t("Save"));
    return $form;
  }

  static function populateForm($form){
      $form->configure->email->value(favourites_configuration::getEmailAddress());
      $form->configure->fromemail->value(favourites_configuration::getFromEmailAddress());
      $form->configure->email_admin->checked(favourites_configuration::isEmailAdmin());
      $form->configure->users_only->checked(favourites_configuration::isUsersOnly());
      $form->configure->owner->value(favourites_configuration::getOwner());
      $form->configure->email_template->value(favourites_configuration::getEmailTemplate());
      $form->configure->select_allow->selected(favourites_configuration::getSelectAllow());
  }

  static function extractForm($form){
      $email = $form->configure->email->value;
      $emailfrom = $form->configure->fromemail->value;
      $owner = $form->configure->owner->value;
      $is_email_admin = $form->configure->email_admin->value;
      $is_users_only = $form->configure->users_only->value;
      $email_template = $form->configure->email_template->value;
      $select_from = $form->configure->select_allow->selected;
      favourites_configuration::setEmailAddress($email);
      favourites_configuration::setEmailAdmin($is_email_admin);
      favourites_configuration::setFromEmailAddress($emailfrom);
      favourites_configuration::setOwner($owner);
      favourites_configuration::setUsersOnly($is_users_only);
      favourites_configuration::setEmailTemplate($email_template);
      favourites_configuration::setSelectAllow($select_from);
  }

  static function replaceStrings($string, $key_values) {
    // Replace x_y before replacing x.
    krsort($key_values, SORT_STRING);

    $keys = array();
    $values = array();
    foreach ($key_values as $key => $value) {
      $keys[] = "%$key";
      $values[] = $value;
    }
    return str_replace($keys, $values, $string);
  }

  static function getEmailAddress(){
    return module::get_var("favourites","email");
  }

  static function setEmailAddress($email){
    module::set_var("favourites","email",$email);
  }

  static function getOwner(){
    return module::get_var("favourites","owner");
  }

  static function setOwner($owner){
    module::set_var("favourites","owner",$owner);
  }

  static function getFromEmailAddress(){
    return module::get_var("favourites","from_email");
  }

  static function setFromEmailAddress($fromemail){
    module::set_var("favourites","from_email",$fromemail);
  }

  static function isEmailAdmin(){
    return module::get_var("favourites","email_admin");
  }

  static function setEmailAdmin($email_admin){
    module::set_var("favourites","email_admin",$email_admin);
  }

  static function isUsersOnly(){
    return module::get_var("favourites","users_only");
  }

  static function setUsersOnly($users_only){
    module::set_var("favourites","users_only",$users_only);
  }

  static function getSelectAllow(){
    return module::get_var("favourites","select_from",1);
  }

  static function setSelectAllow($select_from){
    module::set_var("favourites","select_from",$select_from);
  }

  static function getEmailTemplate(){
    return module::get_var("favourites","email_template");
  }

  static function setEmailTemplate($email_template){
    module::set_var("favourites","email_template",$email_template);
  }

  static function canSelectAlbums(){
    return self::getSelectAllow()!=1;
  }

  static function canSelectItems(){
    return self::getSelectAllow()!=2;
  }

}