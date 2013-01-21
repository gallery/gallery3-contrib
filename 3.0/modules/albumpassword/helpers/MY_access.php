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

class access extends access_Core {
  static function required($perm_name, $item) {
    // Original code from the required function in modules/gallery/helpers/access.php.
    if (!access::can($perm_name, $item)) {
      if ($perm_name == "view") {
        // Treat as if the item didn't exist, don't leak any information.
        throw new Kohana_404_Exception();
      } else {
        access::forbidden();
      }

    // Begin rWatcher modifications.
    //   Throw a 404 error when a user attempts to access a protected item,
    //   unless the password has been provided, or the user is the item's owner.
    } elseif (module::get_var("albumpassword", "hideonly") == false) {
      $item_protected = ORM::factory("albumpassword_idcache")->where("item_id", "=", $item->id)->order_by("cache_id")->find_all();
      if (count($item_protected) > 0) {
        $existing_password = ORM::factory("items_albumpassword")->where("id", "=", $item_protected[0]->password_id)->find();
        if ($existing_password->loaded()) {
          if ((cookie::get("g3_albumpassword") != $existing_password->password) &&
              (identity::active_user()->id != $item->owner_id) &&
              (!identity::active_user()->admin)) {
            throw new Kohana_404_Exception();
          }
        }
      }
    }
  }
}
