<?php defined("SYSPATH") or die("No direct script access.");
/**
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
define("LDAP_GUEST_ID", 0);
define("LDAP_EVERYBODY_ID", 0);
define("LDAP_REGISTERED_USERS_ID", 99999);

class IdentityProvider_Ldap_Driver implements IdentityProvider_Driver {
  static $_params;
  private static $_connection;
  private static $_guest_user;
  private static $_everybody_group;
  private static $_registered_users_group;

  /**
   * Initializes the LDAP Driver
   *
   * @return  void
   */
  public function __construct($params) {
    self::$_params = $params;
    if (!in_array(self::$_params["everybody_group"], self::$_params["groups"]) || !in_array(self::$_params["registered_users_group"], self::$_params["groups"])) {
      throw new Exception("Module ldap: Values of parameters \"everybody_group\" and \"registered_users_group\" must be listed in parameter \"groups\"!");
    }
    self::$_connection = ldap_connect(self::$_params["url"]);
    ldap_set_option(self::$_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    if (self::$_params["bind_rdn"]) {
      ldap_bind(self::$_connection, self::$_params["bind_rdn"], self::$_params["bind_password"]);
    } else {
      ldap_bind(self::$_connection);
    }
  }

  /**
   * @see IdentityProvider_Driver::guest.
   */
  public function guest() {
    return self::lookup_user_by_name(self::$_params["guest_user"]);
  }

  /**
   * @see IdentityProvider_Driver::admin_user.
   */
  public function admin_user() {
    return self::lookup_user_by_name(self::$_params["admins"][0]);
  }

  /**
   * @see IdentityProvider_Driver::create_user.
   */
  public function create_user($name, $full_name, $password, $email) {
    throw new Exception("@todo INVALID OPERATION");
  }

  /**
   * @see IdentityProvider_Driver::is_correct_password.
   */
  public function is_correct_password($user, $password) {
    $connection = ldap_connect(self::$_params["url"]);
    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    $lbind = @ldap_bind($connection, $user->dn, $password);
    ldap_unbind($connection);

    return ($lbind) ? true : false;
  }

  /**
   * @see IdentityProvider_Driver::lookup_user.
   */
  public function lookup_user($id) {
    if ($id == LDAP_GUEST_ID) {
      return self::lookup_user_by_name(self::$_params["guest_user"]);
    }
    $result = ldap_search(self::$_connection, self::$_params["user_domain"], "uidNumber=$id");
    $entries = ldap_get_entries(self::$_connection, $result);
    if ($entries["count"] > 0) {
      return new Ldap_User($entries[0]);
    }
    return null;
  }

  /**
   * @see IdentityProvider_Driver::lookup_user_by_name.
   *
   * Special processing: if the supplied name is admin then look up the first user
   * specified by the "admins" driver params
   */
  public function lookup_user_by_name($name) {
    $result = ldap_search(self::$_connection, self::$_params["user_domain"], "uid=$name");
    $entries = ldap_get_entries(self::$_connection, $result);
    if ($entries["count"] > 0) {
      return new Ldap_User($entries[0]);
    }
    if ($name == self::$_params["guest_user"]) {
      if (empty(self::$_guest_user)) {
        self::$_guest_user = new Ldap_User();
        self::$_guest_user->id = LDAP_GUEST_ID;
        self::$_guest_user->name = "$name";
        self::$_guest_user->full_name = "$name";
        self::$_guest_user->guest = true;
        self::$_guest_user->admin = false;
        self::$_guest_user->locale = null;
        self::$_guest_user->email = null;
      }
      return self::$_guest_user;
    }
    return null;
  }

  /**
   * @see IdentityProvider_Driver::create_group.
   */
  public function create_group($name) {
    throw new Exception("@todo INVALID OPERATION");
  }

  /**
   * @see IdentityProvider_Driver::everybody.
   */
  public function everybody() {
    return $this->lookup_group_by_name(self::$_params["everybody_group"]);
  }

  /**
   * @see IdentityProvider_Driver::registered_users.
   */
  public function registered_users() {
    return $this->lookup_group_by_name(self::$_params["registered_users_group"]);
  }

  /**
   * @see IdentityProvider_Driver::lookup_group.
   */
  public function lookup_group($id) {
    if ($id = LDAP_EVERYBODY_GROUP_ID) {
      return self::lookup_group_by_name(self::$_params["everybody_group"]);
    } else if ($id = LDAP_REGISTERED_USERS_ID) {
      return self::lookup_group_by_name(self::$_params["registered_users_group"]);
    }
    $result = @ldap_search(self::$_connection, self::$_params["group_domain"], "gidNumber=$id");
    $entry_id = ldap_first_entry(self::$_connection, $result);

    if ($entry_id !== false) {
      $cn_entry = ldap_get_values(self::$_connection, $entry_id, "cn");
      $gid_number_entry = ldap_get_values(self::$_connection, $entry_id, "gidNumber");
      if (in_array($cn_entry, self::$_params["groups"])) {
        return new Ldap_Group($gid_number_entry[0], $cn_entry[0]);
      }
    }
    return null;
  }

  /**
   * Look up the group by name.
   * @param string     $name the name of the group to locate
   * @return Group_Definition
   */
  public function lookup_group_by_name($name) {
    $result = @ldap_search(self::$_connection, self::$_params["group_domain"], "cn=$name");
    $entry_id = ldap_first_entry(self::$_connection, $result);

    if ($entry_id !== false) {
      $cn_entry = ldap_get_values(self::$_connection, $entry_id, "cn");
      $gid_number_entry = ldap_get_values(self::$_connection, $entry_id, "gidNumber");
      if (in_array($cn_entry, self::$_params["groups"])) {
        return new Ldap_Group($gid_number_entry[0], $cn_entry[0]);
      }
    }
    if ($name == self::$_params["everybody_group"]) {
      if (!self::$_everybody_group) {
        self::$_everybody_group = new Ldap_Group(LDAP_EVERYBODY_ID, $name);
      }
      return self::$_everybody_group;
    }
    if ($name == self::$_params["registered_users_group"]) {
      if (!self::$_registered_users_group) {
        self::$_registered_users_group = new Ldap_Group(LDAP_REGISTERED_USERS_ID, $name);
      }
      return self::$_registered_users_group;
    }
    return null;
  }

  /**
   * @see IdentityProvider_Driver::get_user_list.
   */
  public function get_user_list($ids) {
    $users = array();
    foreach ($ids as $id) {
      if ($user = $this->lookup_user($id)) {
        $users[] = $user;
      }
    }
    return $users;
  }

  /**
   * @see IdentityProvider_Driver::groups.
   */
  public function groups() {
    $groups = array();
    foreach (self::$_params["groups"] as $group_name) {
      if ($group = $this->lookup_group_by_name($group_name)) {
        $groups[] = $group;
      }
    }
    return $groups;
  }

  static function groups_for($user) {
    $result = ldap_search(self::$_connection, self::$_params["group_domain"],
                          "(memberUid=$user->name)");

    $associated_groups = self::$_params["groups"];
    $groups = array();
    $in_everybody_group = false;
    $in_registered_users_group = false;
    for ($entry_id = ldap_first_entry(self::$_connection, $result);
         $entry_id != false;
         $entry_id = ldap_next_entry(self::$_connection, $entry_id)) {
      $group_id = ldap_get_values(self::$_connection, $entry_id, "gidNumber");
      $group_name = ldap_get_values(self::$_connection, $entry_id, "cn");
      if (in_array($group_name[0], $associated_groups)) {
        $groups[] = new Ldap_Group($group_id[0], $group_name[0]);
      }
      if ($group_name[0] == self::$_params["everybody_group"]) {
        $in_everybody_group = true;
      }
      if ($group_name[0] == self::$_params["registered_users_group"]) {
        $in_registered_users_group = true;
      }
    }
    if (!$in_everybody_group) {
      $groups[] = self::lookup_group_by_name(self::$_params["everybody_group"]);
    }
    if (!$user->guest && !$in_registered_users_group) {
      $groups[] = self::lookup_group_by_name(self::$_params["registered_users_group"]);
    }
    return $groups;
  }

  /**
   * @see IdentityProvider_Driver::add_user_to_group.
   */
  public function add_user_to_group($user, $group) {
    throw new Exception("@todo INVALID OPERATION");
  }

  /**
   * @see IdentityProvider_Driver::remove_user_to_group.
   */
  public function remove_user_from_group($user, $group) {
    throw new Exception("@todo INVALID OPERATION");
  }
} // End Identity Gallery Driver

