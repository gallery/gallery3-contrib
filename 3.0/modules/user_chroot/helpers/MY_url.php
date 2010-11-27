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

// /!\ Hack: There is no good way to extend gallery url
$gallery_url = file_get_contents(MODPATH . 'gallery/helpers/MY_url.php');
$gallery_url = preg_replace('#^<\?php #', '', $gallery_url);
$gallery_url = preg_replace('#class url extends url_Core#', 'class url_G3 extends url_Core', $gallery_url);
eval($gallery_url);

class url extends url_G3 {
  static function parse_url() {
    if( user_chroot::album() ) {
      if( Router::$current_uri == '' ) {
        Router::$controller = false;
      }

      Router::$current_uri = trim(user_chroot::album()->relative_url().'/'.Router::$current_uri, '/');
    }

    return parent::parse_url();
  }

  static function site($uri = '', $protocol = FALSE) {
    if( user_chroot::album() ) {
      $uri = preg_replace('#^'.user_chroot::album()->relative_url().'#', '', $uri);
    }

    return parent::site($uri, $protocol);
  }
}