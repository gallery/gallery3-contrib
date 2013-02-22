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
class movie_tools_installer {
  static function install() {
    $formats = movie_tools::get_formats();
    foreach ($formats as $id => $data) {
      module::set_var("movie_tools", "allow_$id", 0);
    }
  }

  static function can_activate() {
    $messages = array();
    if (module::get_version("gallery") < 56) {
      $messages["warn"][] = t("Movie Tools requires Gallery v3.0.5 or newer.");
    }
    return $messages;
  }

  static function uninstall() {
    module::clear_all_vars("movie_tools");
  }
}
