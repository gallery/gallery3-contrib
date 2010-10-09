<?php defined("SYSPATH") or die("No direct script access.");
/**
 * GreyDragon Theme - a theme for Menalto Gallery 3
 * Copyright (C) 2009-2010 Serguei Dosyukov
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
class greydragon_theme {

  static function credits($theme) {
    $theme_id = module::get_var("gallery", "active_site_theme"); 
    $theme_info = new ArrayObject(parse_ini_file(THEMEPATH . "$theme_id/theme.info"), ArrayObject::ARRAY_AS_PROPS);

    return '<li><a href="http://codex.gallery2.org/Gallery3:Themes:greydragon" target="_blank">' 
      . $theme_info->name . ' ' . $theme_info->version . '</a></li>';
  }
}

