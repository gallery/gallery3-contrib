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
class user_info_event_Core {

  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("user_info")
               ->label(t("User Info"))
               ->url(url::site("admin/user_info")));
  }

  static function user_login() {
	  $log_logins = module::get_var("user_info", "log_logins");
	  if ($log_logins == "Yes") {
	      $user_info = ORM::factory("user_info");
	      $user_info->user_id = identity::active_user()->id;
		  $user_info->user_name = identity::active_user()->name;
		  $user_info->ip_address = $_SERVER['REMOTE_ADDR'];
		  $user_info->time_stamp = time();
	      $user_info->action = "Login";
	      $user_info->save();
	  }
  }

  static function user_logout($user) {
	  $log_logouts = module::get_var("user_info", "log_logouts");
	  if ($log_logouts == "Yes") {
	      $user_info = ORM::factory("user_info");
	      $user_info->user_id = $user->id;
		  $user_info->user_name = $user->name;
		  $user_info->ip_address = $_SERVER['REMOTE_ADDR'];
		  $user_info->time_stamp = time();
	      $user_info->action = "Logout";
	      $user_info->save();
	  }
  }

  static function user_auth_failed($user_name) {
	  $log_failed_logins = module::get_var("user_info", "log_failed_logins");
	  if ($log_failed_logins == "Yes") {
 	      $user_info = ORM::factory("user_info");
	      if (identity::lookup_user_by_name($user_name)) {
		    $user_info->user_id = identity::lookup_user_by_name($user_name)->id;
	      } else {
		  	$user_info->user_id = "";
	      }
		  $user_info->user_name = $user_name;
		  $user_info->ip_address = $_SERVER['REMOTE_ADDR'];
		  $user_info->time_stamp = time();
	      $user_info->action = "Failed Login";
	      $user_info->save();
	  }
  }

  static function user_auth($user) {
	  $log_re_authenticate_logins = module::get_var("user_info", "log_re_authenticate_logins");
	  if ($log_re_authenticate_logins == "Yes") {
	      $user_info = ORM::factory("user_info");
		  $user_info->user_id = $user->id;
		  $user_info->user_name = $user->name;
		  $user_info->ip_address = $_SERVER['REMOTE_ADDR'];
		  $user_info->time_stamp = time();
	      $user_info->action = "Re-Authenticate Login";
	      $user_info->save();
	  }
  }

  static function user_created($pending_user) {
	  $log_user_created = module::get_var("user_info", "log_user_created");
	  if ($log_user_created == "Yes") {
	      $user_info = ORM::factory("user_info");
		  $user_info->user_id = $pending_user->id;
		  $user_info->user_name = $pending_user->name;
		  $user_info->ip_address = $_SERVER['REMOTE_ADDR'];
		  $user_info->time_stamp = time();
	      $user_info->action = "User Created";
	      $user_info->save();
	  }
  }


}
