<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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
    // Hide the contents of a password protected album,
    // Unless the current user is an admin, or the albums owner.

    $model = item_Core::viewable($model);
    $album_item = ORM::factory("item")->where("id", "=", $model->id)->find();

    // Figure out if the user can access this album.
    $deny_access = false;
    $existing_password = ORM::factory("items_albumpassword")->where("album_id", "=", $model->id)->find();
    if ($existing_password->loaded()) {
      if ((cookie::get("g3_albumpassword") != $existing_password->password) &&
          (identity::active_user()->id != $album_item->owner_id))
        $deny_access = true;
    }

    // set access::DENY if necessary.
    if ($deny_access == true) {
      $view_restrictions = array();
      if (!identity::active_user()->admin) {
        foreach (identity::group_ids_for_active_user() as $id) {
          $view_restrictions[] = array("items.view_$id", "=", access::DENY);
        }
      }
    }
    if (count($view_restrictions)) {
      $model->and_open()->merge_or_where($view_restrictions)->close();
    }

    return $model;
  }
}
