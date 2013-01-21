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
class quotas_event_Core {  
  static function admin_menu($menu, $theme) {
    // Add a User quotas link to the admin menu.
    $menu->get("content_menu")
      ->append(Menu::factory("link")
               ->id("quotas")
               ->label(t("User quotas"))
               ->url(url::site("admin/quotas")));
  }

  static function user_created($user) {
    // Set up some default values whenever a new user is created.
    $record = ORM::factory("users_space_usage")->where("owner_id", "=", $user->id)->find();
    if (!$record->loaded()) {
      $record->owner_id = $user->id;
      $record->fullsize = 0;
      $record->resize = 0;
      $record->thumb = 0;
      $record->save();
    }
  }

  static function user_before_delete($user) {
    // When deleting a user, all of that user's items get re-assigned to the admin account,
    //   so the file sizes need to be reassigned to the admin user as well.
    $admin = identity::admin_user();
    $admin_record = ORM::factory("users_space_usage")->where("owner_id", "=", $admin->id)->find();
    $deleted_user_record = ORM::factory("users_space_usage")->where("owner_id", "=", $user->id)->find();
    if ($deleted_user_record->loaded()) {
      $admin_record->fullsize = $admin_record->fullsize + $deleted_user_record->fullsize;
      $admin_record->resize = $admin_record->resize + $deleted_user_record->resize;
      $admin_record->thumb = $admin_record->thumb + $deleted_user_record->thumb;
      $admin_record->save();
      $deleted_user_record->delete();
    }
  }

  static function item_before_create($item) {
    // When creating a new item, make sure it's file size won't put the user over their limit.
    //   If it does, throw an error, which will prevent gallery from accepting the file.
    $record = ORM::factory("users_space_usage")->where("owner_id", "=", $item->owner_id)->find();
    if (!$record->loaded()) {
      $record->owner_id = $item->owner_id;
    }
    if ($record->get_usage_limit() == 0) {
      return;
    }
    if ((filesize($item->data_file) + $record->current_usage()) > $record->get_usage_limit()) {
      throw new Exception($item->name . " rejected, user #" . $item->owner_id . " over limit.");
    }
  }

  static function item_created($item) {
    // When a new item is created, add it's file size to the users_space_usage table.
    $record = ORM::factory("users_space_usage")->where("owner_id", "=", $item->owner_id)->find();
    if (!$record->loaded()) {
      $record->owner_id = $item->owner_id;
      $record->fullsize = 0;
      $record->resize = 0;
      $record->thumb = 0;
      $record->save();
    }
    $record->add_item($item);
  }

  static function item_before_delete($item) {
    // When an item is deleted, remove it's file size from the users_space_usage table.
    $record = ORM::factory("users_space_usage")->where("owner_id", "=", $item->owner_id)->find();
    $record->remove_item($item);
  }

  // I can't monitor the item_before_update / item_updated events to adjust for rotated photos,
  //   because they fire when a new photo is uploaded (before it's created) and cause all kinds of weirdness.
  //   So instead, I'm using graphics_rotate to detect a rotate and remove the existing file sizes, and
  //   item_updated_data_file to add in the new data file sizes.
  //   Does item_updated_data_file fire for any other reason? (watermarking?  renaming/moving/deleting/keeporiginal do not cause updated_data_file.)
  static function graphics_rotate($input_file, $output_file, $options) {
    // Remove the current item's file size from the quotas table.
    $item = item::find_by_path(substr(str_replace(VARPATH, "", $input_file), strpos(str_replace(VARPATH, "", $input_file), "/")+1));
    if ($item->loaded()) {
      $record = ORM::factory("users_space_usage")->where("owner_id", "=", $item->owner_id)->find();
      $record->remove_item($item);
    }
  }

  static function item_updated_data_file($item) {
    // Add the current item's file size into the quotas table.
    $record = ORM::factory("users_space_usage")->where("owner_id", "=", $item->owner_id)->find();
    $record->add_item($item);
  }

  static function show_user_profile($data) {
    // Display # of albums and photos/movies on user profile page.
    //   Also display current disc usage.
    $v = new View("user_profile_quotas.html");
    $quotas_record = ORM::factory("users_space_usage")->where("owner_id", "=", $data->user->id)->find();

    $v->user_profile_data = array();
    $v->user_profile_data[(string) t("Albums")] = db::build()->from("items")->where("type", "=", "album")->where("owner_id", "=", $data->user->id)->count_records();
    $v->user_profile_data[(string) t("Uploads")] = db::build()->from("items")->where("type", "!=", "album")->where("owner_id", "=", $data->user->id)->count_records();
    $v->user_profile_data[(string) t("Disc Usage")] = $quotas_record->total_usage_string();

    $data->content[] = (object) array("title" => t("User stats"), "view" => $v);
  }
}
