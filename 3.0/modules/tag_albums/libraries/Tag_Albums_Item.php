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
class Tag_Albums_Item_Core {
  public $title = "";
  public $id = -1;
  public $item_id = 0;
  public $url = "#";
  public $thumb_url = "";
  public $thumb_width = 0;
  public $thumb_height = 0;
  public $item_type = "";
  public $type = "";
  public $view_count = 0;
  public $owner;

  public function is_album() {
    if ($this->item_type == "album") {
      return true;
    } else {
      return false;
    }
  }

  public function has_thumb() {
    if ($this->thumb_url != "") {
      return true;
    } else {
      return false;
    }
  }

  public function full_or_resize_url() {
    if ($this->item_id > 0) {
      $item = ORM::factory("item", $this->item_id);
      if (access::can("view_full", $item)) {
        return $item->file_url();
      } else {
        return $item->resize_url();
      }
    } else {
      return "";
    }
  }

  public function thumb_img($extra_attrs=array(), $max=null, $center_vertically=false) {
    list ($height, $width) = $this->scale_dimensions($max);
    if ($center_vertically && $max) {
      // The constant is divide by 2 to calculate the file and 10 to convert to em
      $margin_top = (int)(($max - $height) / 20);
      $extra_attrs["style"] = "margin-top: {$margin_top}em";
      $extra_attrs["title"] = $this->title;
    }
    $attrs = array_merge($extra_attrs,
            array(
              "src" => $this->thumb_url(),
              "alt" => $this->title,
              "width" => $width,
              "height" => $height)
            );
    // html::image forces an absolute url which we don't want
    return "<img" . html::attributes($attrs) . "/>";
  }

  public function scale_dimensions($max) {
    $width = $this->thumb_width;
    $height = $this->thumb_height;

    if ($width <= $max && $height <= $max) {
        return array($height, $width);
    }

    if ($height) {
      if (isset($max)) {
        if ($width > $height) {
          $height = (int)($max * $height / $width);
          $width = $max;
        } else {
          $width = (int)($max * $width / $height);
          $height = $max;
        }
      }
    } else {
      // Missing thumbnail, can happen on albums with no photos yet.
      // @todo we should enforce a placeholder for those albums.
      $width = 0;
      $height = 0;
    }
    return array($height, $width);
  }

  public function thumb_url() {
    return $this->thumb_url;
  }

  public function url() {
    return $this->url;
  }

  public function set_thumb($new_url, $new_width, $new_height) {
    $this->thumb_url = $new_url;
    $this->thumb_width = $new_width;
    $this->thumb_height = $new_height;
  }

  public function __construct($new_title, $new_url, $new_type, $new_id) {
    $this->title = $new_title;
    $this->url = $new_url;
    $this->item_type = $new_type;
    $this->type = $new_type;
    $this->item_id = $new_id;
  }
}
