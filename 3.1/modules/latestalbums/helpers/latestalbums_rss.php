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

class latestalbums_rss_Core {
  static function available_feeds($item, $tag) {
    $feeds["latestalbums/latest"] = t("Latest albums");
    return $feeds;
  }

  static function feed($feed_id, $offset, $limit, $id) {
    switch ($feed_id) {
    case "latest":
      $feed = new stdClass();
      $feed->items = ORM::factory("item")
        ->viewable()
        ->where("type", "=", "album")
        ->order_by("created", "DESC")
        ->find_all($limit, $offset);

      $all_items = ORM::factory("item")
        ->viewable()
        ->where("type", "=", "album")
        ->order_by("created", "DESC");

      $feed->max_pages = ceil($all_items->find_all()->count() / $limit);
      $feed->title = t("Latest albums");
      $feed->description = t("Most recently created albums");
      return $feed;
    }
  }
}
