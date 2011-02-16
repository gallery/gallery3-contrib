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

class item extends item_Core {
  static function viewable($model) {
    // Hide password protected albums until the correct password is entered, 
    // unless the current user is an admin, or the albums owner.

    $model = item_Core::viewable($model);

    // If the user is an admin, don't hide anything anything.
    //   If not, hide whatever is restricted by an album password
    //   that the current user is not the owner of.
    if (!identity::active_user()->admin) {

      // Display items that are not in idcaches.
      $model->and_open()->join("albumpassword_idcaches", "items.id", "albumpassword_idcaches.item_id", "LEFT OUTER")
            ->and_where("albumpassword_idcaches.item_id", "IS", NULL);

      // ... Unless their password id corresponds with a valid password.
      $existing_password = ORM::factory("items_albumpassword")->where("password", "=", cookie::get("g3_albumpassword"))->find_all();
      if (count($existing_password) > 0) {
        foreach ($existing_password as $one_password) {
          $model->or_where("albumpassword_idcaches.password_id", "=", $one_password->id);
        }
      }

      // Or the current user is the owner of the item.
      $model->or_where("items.owner_id", "=", identity::active_user()->id)->close();
    }

    return $model;
  }
}
