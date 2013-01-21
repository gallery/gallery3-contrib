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
class calendarview_Core {
  static function get_items_count($where=array()) {
    // Returns the number of viewable items identified by $where.
    return ORM::factory("item")
      ->viewable()
      ->merge_where($where)
      ->order_by("captured", "ASC")
      ->count_all();
  }

  static function get_items($limit=null, $offset=null, $where=array()) {
    // Returns the items identified by $where, up to $limit, and starting at $offset.
    return ORM::factory("item")
      ->viewable()
      ->merge_where($where)
      ->order_by("captured", "ASC")
      ->find_all($limit, $offset);    
  }

  static function get_position($item, $where=array()) {
    // Get's $item's position within $where.
    return ORM::factory("item")
      ->viewable()
      ->merge_where($where)
      ->where("items.id", "<=", $item->id)
      ->order_by("captured", "ASC")
      ->count_all();
  }
}
