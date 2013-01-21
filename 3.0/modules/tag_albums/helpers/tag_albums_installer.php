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
class tag_albums_installer {
  static function install() {
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {tags_album_ids} (
               `id` int(9) NOT NULL auto_increment,
               `album_id` int(9) NOT NULL,
               `tags` varchar(2048) default NULL,
               `search_type` varchar(128) NOT NULL,
               PRIMARY KEY (`id`),
               KEY(`album_id`, `id`))
               DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE IF NOT EXISTS {tags_album_tag_covers} (
               `id` int(9) NOT NULL auto_increment,
               `tag_id` int(9) NOT NULL,
               `photo_id` int(9) NOT NULL,
               PRIMARY KEY (`id`),
               KEY(`tag_id`, `id`))
               DEFAULT CHARSET=utf8;");

    // Set up some default values.
    module::set_var("tag_albums", "tag_sort_by", "name");
    module::set_var("tag_albums", "tag_sort_direction", "ASC");
    module::set_var("tag_albums", "subalbum_sort_by", "title");
    module::set_var("tag_albums", "subalbum_sort_direction", "ASC");
    module::set_var("tag_albums", "tag_index", "default");
    module::set_var("tag_albums", "tag_index_scope", "0");
    module::set_var("tag_albums", "tag_index_filter_top", "0");
    module::set_var("tag_albums", "tag_index_filter_bottom", "0");

    // Set the module's version number.
    module::set_version("tag_albums", 4);
  }

  static function upgrade($version) {
    $db = Database::instance();
    if ($version == 1) {
      module::set_var("tag_albums", "tag_index", "default");
      module::set_var("tag_albums", "tag_index_scope", "0");
      module::set_var("tag_albums", "tag_index_filter", "0");
      module::set_version("tag_albums", 2);
    }

    if ($version == 2) {
      $db->query("CREATE TABLE IF NOT EXISTS {tags_album_tag_covers} (
               `id` int(9) NOT NULL auto_increment,
               `tag_id` int(9) NOT NULL,
               `photo_id` int(9) NOT NULL,
               PRIMARY KEY (`id`),
               KEY(`tag_id`, `id`))
               DEFAULT CHARSET=utf8;");
      module::set_version("tag_albums", 3);
    }

    if ($version == 3) {
      module::set_var("tag_albums", "tag_index_filter_top", module::get_var("tag_albums", "tag_index_filter", "0"));
      module::set_var("tag_albums", "tag_index_filter_bottom", module::get_var("tag_albums", "tag_index_filter", "0"));
      module::clear_var("tag_albums", "tag_index_filter");
      module::set_version("tag_albums", 4);
    }
  }
  
  static function deactivate() {
    site_status::clear("tag_albums_needs_tag");
  }

  static function can_activate() {
    $messages = array();
    if (!module::is_active("tag")) {
      $messages["warn"][] = t("The Tag Albums module requires the Tags module.");
    }
    return $messages;
  }

  static function uninstall() {
    module::delete("tag_albums");
  }
}
