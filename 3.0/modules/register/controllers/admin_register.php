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
class Admin_register_Controller extends Admin_Controller {
  public function index() {
    $count = ORM::factory("pending_user")
      ->where("state", "!=", 2)
      ->count_all();
    if ($count == 0) {
      site_status::clear("pending_user_registrations");
    }
    list ($form, $errors) = $this->_get_form();
    print $this->_get_admin_view($form, $errors);
  }

  public function update() {
    access::verify_csrf();

    $post = new Validation($_POST);
    $post->add_rules("policy", "required");
    $post->add_rules("group", array($this, "passthru"));
    $post->add_rules("email_verification", array($this, "passthru"));
    // added Shad Laws, v2
    $post->add_rules("admin_notify", array($this, "passthru"));
    $post->add_rules("subject_prefix", array($this, "passthru"));
    $group_list = array();
    if ($post->validate()) {
      module::set_var("registration", "policy", $post->policy);
      module::set_var("registration", "default_group", $post->group);
      module::set_var("registration", "email_verification", !empty($post->email_verification));
      // added Shad Laws, v2
      module::set_var("registration", "admin_notify", !empty($post->admin_notify));
      module::set_var("registration", "subject_prefix", $post->subject_prefix);

      message::success(t("Registration defaults have been updated."));

      url::redirect("admin/register");
    } else {
      list ($form, $errors) = $this->_get_form();
      $form = array_merge($form, $post->as_array());
      $errors = array_merge($errors, $post->errors());
      print $this->_get_admin_view($form, $errors);
    }
  }

  // We need this validation callback in order to have the optional fields copied to
  // validation array.
  public function passthru($field) {
    return true;
  }

  public function activate() {
    access::verify_csrf();

    $post = new Validation($_POST);
    $post->add_rules("activate_users", "required");
    $post->add_rules("activate", "alpha_numeric");
    if ($post->validate()) {
      $names = array();
      if (!empty($post->activate)) {
        foreach ($post->activate as $id) {
          $user = register::create_new_user($id);
          $names[] = $user->name;
        }

        message::success(t("Activated %users.", array("users" => implode(", ", $names))));
      }

      $count = ORM::factory("pending_user")
        ->where("state", "!=", 2)
        ->count_all();

      if ($count == 0) {
        site_status::clear("pending_user_registrations");
      }
      url::redirect("admin/register");
    }

    list ($form, $errors) = $this->_get_form();
    $form = array_merge($form, $post->as_array());
    $errors = array_merge($errors, $post->errors());
    print $this->_get_admin_view($form, $errors);
  }

  private function _get_admin_view($form, $errors) {
    $v = new Admin_View("admin.html");
    $v->page_title = t("User registration");
    $v->content = new View("admin_register.html");
    $v->content->action = "admin/register/update";
    $v->content->policy_list =
      array("admin_only" => t("Only site administrators can create new user accounts."),
            "visitor" =>
               t("Visitors can create accounts and no administrator approval is required."),
            "admin_approval" =>
               t("Visitors can create accounts but administrator approval is required."));
    $admin = identity::admin_user();
    $v->content->disable_email =
      empty($admin->email) || $form["policy"] == "admin_only" ? "disabled" : "";
    if (empty($admin->email)) {
      module::set_var("registration", "email_verification", false);
    }
    // below lines added Shad Laws, v2
    $v->content->disable_admin_notify =
      empty($admin->email) || $form["policy"] !== "admin_approval" ? "disabled" : "";
    if (empty($admin->email)) {
      module::set_var("registration", "admin_notify", false);
    }

    $v->content->group_list = array();
    foreach (identity::groups() as $group) {
      if ($group->id != identity::everybody()->id &&
          $group->id != identity::registered_users()->id) {
        $v->content->group_list[$group->id] = $group->name;
      }
    }
    $hidden = array("name" => "csrf", "value" => access::csrf_token());
    if (count($v->content->group_list)) {
      $v->content->group_list =
        array("" => t("Choose the default group")) + $v->content->group_list;
    } else {
      $hidden["group"] = "";
    }
    $v->content->hidden = $hidden;
    $v->content->pending = ORM::factory("pending_user")->find_all();
    $v->content->activate = "admin/register/activate";
    $v->content->form = $form;
    $v->content->errors = $errors;
    return $v;
  }

  private function _get_form() {
    $form = array("policy" => module::get_var("registration", "policy"),
                  "group" => module::get_var("registration", "default_group"),
                  "email_verification" => module::get_var("registration", "email_verification"),
                  // added Shad Laws, v2
                  "subject_prefix" => module::get_var("registration", "subject_prefix"),
                  "admin_notify" => module::get_var("registration", "admin_notify"));
    $errors = array_fill_keys(array_keys($form), "");

    return array($form, $errors);
  }
}