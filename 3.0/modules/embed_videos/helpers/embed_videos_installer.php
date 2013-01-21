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

class embed_videos_installer {
  static function install() {
    $db = Database::instance();
    $db->query("CREATE TABLE {embedded_videos} (
						`id` int(9) NOT NULL auto_increment,
						`embed_code` varchar(2048) DEFAULT NULL,
						`source` varchar(64) DEFAULT NULL,
						`item_id` int(9) NOT NULL,
						PRIMARY KEY (`id`),
						KEY (`item_id`, `id`))
						DEFAULT CHARSET=utf8;");
    module::set_version("embed_videos", 2);
    //exec("cd modules/gallery/controllers/; ln -s ../../embed/controllers/embeds.php embeds.php");
  }

  static function deactivate() {

  }
  static function uninstall() {
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {embedded_videos};");
    module::delete("embed_videos");
  }
}
