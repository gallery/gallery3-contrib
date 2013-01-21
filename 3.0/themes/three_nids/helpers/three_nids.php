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

class three_nids_Core {
  static function fancylink($item, $view_type="album", $group_img = true,
                            $display_comment = true, $parent_title_class = "h2") {
    // view_type = album || dynamic || header
    $link = "";
    access::required("view", $item);

    $photo_size = module::get_var("three_nids", "photo_size");
    if ($photo_size == "full" || $item->is_movie()){
      $width = $item->width;
      $height = $item->height;
    }else{
      $width = $item->resize_width;
      $height = $item->resize_height;
    }

    $description_mode = module::get_var("three_nids", "description");
    $description = "";
    $tags = tag::item_tags($item);
    if(count($tags) && $description_mode == "tags"){
      $description = " || " . implode(", ", $tags);
    } else if ($description_mode == "item" && $item->description != ""){
      $description = " || " . str_replace("\"","&quot;",$item->description);
    } else if (($description_mode == "parent" ||
                $description_mode == "item") && $item->parent()->description != ""){
      $description = " || " . str_replace("\"", "&quot;", $item->parent()->description);
    }

    $title_mode = module::get_var("three_nids", "title");
    if ($title_mode == "parent"){
      $title = html::clean($item->parent()->title);
    } else {
      $title = html::clean($item->title);
    }

    $rel = "";
    if ($group_img == true) {
      $rel = " rel=\"fancygroup\" ";
    }

    if ($item->is_photo() || $item->is_movie()){
      $fancymodule = "";
      if (module::is_active("exif")) {
        $fancymodule .= "exif::" . url::site("exif/show/{$item->id}") . ";;";
      }
      if (module::is_active("comment")) {
        $fancymodule .= "comment::" . url::site("three_nids/show_comments/{$item->id}") .
          ";;comment_count::" . three_nids::comment_count($item) . ";;";
      }
      if ($item->is_photo()){
        $link .= "<a href=\"" . url::site("photos/{$item->id}") ."/?w=" . $width .
          "xewx&h=" . $height . "xehx\" " . $rel . " class=\"fancyclass iframe\" title=\"" .
          $title . $description ."\" name=\"" . $fancymodule . " \">";
      } else {
        $link .= "<a href=\"" . url::site("movies/{$item->id}") . "/?w=" .
          strval(20 + $width) . "xewx&h=" . strval(50 + $height) . "xehx\" " . $rel .
          " class=\"fancyclass iframe\" title=\"" . $item->parent()->title . $description .
          "\" name=\"" . $fancymodule . " \">";
      }
    } else if ($item->is_album() && $view_type != "header") {
      $link .= "<a href=\"" . $item->url() . "\">";
    } else {
      // NOTE: we don't want to open an <a> here because $view_type is "header", but lower down
      // we're going to close one, so that's going to generate a mismatch.  For now, just open a
      // link anyway.
      // @todo: figure out what we really should be doing here.
      $link .= "<a href=\"" . $item->url() . "\">";
    }

    if ($view_type != "header") {
      $link .= $item->thumb_img(array("class" => "g-thumbnail")) . "</a>";
      if ($item->is_album() && $view_type == "album") {
        $link .= "<a href=\"" . $item->url() . "?show=" . $item->id .
          "\"><$parent_title_class><span></span>" . html::clean($item->title) .
          "</$parent_title_class></a>";
      } else if (!($item->is_album()) && $view_type == "dynamic") {
        $link .= "<a href=\"" . $item->parent()->url() . "?show=" . $item->id .
          "\" class=\"g-parent-album\"><$parent_title_class><span></span>" .
          html::clean($item->parent()->title) . "</$parent_title_class></a>";
      }

      if (($item->is_photo() || $item->is_movie()) && $display_comment &&
          module::is_active("comment")) {
        $link .= "<ul class=\"g-metadata\"><li><a href=\"" .
          url::site("three_nids/show_comments/{$item->id}") .
          "\" class=\"iframe fancyclass g-hidden\">" . three_nids::comment_count($item) .
          " " . t("comments") . "</a></li></ul>";
      }
    } else {
      $link .= "</a>";
    }
    return $link;
  }

  static function comment_count($item) {
    access::required("view", $item);

    return ORM::factory("comment")
      ->where("item_id", "=", $item->id)
      ->where("state", "=", "published")
      ->order_by("created", "DESC")
      ->count_all();
  }
}
?>