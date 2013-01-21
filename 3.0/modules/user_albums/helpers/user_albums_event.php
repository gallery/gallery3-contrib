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
class user_albums_event_Core {
  /**
   * Create an album for the newly created user and give him view and edit permissions.
   */
  static function user_created($user) {
    // Create a group with the same name, if necessary
    $group_name = "auto: {$user->name}";
    $group = identity::lookup_group_by_name($group_name);
    if (!$group) {
      $group = identity::create_group($group_name);
      identity::add_user_to_group($user, $group);
    }

    // Create an album for the user, if it doesn't exist
    $album = ORM::factory("item")
      ->where("parent_id", "=", item::root()->id)
      ->where("name", "=", $user->name)
      ->find();
    if (!$album->loaded()) {
      $album->type = "album";
      $album->name = $user->name;
      $album->title = "{$user->name}'s album";
      $album->parent_id = item::root()->id;
      $album->sort_column = "weight";
      $album->sort_order = "asc";
      $album->save();

      access::allow($group, "view", item::root());
      access::allow($group, "view_full", $album);
      access::allow($group, "edit", $album);
      access::allow($group, "add", $album);
    }
  }
}
