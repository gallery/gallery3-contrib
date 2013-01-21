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

class access extends access_Core {

  /**
   * If the user is chrooted, deny access outside of the chroot.
   */
  static function user_can($user, $perm_name, $item) {
    if( $user->id == identity::active_user()->id && user_chroot::album() ) {
      if( $item->left_ptr < user_chroot::album()->left_ptr || user_chroot::album()->right_ptr < $item->right_ptr ) {
        return false;
      }
    }

    return parent::user_can($user, $perm_name, $item);
  }

  /**
   * Copied from modules/gallery/helpers/access.php because of the usage of self::
   */
  static function can($perm_name, $item) {
    return self::user_can(identity::active_user(), $perm_name, $item);
  }

  /**
   * Copied from modules/gallery/helpers/access.php because of the usage of self::
   */
  static function required($perm_name, $item) {
    if (!self::can($perm_name, $item)) {
      if ($perm_name == "view") {
        // Treat as if the item didn't exist, don't leak any information.
        throw new Kohana_404_Exception();
      } else {
        self::forbidden();
      }
    }
  }
}
