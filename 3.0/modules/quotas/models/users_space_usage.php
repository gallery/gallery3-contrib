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
class Users_Space_Usage_Model_Core extends ORM {
  static function _format_usage($bytes) {
    $usage_unit = array("B","kB","MB","GB","TB","PB","EB","ZB","YB");
    $unit_counter = 0;
    while ($bytes >= 1024) {
      $bytes = $bytes / 1024;
      $unit_counter++;
    }

    return (number_format($bytes, 2) . " " . $usage_unit[$unit_counter]);
  }

  public function partial_usage_string($file_type) {
    // Return a string for the user's total usage in fullsizes, resizes, or thumbs
    //   with the appropriate file size prefix.
    // $file_type should be either fullsize, resize, or thumb.
    return (Users_Space_Usage_Model_Core::_format_usage($this->$file_type));
  }
  
  public function total_usage() {
    // Return the user's total usage in bytes.
    return ($this->fullsize + $this->resize + $this->thumb);
  }

  public function total_usage_string() {
    // Return the user's total usage as a string with the appropriate file size prefix.
    return (Users_Space_Usage_Model_Core::_format_usage($this->total_usage()));
  }

  public function current_usage() {
    // Return the users relevant usage in bytes based on the use_all_sizes setting.
    if (module::get_var("quotas", "use_all_sizes") == true) {
      return $this->total_usage();
    } else {
      return $this->fullsize;
    }
  }

  public function current_usage_string() {
    // Return the users relevant usage as a string with the appropriate file size prefix
    //   based on the use_all_sizes setting.
    return (Users_Space_Usage_Model_Core::_format_usage($this->current_usage()));
  }

  public function get_usage_limit() {
    // Returns a user's maximum limit in bytes.
    $user_groups = ORM::factory("group")
                   ->join("groups_users", "groups_users.group_id", "groups.id")
                   ->join("groups_quotas", "groups_quotas.group_id", "groups.id")
                   ->select("groups.id")
                   ->select("groups_quotas.storage_limit")
                   ->where("groups_users.user_id", "=", $this->owner_id)
                   ->order_by("groups_quotas.storage_limit", "DESC")
                   ->find_all(1);
    if (!empty($user_groups)) {
      if ($user_groups[0]->storage_limit <= "0") {
        return 0;
      } else {
        return $user_groups[0]->storage_limit;
      }
    }
    return 0;
  }

  public function get_usage_limit_string() {
    // Returns a user's maximum limit as a string with the appropriate file size prefix
    //  or an infinity symbol if the user has no limit.
    $user_limit = $this->get_usage_limit();
    if ($user_limit == 0) {
      return "&infin;";
    } else {
      return (Users_Space_Usage_Model_Core::_format_usage($this->get_usage_limit()));
    }
  }

  public function add_item($item) {
    // Adds an item's file size to the table.
    if ($item->is_album()) {
      return ;
    }

    $item_fullsize = 0;
    $item_resize = 0;
    $item_thumb = 0;

    if (file_exists($item->file_path())) {
      $item_fullsize = filesize($item->file_path());
    }
    if (file_exists($item->thumb_path())) {
      $item_thumb = filesize($item->thumb_path());
    }
    if (file_exists($item->resize_path())) {
      $item_resize = filesize($item->resize_path());
    }

    $this->fullsize = $this->fullsize + $item_fullsize;
    $this->resize = $this->resize + $item_resize;
    $this->thumb = $this->thumb + $item_thumb;
    $this->save();

    return ;
  }

  public function remove_item($item) {
    // Removes an item's file size from the table.
    if ($item->is_album()) {
      return ;
    }

    $item_fullsize = 0;
    $item_resize = 0;
    $item_thumb = 0;

    if (file_exists($item->file_path())) {
      $item_fullsize = filesize($item->file_path());
    }
    if (file_exists($item->thumb_path())) {
      $item_thumb = filesize($item->thumb_path());
    }
    if (file_exists($item->resize_path())) {
      $item_resize = filesize($item->resize_path());
    }

    $this->fullsize = $this->fullsize - $item_fullsize;
    $this->resize = $this->resize - $item_resize;
    $this->thumb = $this->thumb - $item_thumb;
    $this->save();

    return ;
  }
}
