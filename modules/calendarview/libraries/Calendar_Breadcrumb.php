<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class Calendar_Breadcrumb_Core {
  public $title = "the_title";
  public $id = 0;
  //public $crumb_url = "asdf";
  public $url = "asdf";
  public $crumb_parent = null;

  public function __construct($new_title, $new_url) {
    $this->title = $new_title;
    $this->url = $new_url;
    //$this->crumb_url = $new_url;
  }

  public function url($query=null) {
    //return $crumb_url;
    //print $this->url;
    //return "http://the.url/";
    return $this->url;
  }

  public function parent() {
    return $crumb_parent;
  }

  public function is_album() {
    return false;
  }

  public function is_photo() {
    return false;
  }
}
