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
class Pear_Controller extends Controller {
  public function show_comments($id) {
    $item = ORM::factory("item", $id);
    access::required("view", $item);

    if(module::is_active("facebook_comment")) {
      $v = new Theme_View("facebook_comment.html", "other", "comment-fragment");
      $v->url = $item->abs_url();
      $v->title = $item->title;
      print $v;
    } else {
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
  public function about($id){
    $item = ORM::factory("item", $id);
    access::required("view", $item);
    $v = new Theme_View("about.html","","");
    $v->item = $item;
    $details = array(array("caption" => "Title", "value" => $item->title));
    if ( $item->description != $item->title) {
      array_push($details, array("caption" => "Description", "value" => $item->description) );
    }
    if ( isset($item->captured )) {
      array_push($details, array("caption" => "Captured", "value" => date(module::get_var("gallery", "date_time_format", "Y-M-d H:i:s"), $item->captured)));
    }
    array_push($details, array("caption" => "Owner", "value" => $item->owner_id));
    array_push($details, array("caption" => "Filename", "value" => $item->name));
    array_push($details, array("caption" => "View count", "value" => $item->view_count));

    $v->details = $details;

    if (module::is_active("tag")) {
      $v->tags= tag::item_tags($item);
    }

    print $v;
  }
  public function download($id){
    $item = ORM::factory("item", $id);

    // Make sure we have access to the item
    if (!access::can("view", $item)) {
      throw new Kohana_404_Exception();
    }

    // Make sure we have view_full access to the original
    if (!access::can("view_full", $item)) {
      throw new Kohana_404_Exception();
    }

    // Don't try to load a directory
    if ($item->is_album()) {
      throw new Kohana_404_Exception();
    }

    $file = $item->file_path();

    if (!file_exists($file)) {
      throw new Kohana_404_Exception();
    }

    header("Content-Length: " . filesize($file));
    header("Pragma: public");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=\"$item->name\"");

    Kohana::close_buffers(false);
    readfile($file);
  }
}
