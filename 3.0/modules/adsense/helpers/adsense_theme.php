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
 
 //header_bottom or footer
class adsense_theme {
  static function header_bottom($theme) {
	if(module::get_var("adsense","location") == "header") {
		$code = module::get_var("adsense", "code");
		if (!$code) {
		  return;
		}
		$google_code = '
		<script type="text/javascript">' . $code . '</script>
		<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>';

		return $google_code;
	}
  }
  
  static function footer($theme) {
	if(module::get_var("adsense","location") == "footer") {
		$code = module::get_var("adsense", "code");
		if (!$code) {
		  return;
		}
		$google_code = '
		<script type="text/javascript">' . $code . '</script>
		<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>';

		return $google_code;
	}
  }  
}
