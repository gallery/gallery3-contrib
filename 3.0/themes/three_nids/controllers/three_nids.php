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
class Three_Nids_Controller extends Controller {
  public function show_comments($id) {
    $item = ORM::factory("item", $id);
    access::required("view", $item);

    $comments = ORM::factory("comment")
      ->where("item_id", "=", $item->id)
      ->where("state", "=", "published")
      ->order_by("created", "ASC")
      ->find_all();

    $v = new Theme_View("comments.html", "other", "comment-fragment");
    $v->comments = $comments;
    $v->item = $item;
    print $v;
  }
}
