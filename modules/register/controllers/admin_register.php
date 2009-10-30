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
class Admin_register_Controller extends Admin_Controller {
  public function index() {
    list ($form, $errors) = $this->_get_form();
    print $this->_get_admin_view($form, $errors);
  }

  public function update() {
    access::verify_csrf();

    $post = new Validation($_POST);
    $post->add_rules("policy", "required");
    $post->add_rules("group", "required");
    if ($post->validate()) {
      Kohana::log("alert", Kohana::debug($post));
      module::set_var("registration", "policy", $post->policy);
      module::set_var("registration", "default_group", $post->group);
      module::set_var("registration", "email_verification", !empty($post->email_verification));

      message::success(t("Registration defaults have been updated."));

      url::redirect("admin/register");
    } else {
      list ($form, $errors) = $this->_get_form();
      arr::overwrite($form, $post->as_array());
      arr::overwrite($errors, $post->errors());
      print $this->_get_admin_view($form, $errors);
    }
  }

  public function activate() {
    access::verify_csrf();

    $post = new Validation($_POST);
    $post->add_rules("activate_users", "required");
    if ($post->validate()) {
      $names = array();
      foreach ($post->activate as $id) {
        $user = ORM::factory("pending_user", $id);
        Kohana::log("alert", Kohana::debug($user->as_array()));

        $password = md5(rand());
        $new_user = identity::create_user($user->name, $user->full_name, $password);
        $new_user->email = $user->email;
        $new_user->url = $user->url;
        $new_user->admin = false;
        $new_user->guest = false;
        $new_user->save();

        identity::add_user_to_group($new_user, module::get_var("registration", "default_group"));

        register::send_user_created_confirmation($new_user, $password);
        $names[] = $user->name;
        $user->delete();
      }

      message::success(t("Activated %users.", implode(", ", $names)));

      $count = Database::instance()
        ->query("select count(id) as pending_count from {pending_users}")
        ->current()->pending_count;
      if ($count == 0) {
        site_status::clear("pending_user_registrations");
      }
      url::redirect("admin/register");
    }

    list ($form, $errors) = $this->_get_form();
    arr::overwrite($form, $post->as_array());
    arr::overwrite($errors, $post->errors());
    print $this->_get_admin_view($form, $errors);
  }

  private function _get_admin_view($form, $errors) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_register.html");
    $v->content->action = "admin/register/update";
    $v->content->policy_list =
      array("admin_only" => t("Only site administrators can create new user accounts."),
            "vistor" =>
               t("Visitors can create accounts and no administrator approval is required."),
            "admin_approval" =>
               t("Visitors can create accounts but administrator approval is required."));
    $admin = identity::admin_user();
    $v->content->no_admin = empty($admin->email) ? "disabled" : "";
    if (empty($admin->email)) {
      module::set_var("registration", "email_verification", false);
    }

    $v->content->group_list = array();
    foreach (identity::groups() as $group) {
      if ($group->id != identity::everybody()->id &&
          $group->id != identity::registered_users()->id) {
        $v->content->group_list[$group->id] = $group->name;
      }
    }
    if (count($v->content->group_list)) {
      $v->content->group_list =
        array("" => t("Choose the default group")) + $v->content->group_list;
    }
    $v->content->hidden = array("csrf" => access::csrf_token());
    $v->content->pending = ORM::factory("pending_user")->find_all();
    $v->content->activate = "admin/register/activate";
    $v->content->form = $form;
    $v->content->errors = $errors;
    return $v;
  }

  private function _get_form() {
    $form = array("policy" => module::get_var("registration", "policy"),
                  "default_group" => module::get_var("registration", "default_group"),
                  "email_verification" => module::get_var("registration", "email_verification"));
    $errors = array_fill_keys(array_keys($form), "");

    return array($form, $errors);
  }
}