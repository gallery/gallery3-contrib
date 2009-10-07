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

class UserGroupStorage_Ldap_Driver extends UserGroupStorage_Driver {
  public function group_ids() {
    $session = Session::instance();
    if (!($ids = $session->get("group_ids"))) {

      $ids = array();
      foreach (user::active()->groups as $group) {
        $ids[] = $group->id;
      }
      $session->set("group_ids", $ids);
    }
    return $ids;
  }

  public function active_user() {
    $session = Session::instance();
    $user = $session->get("user", null);
    if (!isset($user)) {
      // Don't do this as a fallback in the Session::get() call because it can trigger unnecessary
      // work.
      $session->set("user", $user = user::guest());
    }
    return $user;
  }

  public function guest_user() {
    return ldap::guest();
  }

  public function set_active_user($user) {
    $session = Session::instance();
    $session->set("user", $user);
    $session->delete("group_ids");
  }

  public function create_user($name, $full_name, $password) {
    throw new Exception("@todo UNSUPPORTED");
  }

  public function is_correct_password($user, $password) {
    try {
      return ldap_bind(ldap::connection(),
                       "uid={$user->name}," . Kohana::config("ldap.user_domain"),
                       $password);
    } catch (Exception $e) {
      // Authentication failure
    }
    return false;
  }

  public function login($user) {
    user::set_active($user);
  }

  public function logout() {
    try {
      Session::instance()->destroy();
    } catch (Exception $e) {
      Kohana::log("error", $e);
    }
  }

  public function lookup_user($id) {
    return ldap::lookup_user($id);
  }

  public function lookup_user_by_name($name) {
    return ldap::lookup_user_by_name($name);
  }

  public function lookup_group($id) {
    return ldap::lookup_group($id);
  }

  public function lookup_group_by_name($name) {
    return ldap::lookup_group_by_name($name);
  }

  public function create_group($name) {
    throw new Exception("@todo UNSUPPORTED");
  }

  public function everybody_group() {
    return ldap::everybody_group();
  }

  public function registered_users_group() {
    return ldap::registered_users_group();
  }
}
