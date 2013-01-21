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

class hide_Core {

  /**
   * Value defining no group can see hidden items.
   */
  const NONE = 0;

  /**
   * Returns the group list for a dropdown widget.
   *
   * @return array  the group list
   */
  static function get_groups_as_dropdown_options() {
    $options = ORM::factory("group")->select_list("id", "name");
    return array_merge(array(self::NONE => t("Nobody")), $options);
  }

  /**
   * Returns the hidden_item model related to the given item.
   *
   * There is an attempt to fetch the model from the database through the model
   * cache. If it fails, a new unsaved model is created.
   *
   * @param Item_Model $item    the item
   * @return Hidden_Item_Model  the related hidden_item model
   */
  static function get_hidden_item_model(Item_Model $item) {
    try {
      $model = model_cache::get("item", $id);
    }
    catch (Exception $e) {
      $model = ORM::factory("hidden_item");
      $model->item_id = $item->id;
      $model->validate();
    }

    return $model;
  }

  /**
   * Returns whether the given item can be hidden.
   *
   * @param Item_Model $item  the item
   * @return bool
   */
  static function can_be_hidden(Item_Model $item) {
    if (empty($item)) {
      return false;
    }

    if ($item->type == "album") {
      return false;
    }
    
    return true;
  }

  /**
   * Returns whether the given item is hidden.
   *
   * @param Item_Model $item  the item
   * @return bool
   */
  static function is_hidden(Item_Model $item) {
    $model = self::get_hidden_item_model($item);
    return $model->loaded();
  }

  /**
   * Hides the given item.
   *
   * @param Item_Model $item  the item to hide
   */
  static function hide(Item_Model $item) {
    if (self::is_hidden($item)) {
      return;
    }

    $hidden_item = self::get_hidden_item_model($item);
    $hidden_item->save();
  }

  /**
   * Allows the given item to be displayed again.
   *
   * @param Item_Model $item  the item to display
   */
  static function show(Item_Model $item) {
    if (!self::is_hidden($item)) {
      return;
    }

    $hidden_item = self::get_hidden_item_model($item);
    $hidden_item->delete();
  }

  /**
   * Returns whether the active user can view hidden items.
   *
   * @return bool
   */
  static function can_view_hidden_items() {
    if (identity::active_user()->admin) {
      return true;
    }

    $authorized_group = module::get_var("hide", "access_permissions");
    if (in_array($authorized_group, identity::group_ids_for_active_user())) {
      return true;
    }

    return false;
  }

  /**
   * Returns whether the active user can hide any items.
   *
   * @return bool
   */
  static function can_hide() {
    if (identity::active_user()->admin) {
      return true;
    }

    return false;
  }
}
