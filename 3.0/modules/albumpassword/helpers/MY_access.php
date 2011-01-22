<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
    if (!self::can($perm_name, $item)) {
      if ($perm_name == "view") {
        // Treat as if the item didn't exist, don't leak any information.
        throw new Kohana_404_Exception();
      } else {
        self::forbidden();
      }

    // Begin rWatcher modifications.
    //   Throw a 404 error when a user attempts to access a protected item,
	//   unless the password has been provided, or the user is the item's owner.
    } elseif (module::get_var("albumpassword", "hideonly") == false) {
      $album_item = "";
      do {
        if ($album_item == "") {
          if ($item->is_album()) {
            $album_item = $item;
          } else {
            $album_item = $item->parent();
          }
        } else {
          $album_item = $album_item->parent();
        }		

        $existing_password = ORM::factory("items_albumpassword")->where("album_id", "=", $album_item->id)->find();
        if ($existing_password->loaded()) {
          if ((cookie::get("g3_albumpassword") != $existing_password->password) &&
              (identity::active_user()->id != $album_item->owner_id)) {
            throw new Kohana_404_Exception();
          }
        }
      } while ($album_item->parent_id > 0);
    }
  }
}