class Ldap_User implements User_Definition {
  private $ldap_entry;

  public function __construct($ldap_entry=null) {
    $this->ldap_entry = $ldap_entry;
  }

  public function display_name() {
    if (!empty($this->ldap_entry["displayname"][0])) {
      return $this->ldap_entry["displayname"][0];
    }
    return $this->full_name;
  }

  public function __get($key) {
    switch($key) {
    case "name":
      return $this->ldap_entry["uid"][0];

    case "guest":
      return false;

    case "id":
      return $this->ldap_entry["uidnumber"][0];

    case "locale":  // @todo
      return null;

    case "admin":
      return in_array($this->ldap_entry["uid"][0],
                      IdentityProvider_Ldap_Driver::$_params["admins"]);

    case "email":
      if (isset($this->ldap_entry["mail"])) {
        return $this->ldap_entry["mail"][0];
      } else if ($this->admin) {
        IdentityProvider_Ldap_Driver::$_params["admin_mail"];
      } else {
        return null;
      }

    case "full_name":
      if (isset($this->ldap_entry["cn"])) {
        return $this->ldap_entry["cn"][0];
      } else {
        return $this->name;
      }

    case "dn":
      return $this->ldap_entry["dn"];

    case "url":  // @todo
      return null;

    default:
      throw new Exception("@todo UNKNOWN_KEY ($key)");
    }
  }

  public function groups() {
      return IdentityProvider_Ldap_Driver::groups_for($this);
  }

  public function avatar_url($size=80, $default=null) {
    return sprintf("http://www.gravatar.com/avatar/%s.jpg?s=%d&r=pg%s",
                   md5($this->email), $size, $default ? "&d=" . urlencode($default) : "");
  }

  // Called e.g. by user_profile.php:_can_view_profile_pages()
  // We don't have "empty" users, so just return true
  public function loaded() {
    return true;
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
