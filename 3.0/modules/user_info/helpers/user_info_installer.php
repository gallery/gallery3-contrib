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
class user_info_installer {
  static function activate() {
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {user_infos} (
                 `id` int(11) NOT NULL auto_increment,
                 `user_id` varchar(128) default NULL,
                 `user_name` varchar(128) default NULL,
                 `ip_address` varchar(255) default NULL,
                 `time_stamp` varchar(128) default NULL,
                 `action` varchar(128) default NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");

    module::set_var("user_info", "per_page", 25);
    module::set_var("user_info", "default_sort_column", "id");
    module::set_var("user_info", "default_sort_order", "DESC");
    module::set_var("user_info", "use_default_gallery_date_format", "Yes");
    module::set_var("user_info", "date_format", "D d M Y h:i:s A T");
    module::set_var("user_info", "log_logins", "Yes");
    module::set_var("user_info", "color_login", "#008000");
    module::set_var("user_info", "log_logouts", "Yes");
    module::set_var("user_info", "color_logout", "#0000FF");
    module::set_var("user_info", "log_failed_logins", "Yes");
    module::set_var("user_info", "color_failed_login", "#FF0000");
    module::set_var("user_info", "log_re_authenticate_logins", "No");
    module::set_var("user_info", "color_re_authenticate_login", "#800080");
    module::set_var("user_info", "log_user_created", "No");
    module::set_var("user_info", "color_user_created", "#FF8C00");
    module::set_version("user_info", 1);
  }


  static function upgrade($version) {
//    $db = Database::instance();
    if ($version == 1) {
//      $db->query("ALTER TABLE {comments} CHANGE `state` `state` varchar(15) default 'unpublished'");
      module::set_version("user_info", $version = 2);
    }
//
//    if ($version == 2) {
//      module::set_var("comment", "access_permissions", "everybody");
//      module::set_version("comment", $version = 3);
//    }
  }



  static function uninstall() {
//    $db = Database::instance();
//    $db->query("DROP TABLE IF EXISTS {userinfo};");
//    /* @todo Put database table drops here */
//    module::delete("userinfo");
  }


//  static function deactivate() {
//    site_status::clear("user_info");
//    $db = Database::instance();
//    $db->query("DROP TABLE IF EXISTS {user_infos};");
//  }


}
