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

class user_chroot_event_Core {

  /**
   * Called just before a user is deleted. This will remove the user from
   * the user_chroot directory.
   */
  static function user_before_delete($user) {
    ORM::factory("user_chroot")->delete($user->id);
  }

  /**
   * Called when admin is adding a user
   */
  static function user_add_form_admin($user, $form) {
    $form->add_user->dropdown("user_chroot")
      ->label(t("Root Album"))
      ->options(self::createGalleryArray())
      ->selected(0);
  }

  /**
   * Called after a user has been added
   */
  static function user_add_form_admin_completed($user, $form) {
    $user_chroot = ORM::factory("user_chroot")->where("id", "=", $user->id)->find();
    $user_chroot->id = $user->id;
    $user_chroot->album_id = $form->add_user->user_chroot->value;
    $user_chroot->save();
  }

  /**
   * Called when admin is editing a user
   */
  static function user_edit_form_admin($user, $form) {
    $user_chroot = ORM::factory("user_chroot")->where("id", "=", $user->id)->find();
    if ($user_chroot->loaded()) {
      $selected = $user_chroot->album_id;
    } else {
      $selected = 0;
    }
    $form->edit_user->dropdown("user_chroot")
      ->label(t("Root Album"))
      ->options(self::createGalleryArray())
      ->selected($selected);
  }

  /**
   * Called after a user had been edited by the admin
   */
  static function user_edit_form_admin_completed($user, $form) {
    $user_chroot = ORM::factory("user_chroot")->where("id", "=", $user->id)->find();
    if ($user_chroot->loaded()) {
      $user_chroot->album_id = $form->edit_user->user_chroot->value;
    } else {
      $user_chroot->id = $user->id;
      $user_chroot->album_id = $form->edit_user->user_chroot->value;
    }
    $user_chroot->save();
  }


  /**
   * Creates an array of galleries
   */
  static function createGalleryArray() {
    $array[0] = "none";
    $root = ORM::factory("item", 1);
    self::tree($root, "", $array);
    return $array;
  }

  /**
   * recursive function to build array for drop down list
   */
  static function tree($parent, $dashes, &$array) {
    if ($parent->id == "1") {
      $array[$parent->id] = ORM::factory("item", 1)->title;
    } else {
      $array[$parent->id] = "$dashes $parent->name";
    }

    $albums = ORM::factory("item")
      ->where("parent_id", "=", $parent->id)
      ->where("type", "=", "album")
      ->order_by("title", "ASC")
      ->find_all();
    foreach ($albums as $album) {
      self::tree($album, "-$dashes", $array);
    }
    return;
  }
}
