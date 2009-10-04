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
class Admin_Ldap_Controller extends Admin_Controller {
  public function index() {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_ldap.html");
    $view->content->config = Kohana::config("ldap");
    print $view;
  }

  public function activate() {
    access::verify_csrf();


    if (module::get_var("gallery", "user_group_storage", "Gallery3") == "Gallery3") {
      // @todo: we should have an API for these
      foreach (ORM::factory("group")->find_all() as $group) {
        $group->delete();
      }
      foreach (ORM::factory("user")->find_all() as $user) {
        $user->delete();
      }
    }

    // Create LDAP groups
    foreach (Kohana::config("ldap.groups") as $group_name) {
      $group = ldap::lookup_group_by_name($group_name);
      module::event("group_created", $group);
    }

    // Fix up permissions.
    $root = item::root();
    $everybody = ldap::everybody_group();
    access::allow($everybody, "view", $root);
    access::allow($everybody, "view_full", $root);
    $registered_users = ldap::registered_users_group();
    access::allow($registered_users, "view", $root);
    access::allow($registered_users, "view_full", $root);

    // Switch authentication
    module::set_var("gallery", "user_group_storage", "Ldap");

    // Logout and go back to the top level
    user::logout();
    url::redirect(item::root()->abs_url());
  }
}