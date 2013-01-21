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
class content_warning_installer {
	static function install() {
		module::set_var("content_warning", "title", "Warning!");
    	module::set_var("content_warning", "message", "This site contains inappropriate material");
    	module::set_var("content_warning", "enter_link_text", "Enter");
    	module::set_var("content_warning", "exit_link_text", "Exit");
    	module::set_var("content_warning", "exit_link_url", "http://www.google.com");
		module::set_version("content_warning", 1);
  	}

  	static function upgrade($version) {
  		//module::set_version("content_warning", 2);
	}

  	static function uninstall() {
  		module::clear_var("content_warning", "title");
    	module::clear_var("content_warning", "message");
    	module::clear_var("content_warning", "enter_link_text");  	
    	module::clear_var("content_warning", "exit_link_text");
    	module::clear_var("content_warning", "exit_link_url");
    	module::delete("content_warning");
  	}
}
