<?php defined("SYSPATH") or die("No direct script access.");/**
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
class dynamic_Core {
  static function get_display_count($dynamic_type_definition) {
    $display_limit = $dynamic_type_definition->limit;
    $children_count = ORM::factory("item")
      ->viewable()
      ->where("type", "!=", "album")
      ->count_all();
    if (!empty($display_limit)) {
      $children_count = min($children_count, $display_limit);
    }
    return $children_count;
  }

  static function items($key_field, $limit=null, $offset=null) {
    return ORM::factory("item")
      ->viewable()
      ->where("type", "!=", "album")
      ->order_by($key_field, "DESC")
      ->find_all($limit, $offset);
  }

  /**
   * Find the position of the given item in the tag collection.  The resulting
   * value is 1-indexed, so the first child in the album is at position 1.
   *
   * @param Tag_Model  $tag
   * @param Item_Model $item
   * @param array      $where an array of arrays, each compatible with ORM::where()
   */
  static function get_position($dynamic_type_definition, $item) {

    $sort_column = $dynamic_type_definition->key_field;
    $display_limit = self::get_display_count($dynamic_type_definition);

    $query_model = ORM::factory("item");

    // If the comparison column has NULLs in it, we can't use comparators on it
    // and will have to deal with it the hard way.
    $count = $query_model->viewable()
      ->where($sort_column, "IS", null)
      ->where("type", "!=", "album")
      ->count_all();

    if (empty($count)) {
      // There are no NULLs in the sort column, so we can just use it directly.

      $position = $query_model->viewable()
        ->where($sort_column, ">", $item->$sort_column)
        ->where("type", "!=", "album")
        ->order_by(array($dynamic_type_definition->key_field => "DESC", "id" => "ASC"))
        ->count_all();

      // We stopped short of our target value in the sort (notice that we're
      // using a inequality comparator above) because it's possible that we have
      // duplicate values in the sort column.  An equality check would just
      // arbitrarily pick one of those multiple possible equivalent columns,
      // which would mean that if you choose a sort order that has duplicates,
      // it'd pick any one of them as the child's "position".
      //
      // Fix this by doing a 2nd query where we iterate over the equivalent
      // columns and add them to our position count.
      foreach ($query_model->viewable()
               ->select("id")
               ->where($sort_column, "=", $item->$sort_column)
               ->where("type", "!=", "album")
               ->order_by(array("id" => "ASC"))
               ->find_all($display_limit) as $row) {
        $position++;
        if ($row->id == $item->id) {
          break;
        }
      }
    } else {
      // There are NULLs in the sort column, so we can't use MySQL comparators.
      // Fall back to iterating over every child row to get to the current one.
      // This can be wildly inefficient for really large albums, but it should
      // be a rare case that the user is sorting an album with null values in
      // the sort column.
      //
      // Reproduce the children() functionality here using Database directly to
      // avoid loading the whole ORM for each row.
      $order_by = array($sort_column => "DESC", $order_by["id"] => "ASC");

      $position = 0;
      foreach ($query_model->viewable()
               ->select("id")
               ->where("parent_id", "=", $album->id)
               ->where("type", "!=", "album")
               ->order_by($order_by)
               ->find_all($display_limit) as $row) {
        $position++;
        if ($row->id == $item->id) {
          break;
        }
      }
    }

    return $position;
  }
}
