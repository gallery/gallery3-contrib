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

class star_Core {

  /**
   * Value defining no group can see hidden items.
   */
//  const NONE = 0;

  /**
   * Returns the group list for a dropdown widget.
   *
   * @return array  the group list
   */
 // static function get_groups_as_dropdown_options() {
 //   $options = ORM::factory("group")->select_list("id", "name");
 //   return array_merge(array(self::NONE => t("Nobody")), $options);
 // }

  /**
   * Returns the starred_item model related to the given item.
   *
   * There is an attempt to fetch the model from the database through the model
   * cache. If it fails, a new unsaved model is created.
   *
   * @param Item_Model $item    the item
   * @return Starred_Item_Model  the related starred_item model
   */
  static function get_starred_item_model(Item_Model $item) {
    try {
      $model = model_cache::get("item", $id);
    }
    catch (Exception $e) {
      $model = ORM::factory("starred_item");
      $model->item_id = $item->id;
      $model->validate();
    }

    return $model;
  }

  static function get_star_user_model() {
    $model = ORM::factory("starred_only_user");
    $model->user_id = identity::active_user()->id;
    $model->validate();
    return $model;
  }

  /**
   * Returns whether the given item can be starred.
   *
   * @param Item_Model $item  the item
   * @return bool
   */
  static function can_be_starred(Item_Model $item) {
    if (empty($item)) {
      return false;
    }

    //if ($item->type == "album") {
    //  return false;
    //}
    
    return true;
  }

  /**
   * Returns whether the given item is starred.
   *
   * @param Item_Model $item  the item
   * @return bool
   */
  static function is_starred(Item_Model $item) {
    $model = self::get_starred_item_model($item);
    return $model->loaded();
  }

  /**
   * Stars the given item.
   *
   * @param Item_Model $item  the item to star
   */
  static function star(Item_Model $item) {
    if (self::is_starred($item)) {
      return;
    }

    $starred_item = self::get_starred_item_model($item);
    $starred_item->save();
  }

  /**
   * Allows the given item to be unstarred.
   *
   * @param Item_Model $item  the item to display
   */
  static function unstar(Item_Model $item) {
    if (!self::is_starred($item)) {
      return;
    }

    $starred_item = self::get_starred_item_model($item);
    $starred_item->delete();
  }

  static function star_only_on() {
    if (self::show_only_starred_items()) {
      return;
    }

    $star_user = self::get_star_user_model();
    $star_user->save();
  }
  static function star_only_off() {
    if (!self::show_only_starred_items()) {
      return;
    }

    $star_user = self::get_star_user_model();
    $star_user->delete();
  }

  /**
   * Returns whether the active user shows only starred items.
   *
   * @return bool
   */
  static function show_only_starred_items() {
    $model = self::get_star_user_model();
    return $model->loaded();
  }

  /**
   * Returns whether the active user can star any items.
   *
   * @return bool
   */
  static function can_star() {
    if (identity::active_user()->admin) {
      return true;
    }

    return false;
  }
}
