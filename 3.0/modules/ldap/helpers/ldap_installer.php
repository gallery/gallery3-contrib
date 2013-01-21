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
class ldap_installer {
  static function can_activate() {
    $messages = array();
    if (array_search("ldap", get_loaded_extensions()) === false) {
      $messages["error"][] =
        t("Cannot install LDAP identity provider as the PHP LDAP extension module is not enabled.");
    } else {
      $messages["warn"][] = IdentityProvider::confirmation_message();
    }
    return $messages;
  }

  static function activate() {
    IdentityProvider::change_provider("ldap");
  }

  static function uninstall() {
    // Delete all groups so that we give other modules an opportunity to clean up
    $ldap_provider = new IdentityProvider("ldap");
    foreach ($ldap_provider->groups() as $group) {
      module::event("group_deleted", $group);
    }
  }

  static function initialize() {
    module::set_version("ldap", 1);
    $root = item::root();
    foreach (IdentityProvider::instance()->groups() as $group) {
      module::event("group_created", $group);
      access::allow($group, "view", $root);
      access::allow($group, "view_full", $root);
    }
  }
}