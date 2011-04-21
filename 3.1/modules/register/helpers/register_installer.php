<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class register_installer {
  static function install() {
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {pending_users} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` varchar(32) NOT NULL,
                 `state` int(9) NOT NULL DEFAULT 0,
                 `full_name` varchar(255) NOT NULL,
                 `email` varchar(64) default NULL,
                 `hash` char(32) default NULL,
                 `url` varchar(255) default NULL,
                 `request_date` int(9) not NULL DEFAULT 0,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`hash`, `state`),
                 UNIQUE KEY(`name`))
               DEFAULT CHARSET=utf8;");

    module::set_var("registration", "policy", "admin_only");
    module::set_var("registration", "default_group", "");
    module::set_var("registration", "email_verification", false);

    module::set_version("register", 1);
  }

  static function uninstall() {
    Database::instance()->query("DROP TABLE IF EXISTS {pending_users};");
  }
}