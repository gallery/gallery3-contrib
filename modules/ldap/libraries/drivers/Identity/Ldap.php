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
class Identity_Ldap_Driver implements Identity_Driver {
  private static $_params;
  private static $_connection;
  private static $_guest_user;

  /**
   * Initializes the LDAP Driver
   *
   * @return  void
   */
  public function __construct($params) {
    self::$_params = $params;
    self::$_connection = ldap_connect($this->_params["url"]);
    ldap_bind(self::$_connection);
  }

  /**
   * @see Identity_Driver::guest.
   */
  public function guest() {
    if (empty(self::$_guest_user)) {
      self::$_guest_user = new Ldap_User();
      self::$_guest_user->id = 0;
      self::$_guest_user->name = "Guest";
      self::$_guest_user->guest = true;
      self::$_guest_user->admin = false;
      self::$_guest_user->locale = null;
      self::$_guest_user->groups = array($this->everybody());
    }
    return self::$_guest_user;
  }

  /**
   * @see Identity_Driver::create_user.
   */
  public function create_user($name, $full_name, $password) {
    throw new Exception("@todo INVALID OPERATION");
  }

  /**
   * @see Identity_Driver::is_correct_password.
   */
  public function is_correct_password($user, $password) {
    $ureturn=ldap_search(self::$_connection, $base_dn, "(uid=$uname)", array('dn'));

    $uent=ldap_first_entry(self::$_connection, $ureturn);
    if (!$uent) return ERROR_CODE;

    $bn=ldap_get_dn(self::$_connection, $uent);

    //This line should use $pass rather than $password
    $lbind=ldap_bind(self::$_connection, $bn, $password);

    return ($lbind) ? true : false;
  }

  /**
   * @see Identity_Driver::lookup_user.
   */
  public function lookup_user($id) {
    $result = ldap_search(self::$_connection, self::$_params["user_domain"], "uidNumber=$id");
    $entries = ldap_get_entries(self::$_connection, $result);
    if ($entries["count"] > 0) {
      $cn_entry = ldap_get_values(self::$_connection, $entry_id, "cn");
      return new Ldap_User($entries[0]);
    }
    return null;
  }

  /**
   * @see Identity_Driver::lookup_user_by_name.
   */
  public function lookup_user_by_name($name) {
    $result = ldap_search(self::$_connection, self::$_params["user_domain"], "uid=$name");
    $entries = ldap_get_entries(self::$_connection, $result);
    if ($entries["count"] > 0) {
      $cn_entry = ldap_get_values(self::$_connection, $entry_id, "cn");
      return new Ldap_User($entries[0]);
    }
    return null;
  }

  /**
   * @see Identity_Driver::create_group.
   */
  public function create_group($name) {
    throw new Exception("@todo INVALID OPERATION");
  }

  /**
   * @see Identity_Driver::everybody.
   */
  public function everybody() {
    return ldap::lookup_group_by_name(self::$_params["everybody_group"]);
  }

  /**
   * @see Identity_Driver::registered_users.
   */
  public function registered_users() {
    return ldap::lookup_group_by_name(self::$_params["registered_users_group"]);
  }

  /**
   * @see Identity_Driver::lookup_group_by_name.
   */
  static function lookup_group_by_name($name) {
    $result = ldap_search(self::$_connection, self::$_params["group_domain"], "cn=$name");
    $entry_id = ldap_first_entry(, $result);
    if ($entry_id) {
      $cn_entry = ldap_get_values(self::$_connection, $entry_id, "cn");
      $gid_number_entry = ldap_get_values(self::$_connection, $entry_id, "gidNumber");
      return new Ldap_Group_Model($gid_number_entry[0], $cn_entry[0]);
    }
    return null;
  }

  /**
   * @see Identity_Driver::get_user_list.
   */
  public function get_user_list($ids) {
    throw new Exception("@todo NOT IMPLEMENTED");
  }

  static function groups_for($user) {
    $result = ldap_search(self::$_connection, self::$_params["group_domain"],
                          "(memberUid=$user->name)");

    $associated_groups = Kohana::config("ldap.groups");
    $groups = array();
    for ($entry_id = ldap_first_entry(self::$_connection, $result);
         $entry_id != false;
         $entry_id = ldap_next_entry(self::$_connection, $entry_id)) {
      $group_id = ldap_get_values(self::$_connection, $entry_id, "gidNumber");
      $group_name = ldap_get_values(self::$_connection, $entry_id, "cn");
      if (in_array($group_name[0], $associated_groups)) {
        $groups[] = new Ldap_Group($group_id[0], $group_name[0]);
      }
    }
    return $groups;
  }
} // End Identity Gallery Driver

class Ldap_User implements User_Definition {
  private $ldap_entry;

  public function __construct($ldap_entry=null) {
    $this->ldap_entry = $ldap_entry;
  }

  public function display_name() {
    return $this->ldap_entry["displayname"][0];
  }

  public function __get($key) {
    switch($key) {
      case "name":
        return $this->ldap_entry["uid"][0];

      case "guest":
        return false;

      case "id":
        return $this->ldap_entry["uidnumber"][0];

      case "groups":
        return Identity_Ldap::Driver::groups_for($this);

      case "locale":  // @todo
        return null;

      case "admin":
        return in_array($this->ldap_entry["uid"][0], Kohana::config("ldap.admins"));

      default:
        throw new Exception("@todo UNKNOWN_KEY ($key)");
    }
  }
  }

class Ldap_Group implements Group_Definition {
  public $id;
  public $name;

  public function __construct($id, $name) {
    $this->id = $id;
    $this->name = $name;
    $this->special = false;
  }
}
