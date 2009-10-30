<?php defined("SYSPATH") or die("No direct script access.");/**
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
class register_Controller extends Controller {
  public function index() {
    print $this->_get_form();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    $valid = $form->validate();

    $name = $form->register_user->inputs["name"]->value;
    $user_exists_data = (object)array("name" => $name);
    module::event("check_username_exists", $user_exists_data);
    if ($user_exists_data->exists) {
      $form->register_user->inputs["name"]->add_error("in_use", 1);
      $valid = false;
    }
    if ($valid) {
      $pending_user = register::create_pending_request($form);
      $policy = module::get_var("registration", "policy");
      if ($policy == "visitor" && $pending_user->confirmed) {
        // @todo create and logon
        // set the form to the one similiar to the admin logon
      } else if (empty($pending_user->confirmed) &&
                 ($policy == "admin_approval" || $policy == "visitor")) {
        register::send_confirmation($pending_user);
      } else {
        site_status::warning(
          t("There are pending user registration. <a href=\"%url\">Review now!</a>",
            array("url" => html::mark_clean(url::site("admin/register")))),
          "pending_user_registrations");
        message::success(t("Your registration request is awaiting administrator approval"));
      }

      print json_encode(array("result" => "success"));
    } else {
      print json_encode(
        array("result" => "error",
              "form" => $form->__toString()));
    }
  }

  public function confirm($hash) {
    $pending_user = ORM::factory("pending_user")
      ->where("hash", $hash)
      ->find();
    if ($pending_user->loaded) {
      // @todo add a request date to the pending user table and check that it hasn't expired
      $policy = module::get_var("registration", "policy");
      $pending_user->confirmed = true;
      $pending_user->save();
      if ($policy == "vistor") {
        // @todo create and logon
        // set the form to the one similiar to the admin logon
      } else {
        site_status::warning(
          t("There are pending user registration. <a href=\"%url\">Review now!</a>",
            array("url" => html::mark_clean(url::site("admin/register")))),
          "pending_user_registrations");
        message::success(t("Your registration request is awaiting administrator approval"));
      }
    } else {
      message::error(t("Your registration request is no longer valid, Please re-register."));
    }
    url::redirect(item::root()->abs_url());
  }

  private function _get_form() {
    $minimum_length = module::get_var("user", "mininum_password_length", 5);
    $form = new Forge("register/handler", "", "post", array("id" => "g-register-form"));
    $group = $form->group("register_user")->label(t("register User"));
    $group->input("name")->label(t("Username"))->id("g-username")
      ->rules("required|length[1,32]")
      ->error_messages("in_use", t("There is already a user with that username"));
    $group->input("full_name")->label(t("Full Name"))->id("g-fullname")
      ->rules("length[0, 255]");
    $group->input("email")->label(t("Email"))->id("g-email")
      ->rules("required|valid_email|length[1,255]");
    $group->input("email2")->label(t("Confirm email"))->id("g-email2")
      ->matches($group->email);
    $group->input("url")->label(t("URL"))->id("g-url")
      ->rules("valid_url");

    module::event("recaptcha_add", $group);
    $group->submit("")->value(t("Register"));
    return $form;
  }
}