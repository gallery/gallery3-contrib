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

class user_chroot_Core {
  private static $_album = null;

  /**
   * Return the root album of the current user, or false if the user is not
   * chrooted.
   */
  public static function album() {
    if( is_null(self::$_album) ) {
      self::$_album = false;

      $item = ORM::factory('item')
        ->join('user_chroots', 'items.id', 'user_chroots.album_id')
        ->where('user_chroots.id', '=', identity::active_user()->id)
        ->find();

      if( $item->loaded() ) {
        self::$_album = $item;
      }
    }

    return self::$_album;
  }
}