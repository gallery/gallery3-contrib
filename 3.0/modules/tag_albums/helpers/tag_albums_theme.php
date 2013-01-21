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
class tag_albums_theme_Core {
  static function head($theme) {
    // If the current page is an item, and if it's in the tags_album_id table,
    //   then redirect to the tag_albums page.
    if ($theme->item()) {
      $album_tags = ORM::factory("tags_album_id")
        ->where("album_id", "=", $theme->item->id)
        ->find_all();
      if (count($album_tags) > 0) {
        url::redirect(url::abs_site("tag_albums/album/" . $album_tags[0]->id . "/" . urlencode($theme->item->name)));
      }
    }
    return;
  }

  static function dynamic_top($theme) {
    // If this page is the "all tags" dynamic page, display filter link text.
    if (isset($theme->content->filter_text) && module::get_var("tag_albums", "tag_index_filter_top", "0")) {
      $view = new View("tag_albums_filter.html");
      $view->filter_text = $theme->content->filter_text;
      return $view;
    }
  }

  static function dynamic_bottom($theme) {
    // If this page is the "all tags" dynamic page, display filter link text.
    if (isset($theme->content->filter_text) && module::get_var("tag_albums", "tag_index_filter_bottom", "0")) {
      $view = new View("tag_albums_filter.html");
      $view->filter_text = $theme->content->filter_text;
      return $view;
    }
  }
}
