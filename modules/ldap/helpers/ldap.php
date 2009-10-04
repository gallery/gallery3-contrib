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
class ldap_Core {
  private static $connection;

  static function connection() {
    if (!isset(self::$connection)) {
      self::$connection = ldap_connect(Kohana::config("ldap.url"));
      ldap_bind(self::$connection);
    }
    return self::$connection;
  }

  static function lookup_group_by_name($name) {

    $result = ldap_search(ldap::connection(),
                          Kohana::config("ldap.group_domain"),
                          "cn=$name");
    $entry_id = ldap_first_entry(ldap::connection(), $result);
    if ($entry_id) {
      $cn_entry = ldap_get_values(ldap::connection(), $entry_id, "cn");
      $gid_number_entry = ldap_get_values(ldap::connection(), $entry_id, "gidNumber");
      return new Ldap_Group_Model($gid_number_entry[0], $cn_entry[0]);
    }
    return null;
  }

  static function lookup_group($id) {
    $result = ldap_search(ldap::connection(),
                          Kohana::config("ldap.group_domain"),
                          "gidNumber=$id");
    $entry_id = ldap_first_entry(ldap::connection(), $result);
    if ($entry_id) {
      $cn_entry = ldap_get_values(ldap::connection(), $entry_id, "cn");
      return new Ldap_Group_Model($id, $cn_entry[0]);
    }
    return null;
  }

  static function lookup_user_by_name($name) {
    $result = ldap_search(ldap::connection(),
                          Kohana::config("ldap.user_domain"),
                          "uid=$name");
    $entries = ldap_get_entries(ldap::connection(), $result);
    if ($entries["count"] > 0) {
      return new Ldap_User_Model($entries[0]);
    }
    return null;
  }

  static function lookup_user($id) {
    $result = ldap_search(ldap::connection(),
                          Kohana::config("ldap.user_domain"),
                          "uidNumber=$id");
    $entries = ldap_get_entries(ldap::connection(), $result);
    if ($entries["count"] > 0) {
      return new Ldap_User_Model($entries[0]);
    }
    return null;
  }

  static function validate_group($input) {
    if (!self::lookup_group_by_name($input->value)) {
      $input->add_error("invalid_group", 1);
    }
  }

  static function groups_for($user) {
    $result = ldap_search(ldap::connection(),
                          Kohana::config("ldap.group_domain"),
                          "(memberUid=$user->name)");

    $associated_groups = Kohana::config("ldap.groups");
    $groups = array();
    for ($entry_id = ldap_first_entry(ldap::connection(), $result);
         $entry_id != false;
         $entry_id = ldap_next_entry(ldap::connection(), $entry_id)) {
      $group_id = ldap_get_values(ldap::connection(), $entry_id, "gidNumber");
      $group_name = ldap_get_values(ldap::connection(), $entry_id, "cn");
      if (in_array($group_name[0], $associated_groups)) {
        $groups[] = new Ldap_Group_Model($group_id[0], $group_name[0]);
      }
    }
    return $groups;
  }

  static function guest() {
    return new Ldap_Guest_Model();
  }

  public function everybody_group() {
    return ldap::lookup_group_by_name(Kohana::config("ldap.everybody_group"));
  }

  public function registered_users_group() {
    return ldap::lookup_group_by_name(Kohana::config("ldap.registered_users_group"));
  }
}
