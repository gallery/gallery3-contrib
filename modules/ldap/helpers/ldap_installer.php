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
class ldap_installer {
  static function install() {
  }

  static function uninstall() {
    // Delete all users and groups so that we give other modules an opportunity to clean up
    foreach (ORM::factory("user")->find_all() as $user) {
      $user->delete();
    }

    foreach (ORM::factory("group")->find_all() as $group) {
      $group->delete();
    }

    try {
      Session::instance()->destroy();
    } catch (Exception $e) {
      // We don't care if there was a problem destroying the session.
    }
  }
}