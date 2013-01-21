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
class addthis_theme_Core {
  static function head($theme) {
    $addthisuser = module::get_var("addthis", "username");
    $theme->css("addthis_menu.css");
    return "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#username=$addthisuser\"></script>\n" .
	"<script type=\"text/javascript\">" .
	"var addthis_config = {ui_header_color: \"#ffffff\",ui_header_background: \"#000000\",ui_offset_top: -15,ui_offset_left: 0" . 
	"}</script>";
  }
}
