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
class Captionator_Controller extends Controller {
  function dialog($album_id) {
    $album = ORM::factory("item", $album_id);
    access::required("view", $album);
    access::required("edit", $album);

    $v = new Theme_View("page.html", "collection", "captionator");
    $v->content = new View("captionator_dialog.html");
    $v->content->album = $album;
    print $v;
  }

  function save($album_id) {
    access::verify_csrf();

    $album = ORM::factory("item", $album_id);
    access::required("edit", $album);

    if (Input::instance()->post("save")) {
      $titles = Input::instance()->post("title");
      $descriptions = Input::instance()->post("description");
      foreach (array_keys($titles) as $id) {
        $item = ORM::factory("item", $id);
        if ($item->loaded() && access::can("edit", $item)) {
          $item->title = $titles[$id];
          $item->description = $descriptions[$id];
          $item->save();
        }
      }
      message::success(t("Captions saved"));
    }
    url::redirect($album->parent()->abs_url());
  }
}
