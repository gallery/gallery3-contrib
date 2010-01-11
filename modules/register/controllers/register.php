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
    if (register::check_user_name($name)) {
      $form->register_user->inputs["name"]->add_error("in_use", 1);
      $valid = false;
    }
    if ($valid) {
      $pending_user = register::create_pending_request($form);
      $policy = module::get_var("registration", "policy");
      if ($policy == "visitor") {
        if ($pending_user->state == 1) {
          Session::instance()->set("registration_first_usage");
          $user = register::create_new_user($pending_user->id);
          auth::login($user);
          Session::instance()->set("registration_first_usage", true);
          $pending_user->delete();
        } else {
          register::send_confirmation($pending_user);
          message::success(t("A confirmation email has been sent to your email address."));
        }
      } else if ($pending_user->state == 1) {
        site_status::warning(
          t("There are pending user registration. <a href=\"%url\">Review now!</a>",
            array("url" => html::mark_clean(url::site("admin/register")))),
          "pending_user_registrations");
        message::success(t("Your registration request is awaiting administrator approval"));
      } else {
        register::send_confirmation($pending_user);
        message::success(t("A confirmation email has been sent to your email address."));
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
      ->where("hash", "=", $hash)
      ->where("state", "=", 0)
      ->find();
    if ($pending_user->loaded()) {
      // @todo add a request date to the pending user table and check that it hasn't expired
      $policy = module::get_var("registration", "policy");
      $pending_user->state = 1;
      $pending_user->save();
      if ($policy == "vistor") {
        $user = register::create_new_user($pending_user->id);
        message::success(t("Your registration request has been approved"));
        auth::login($user);
        Session::instance()->set("registration_first_usage", true);
        $pending_user->delete();
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

  public function first($hash) {
    $pending_user = ORM::factory("pending_user")
      ->where("hash", "=", $hash)
      ->where("state", "=", 2)
      ->find();
    if ($pending_user->loaded()) {
      // @todo add a request date to the pending user table and check that it hasn't expired
      $user = identity::lookup_user_by_name($pending_user->name);
      if (!empty($user)) {
        auth::login($user);
        Session::instance()->set("registration_first_usage", true);
        $pending_user->delete();
      }
      url::redirect(item::root()->abs_url());
    } else {
      message::warning(t("Your account is ready to use so please login."));
    }
    url::redirect(item::root()->abs_url());
  }

  public function welcome_message() {
    $user = identity::active_user();
    $password = substr(md5(rand()), 0, 8);
    $user->password = $password;
    $user->save();

    $v = new View("register_welcome_message.html");
    $v->user = $user;
    $v->password = $password;
    print $v;
  }

  private function _get_form() {
    $minimum_length = module::get_var("user", "mininum_password_length", 5);
    $form = new Forge("register/handler", "", "post", array("id" => "g-register-form"));
    $group = $form->group("register_user")->label(t("Register user"));
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

    module::event("register_add_form", $form);
    $group->submit("")->value(t("Register"));
    return $form;
  }
}