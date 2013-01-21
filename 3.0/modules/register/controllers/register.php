<?php defined("SYSPATH") or die("No direct script access.");/**
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
class register_Controller extends Controller {
  const ALLOW_PRIVATE_GALLERY = true;

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
          $user = register::create_new_user($pending_user->id);
          Session::instance()->set("registration_first_usage");
          auth::login($user);
          Session::instance()->set("registration_first_usage", true);
          $pending_user->delete();
        } else {
          $user = register::create_new_user($pending_user->id, true);
          message::success(t("A confirmation email has been sent to your email address."));
        }
      } else if ($pending_user->state == 1) {
        site_status::warning(
          t("There are pending user registration. <a href=\"%url\">Review now!</a>",
            // modified by Shad Laws, v2
            // array("url" => html::mark_clean(url::site("admin/register")))),
            array("url" => html::mark_clean(url::site("admin/register")), "locale" => module::get_var("gallery", "default_locale"))),
          "pending_user_registrations");
        message::success(t("Your registration request is awaiting administrator approval"));
        // added by Shad Laws, v2
        if (module::get_var("registration", "admin_notify") == 1) {
          register::send_admin_notify($pending_user);
        }
      } else {
        register::send_confirmation($pending_user);
        message::success(t("A confirmation email has been sent to your email address."));
      }

      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
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
            // modified by Shad Laws, v2
            // array("url" => html::mark_clean(url::site("admin/register")))),
            array("url" => html::mark_clean(url::site("admin/register")), "locale" => module::get_var("gallery", "default_locale"))),
          "pending_user_registrations");
        message::success(t("Your registration request is awaiting administrator approval"));
        // added by Shad Laws, v2
        if (module::get_var("registration", "admin_notify") == 1) {
          register::send_admin_notify($pending_user);
        }
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

  public function change_password($id, $password) {
    $user = user::lookup($id);
    print $this->_get_change_password_form($user, $password);
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
    // modified by Shad Laws, v2
    // $group->input("url")->label(t("URL"))->id("g-url")
    $group->input("url")->label(t("URL")." (".t("optional").")")->id("g-url")
      ->rules("valid_url");

    module::event("register_add_form", $form);
    module::event("captcha_protect_form", $form);
    $group->submit("")->value(t("Register"));
    return $form;
  }

  /**
   * Get the password change form.  This code is copied from controllers/users.php.  The
   * difference is that as this is the first time logging on, the user might not have
   * expected that they were going to have to enter the password displayed on the welcome
   * page, and didn't make note of it.  If we were using the standard change password dialog, the
   * user would be screwed as there is no way to go back and get it.  So with this dialog,
   * we will provide the old password as a hidden field.
   */
  private function _get_change_password_form($user, $password) {
    $form = new Forge(
      "users/change_password/$user->id", "", "post", array("id" => "g-change-password-user-form"));
    $group = $form->group("change_password")->label(t("Change your password"));
    $group->hidden("old_password")->value($password);
    $group->password("password")->label(t("New password"))->id("g-password")
      ->error_messages("min_length", t("Your new password is too short"));
    $group->script("")
      ->text(
        '$("form").ready(function(){$(\'input[name="password"]\').user_password_strength();});');
    $group->password("password2")->label(t("Confirm new password"))->id("g-password2")
      ->matches($group->password)
      ->error_messages("matches", t("The passwords you entered do not match"));

    module::event("user_change_password_form", $user, $form);
    $group->submit("")->value(t("Save"));
    return $form;
  }
}