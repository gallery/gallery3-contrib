<?php defined("SYSPATH") or die("No direct script access.");
/**
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
class BatchTag_Controller extends Controller {
  public function tagitems() {
    // Tag all non-album items in the current album with the specified tags.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    $input = Input::instance();

    // Figure out if the contents of sub-albums should also be tagged
    $str_tag_subitems = $input->post("tag_subitems");

    $children = "";
    if ($str_tag_subitems == false) {
      // Generate an array of all non-album items in the current album.
      $children = ORM::factory("item")
        ->where("parent_id", "=", $input->post("item_id"))
        ->where("type", "!=", "album")
        ->find_all();
    } else {
      // Generate an array of all non-album items in the current album
      //   and any sub albums.
      $item = ORM::factory("item", $input->post("item_id"));
      $children = $item->descendants();
    }
    // Loop through each item in the album and make sure the user has
    //   access to view and edit it.
    foreach ($children as $child) {
      if (access::can("view", $child) && access::can("edit", $child) && !$child->is_album()) {

        // Assuming the user can view/edit the current item, loop
        //   through each tag that was submitted and apply it to
        //   the current item.
        foreach (explode(",", $input->post("name")) as $tag_name) {
          $tag_name = trim($tag_name);
          if ($tag_name) {
            tag::add($child, $tag_name);
          }
        }
      }
    }

    // Redirect back to the album.
    $item = ORM::factory("item", $input->post("item_id"));
    url::redirect(url::abs_site("{$item->type}s/{$item->id}"));
  }
}
