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

/*
 * /!\ Hack
 * Module 'gallery' already provides a class 'url' which extends url_Core. This
 * hack renames class 'url', so user_chroot can have its own (which extends the
 * original)
 */
$gallery_url = file_get_contents(MODPATH . 'gallery/helpers/MY_url.php');
$gallery_url = str_replace('<?php ', '', $gallery_url);
$gallery_url = str_replace('class url extends url_Core', 'class url_G3 extends url_Core', $gallery_url);
eval($gallery_url);

class url extends url_G3 {
  /**
   * Add the chroot path at the begining of the requested URI
   */
  static function parse_url() {
    if( user_chroot::album() ) {
      if( Router::$controller == 'albums' && Router::$current_uri == '' ) {
        // Root album requested
        Router::$controller = null;
        Router::$current_uri = trim(user_chroot::album()->relative_url().'/'.Router::$current_uri, '/');

      } else if( is_null(Router::$controller) && Router::$current_uri != '' ) {
        // Non-root album requested
        Router::$current_uri = trim(user_chroot::album()->relative_url().'/'.Router::$current_uri, '/');
      }
    }

    return parent::parse_url();
  }

  /**
   * Remove the chroot part of the URI.
   */
  static function site($uri = '', $protocol = FALSE) {
    if( user_chroot::album() ) {
      $uri = preg_replace('#^'.user_chroot::album()->relative_url().'#', '', $uri);
    }

    return parent::site($uri, $protocol);
  }
}