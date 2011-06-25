<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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
class twitter_installer {

  static function install() {
    Database::instance()
      ->query("CREATE TABLE {twitter_tweets} (
                `id` int(9) NOT NULL AUTO_INCREMENT,
                `item_id` int(9) NOT NULL,
                `sent` int(9) NULL,
                `twitter_id` decimal(20,0) NULL,
                `tweet` varchar(255) NOT NULL,
                `user_id` int(9) NOT NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");
    Database::instance()
      ->query("CREATE TABLE {twitter_users} (
                `id` int(9) NOT NULL AUTO_INCREMENT,
                `oauth_token` varchar(64) NOT NULL,
                `oauth_token_secret` varchar(64) NOT NULL,
                `screen_name` varchar(16) NOT NULL,
                `twitter_user_id` int(9) NOT NULL,
                `user_id` int(9) NOT NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");
    module::set_version("twitter", 1);
    twitter::reset_default_tweet();
  }

  static function deactivate() {
    site_status::clear("twitter_config");
  }
}
