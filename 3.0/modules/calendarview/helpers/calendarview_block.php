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
class calendarview_block_Core {
  static function get_site_list() {
    return array("calendarview_photo" => t("More Photos From This Date"));
  }

  static function get($block_id, $theme) {
    $block = "";

    // Make sure the current page belongs to an item.
    if (!$theme->item()) {
      return;
    }
    $item = $theme->item;

    $display_date = "";
    if (isset($item->captured)) {
      $display_date = $item->captured;
    }elseif (isset($item->created)) {
      $display_date = $item->created;
    }

    // Make sure there are photo's to display.
    $day_count = ORM::factory("item")
                 ->viewable()
                 ->where("type", "!=", "album")
                 ->where("captured", ">=", mktime(0, 0, 0, date("n", $display_date), date("j", $display_date), date("Y", $display_date)))
                 ->where("captured", "<", mktime(0, 0, 0, date("n", $display_date), date("j", $display_date)+1, date("Y", $display_date)))
                 ->find_all()
                 ->count();
    $month_count = ORM::factory("item")
                   ->viewable()
                   ->where("type", "!=", "album")
                   ->where("captured", ">=", mktime(0, 0, 0, date("n", $display_date), 1, date("Y", $display_date)))
                   ->where("captured", "<", mktime(0, 0, 0, date("n", $display_date)+1, 1, date("Y", $display_date)))
                   ->find_all()
                   ->count();

    switch ($block_id) {
    case "calendarview_photo":
      if ( ($display_date != "") && (($day_count > 0) || ($month_count > 0)) ) {
        $block = new Block();
        $block->css_id = "g-calendarview-sidebar";
        $block->title = t("Calendar");
        $block->content = new View("calendarview_sidebar.html");
        $block->content->date = $display_date;
        $block->content->day_count = $day_count;
        $block->content->month_count = $month_count;
      }
      break;
    }
    return $block;
  }
}
