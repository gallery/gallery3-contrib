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
class Captionator_Controller extends Controller {
  function dialog($album_id) {
    $album = ORM::factory("item", $album_id);
    access::required("view", $album);

    if (!access::can("edit", $album)) {
      // The user can't edit; perhaps they just logged out?
      url::redirect($album->abs_url());
    }

    $v = new Theme_View("page.html", "collection", "captionator");
    $v->content = new View("captionator_dialog.html");
    $v->content->album = $album;
    $v->content->enable_tags = module::is_active("tag");
    if ($v->content->enable_tags) {
      $v->content->tags = array();
      foreach ($album->viewable()->children() as $child) {
        $item = ORM::factory("item", $child->id);
        $tag_names = array();
        foreach (tag::item_tags($item) as $tag) {
          $tag_names[] = $tag->name;
        }
        $v->content->tags[$child->id] = implode(", ", $tag_names);
      }
    }
    print $v;
  }

  function save($album_id) {
    access::verify_csrf();

    $album = ORM::factory("item", $album_id);
    access::required("edit", $album);

    if (Input::instance()->post("save")) {
      $titles = Input::instance()->post("title");
      $descriptions = Input::instance()->post("description");
      $filenames = Input::instance()->post("filename");
      $internetaddresses = Input::instance()->post("internetaddress");
      $tags = Input::instance()->post("tags");
      $enable_tags = module::is_active("tag");
      foreach (array_keys($titles) as $id) {
        $item = ORM::factory("item", $id);
        if ($item->loaded() && access::can("edit", $item)) {
          $item->title = $titles[$id];
          $item->description = $descriptions[$id];
          $item->name = $filenames[$id];
          $item->slug = $internetaddresses[$id];
          $item->save();
          if ($enable_tags) {
            tag::clear_all($item);
            foreach (explode(",", $tags[$id]) as $tag_name) {
              if ($tag_name) {
                tag::add($item, trim($tag_name));
              }
            }
            tag::compact();
          }
        }
      }
      message::success(t("Captions saved"));
    }
    url::redirect($album->abs_url());
  }
}
