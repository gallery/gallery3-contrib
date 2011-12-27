<?php defined("SYSPATH") or die("No direct script access.");/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class content_warning_theme {	
	static function head($theme) {
		$h = '
			<script type="text/javascript" src="/lib/jquery.js"></script>
			<link type="text/css" href="/modules/content_warning/jqModal.css" rel="stylesheet" />
			<script type="text/javascript" src="/modules/content_warning/jqModal.js"></script>
			<script type="text/javascript">
				jQuery().ready(function($){
					$(\'#dialog\').jqm().jqmShow({});
		      	});
			</script>			
		';
		return $h;
	}
	
	static function page_top($theme) {		
		$cw = '
			<div class="jqmWindow" id="dialog">
				<hr />
				<h3>'.module::get_var("content_warning", "title").'</h3>
				<br />
				<p>'.nl2br(module::get_var("content_warning", "message")).'</p>
				<br />
				<div id="cw_buttons_container">
					<div class="cw_buttons" id="cw_ko">
						<a href="'.module::get_var("content_warning", "exit_link_url").'">'.module::get_var("content_warning", "exit_link_text").'</a>
					</div>
					<div class="cw_buttons" id="cw_ok">					
						<a href="/index.php/content_warning?cw=1">'.module::get_var("content_warning", "enter_link_text").'</a>	
					</div>
				</div>
			</div>
		';
		if(!isset($_COOKIE['cw_agree'])) {
			return $cw;
		}
	}
}