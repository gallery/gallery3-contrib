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

class user_chroot_event_Core {

  /**
   * Called just before a user deletion.
   */
  public static function user_before_delete($user) {
    ORM::factory('user_chroot', $user->id)->delete();
  }

  /**
   * Called just before an item deletion.
   */
  public static function item_before_delete($item) {
    if( $item->is_album() ) {
      ORM::factory('user_chroot')->where('album_id', '=', $item->id)->delete();
    }
  }

  /**
   * Called when building the 'Add user' form for an admin.
   */
  static function user_add_form_admin($user, $form) {
    $form->add_user->dropdown('user_chroot')
      ->label(t("Root Album"))
      ->options(self::albumsTreeArray())
      ->selected(1);
  }

  /**
   * Called just after a user has been added by an admin.
   */
  public static function user_add_form_admin_completed($user, $form) {
    if( $form->add_user->user_chroot->value > 1 ) {
      $user_chroot = ORM::factory('user_chroot');
      $user_chroot->id = $user->id;
      $user_chroot->album_id = $form->add_user->user_chroot->value;
      $user_chroot->save();
    }
  }

  /**
   * Called when building the 'Edit user' form for an admin.
   */
  public static function user_edit_form_admin($user, $form) {
    $user_chroot = ORM::factory('user_chroot', $user->id);

    $selected = ( $user_chroot->loaded() )
      ? $user_chroot->album_id
      : 1;

    $form->edit_user->dropdown('user_chroot')
      ->label(t("Root Album"))
      ->options(self::albumsTreeArray())
      ->selected($selected);
  }

  /**
   * Called just after a user has been edited by an admin.
   */
  public static function user_edit_form_admin_completed($user, $form) {
    if( $form->edit_user->user_chroot->value <= 1 ) {
      ORM::factory('user_chroot')->delete($user->id);

    } else {
      $user_chroot = ORM::factory('user_chroot', $user->id);

      if( !$user_chroot->loaded() ) {
        $user_chroot = ORM::factory('user_chroot');
        $user_chroot->id = $user->id;
      }

      $user_chroot->album_id = $form->edit_user->user_chroot->value;
      $user_chroot->save();
    }
  }

  /**
   * Generate an array representing the hierarchy of albums.
   */
  private static function albumsTreeArray($level_marker = '    ') {
    $tree = array();
    $albums = ORM::factory('item')
      ->where('type', '=', 'album')
      ->order_by('left_ptr', 'ASC')
      ->find_all();

    foreach($albums as $album) {
      $tree[$album->id] = html::clean(
        str_repeat($level_marker, $album->level - 1).' '.$album->title );
    }

    return $tree;
  }
}
