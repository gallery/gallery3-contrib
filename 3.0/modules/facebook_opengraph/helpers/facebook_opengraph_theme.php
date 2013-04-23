<?php defined("SYSPATH") or die("No direct script access.");/**
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
class facebook_opengraph_theme {
  static function head($theme) {
    if ($theme->item()) {
      $item = $theme->item();
      $image_url = $item->thumb_url(true);
      $page_url = url::abs_current(true);
	  return "<meta property=\"og:image\" content=\"$image_url\"/>
		  <meta property=\"og:title\" content=\"$item->title\"/>
		  <meta property=\"og:type\" content=\"article\"/>
		  <meta property=\"og:url\" content=\"$page_url\"/>";
    }
  }


}
