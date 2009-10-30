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

    // @todo create a user event "user_exists" which checks for name clashes
    $name = $form->register_user->inputs["name"]->value;
    $user_exists_data = (object)array("name" => $name);
    module::event("check_username_exists", $user_exists_data);
    if ($user_exists_data->exists) {
      $form->register_user->inputs["name"]->add_error("in_use", 1);
      $valid = false;
    }
    if ($valid) {
      switch (module::get_var("registration", "policy")) {
      case "immediate":
        message::success(t("Your registration request has been processed"));
        break;
      case "admin":
        $this->_create_pending_request($form);
        message::success(t("Your registration request is awaiting administrator approval"));
        site_status::warning(
          t("There are pending user registration. <a href=\"%url\">Review now!</a>",
            array("url" => html::mark_clean(url::site("admin/register")))),
          "pending_user_registrations");

        break;
      case "email":
        message::success(t("A confirmation email has been sent to the email address you provided."));
        break;
      }

      print json_encode(
        array("result" => "success"));
    } else {
      print json_encode(
        array("result" => "error",
              "form" => $form->__toString()));
    }
  }

  private function _create_pending_request($form) {
    $user = ORM::factory("pending_user");
    $user->name = $form->register_user->inputs["name"]->value;
    $user->full_name = $form->register_user->inputs["full_name"]->value;
    // @todo call identity to hash the password
    $user->password = $form->register_user->inputs["password"]->value;
    $user->email = $form->register_user->inputs["email"]->value;
    $user->url = $form->register_user->inputs["url"]->value;
    $user->hash = md5(rand());
    $user->save();
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
    $group->password("password")->label(t("Password"))->id("g-password")
      ->rules("required|length[{$minimum_length}, 40]");
    $group->password("password2")->label(t("Confirm Password"))->id("g-password2")
      ->matches($group->password);
    $group->input("email")->label(t("Email"))->id("g-email")
      ->rules("valid_email|length[1,255]");
    $group->input("email2")->label(t("Confirm email"))->id("g-email2")
      ->matches($group->email);
    $group->input("url")->label(t("URL"))->id("g-url")
      ->rules("valid_url");

    module::event("recaptcha_add", $group);
    $group->submit("")->value(t("Register"));
    return $form;
  }
}