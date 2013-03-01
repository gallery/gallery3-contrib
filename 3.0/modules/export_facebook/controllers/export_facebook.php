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
class export_facebook_Controller extends Controller {
  public function export_fb($a) {
    if ($_GET['a'] == "albums") {
      // Generate an array of albums in the items table,
      //   skip id=1 as its the root album.
      $albums = ORM::factory("item")
        ->where("type", "=", "album")
        ->where("id", "!=", "1")
        ->viewable()
        ->find_all();

      // Loop through each album and output the necessary information.
      foreach ($albums as $album) {
        $album_contents = ORM::factory("item")
          ->where("parent_id", "=", $album->id)
          ->where("type", "=", "photo")
          ->viewable()
          ->find_all();

        print ($album->level-2) . "\t" . $album->id . "\t" . $album->name . "\t" . count($album_contents) . "\n";
      }

    } else if ($_GET['a'] == "photos") {
      // Generate an array of photo's in the specified album.
      $photos = ORM::factory("item")
        ->where("type", "=", "photo")
        ->where("parent_id", "=", $_GET['id'])
        ->viewable()
        ->find_all();

      // Loop through each photo, generate a list of tags (if available) and then output the necessary information.
      foreach ($photos as $photo) {
        $photo_keywords = "";
        if (module::is_active("tag")) {
          $photo_tags = ORM::factory("tag")
            ->join("items_tags", "tags.id", "items_tags.tag_id")
            ->where("items_tags.item_id", "=", $photo->id)
            ->find_all();
          foreach ($photo_tags as $tag) {
            $photo_keywords = $photo_keywords . $tag->name . ", ";
          }
          // Cut off the ", " from the end of the string.
          if ($photo_keywords != "") {
            $photo_keywords = substr($photo_keywords, 0, -2);
          }
        }
        print $photo->id . "\t" . $photo->title . "\t" . stristr($photo->resize_url(false),"/var/") . "\t" . stristr($photo->thumb_url(false), "/var/") . "\t\t" . $photo->description . "\t" . $photo_keywords . "\n";
      }
    }
  }
}