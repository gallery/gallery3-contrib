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

class Dynamic_Display_Context_Core extends Display_Context {
  protected function __construct() {
    parent::__construct("dynamic");
  }

  function display_context($item) {
    $dynamic_type_definition = $this->get("dynamic_type");

    $position = dynamic::get_position($dynamic_type_definition, $item);
    if ($position > 1) {
      list ($previous_item, $ignore, $next_item) = dynamic::items($dynamic_type_definition->key_field, 3, $position - 2);
    } else {
      $previous_item = null;
      list ($next_item) = dynamic::items($dynamic_type_definition->key_field, 1, $position);
    }

    $root = item::root();
    return array("position" =>$position,
                 "previous_item" => $previous_item,
                 "next_item" =>$next_item,
                 "sibling_count" => dynamic::get_display_count($dynamic_type_definition),
                 "breadcrumbs" => array(
                   Breadcrumb::instance($root->title, $root->url())->set_first(),
                   Breadcrumb::instance($dynamic_type_definition->title, $this->_url("show={$item->id}")),
                   Breadcrumb::instance($item->title, $item->url())->set_last()));
  }

  private function _url($query=null) {
    $albumPath = $this->get("path");
    $url = url::site("dynamic/$albumPath");
    if ($query) {
      $url .= "?$query";
    }
    return $url;
  }
}
