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
class EXIF_GPS_Controller extends Controller {
  public static $xml_records_limit = 1000;

  public function item($item_id) {
    // Make sure the context callback is set to album when linking to photos from map pages.
    $item = ORM::factory("item", $item_id);
    access::required("view", $item);
    item::set_display_context_callback("Albums_Controller::get_display_context");
    url::redirect($item->abs_url());
  }

  public function xml($query_type, $query_id, $offset) {
    // Generate an xml output of the photos to be mapped.
    // $query_type can be either "album" or "user", $query_id is the id# of the album or user to map.

    // If the user can't view maps, don't let them view the xml.
    if ((module::get_var("exif_gps", "restrict_maps") == true) && (identity::active_user()->guest)) {
      throw new Kohana_404_Exception();
    }

    $items = "";
    if ($query_type == "user") {
      $items = ORM::factory("item")
               ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
               ->where("items.owner_id", "=", $query_id)
               ->viewable()
               ->order_by("exif_coordinates.latitude", "ASC")
               ->find_all(EXIF_GPS_Controller::$xml_records_limit, $offset);
    } elseif ($query_type == "album") {
      $items = ORM::factory("item", $query_id)
               ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
               ->viewable()
               ->order_by("exif_coordinates.latitude", "ASC")
               ->descendants(EXIF_GPS_Controller::$xml_records_limit, $offset);
    }

    $v = new View("exif_gps_coordinates_xml.html");
    $v->items = $items;
    header("Content-type: text/xml; charset=utf-8");
    print $v;
  }

  public function map($map_type, $type_id) {
    // Map all items in the specified album or user.
    // Valid values for $map_type are "album" or "user", $type_id is either an 
    //   album id# or a user id#.

    // If the user can't view maps, throw a 404 error.
    if ((module::get_var("exif_gps", "restrict_maps") == true) && (identity::active_user()->guest)) {
      throw new Kohana_404_Exception();
    }

    // Figure out what to display for the page title and how many items to display.
    $map_title = "";
    $items_count = 0;
    if ($map_type == "album") {
      $curr_album = ORM::factory("item")->where("id", "=", $type_id)->find_all();
      $map_title = $curr_album[0]->title;
      $items_count = ORM::factory("item", $type_id)
               ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
               ->viewable()
               ->order_by("exif_coordinates.latitude", "ASC")
               ->descendants_count();
    } elseif ($map_type == "user") {
      $curr_user = ORM::factory("user")->where("id", "=", $type_id)->find_all();
      $map_title = $curr_user[0]->full_name . "'s " . t("Photos");
      $items_count = ORM::factory("item")
               ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
               ->where("items.owner_id", "=", $type_id)
               ->viewable()
               ->order_by("exif_coordinates.latitude", "ASC")
               ->count_all();
    }

    // Set up breadcrumbs.
    $breadcrumbs = array();
    if ($map_type == "album") {
      $counter = 0;
      $breadcrumbs[] = Breadcrumb::instance(t("Map"), url::site("exif_gps/map/album/{$type_id}"))->set_last();
      $parent_item = ORM::factory("item", $type_id);
      while ($parent_item->id != 1) {
        $breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url());
        $parent_item = ORM::factory("item", $parent_item->parent_id);
      }
      $breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url())->set_first();
      $breadcrumbs = array_reverse($breadcrumbs, true);
    } else {
      $root = item::root();
      $breadcrumbs[] = Breadcrumb::instance($root->title, $root->url())->set_first();
      $breadcrumbs[] = Breadcrumb::instance(t("Photo Map"), url::site("exif_gps/map/{$map_type}/{$type_id}"))->set_last();
    }

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "other", "EXIF_GPS_MAP");
    $template->page_title = t("Gallery :: Map");
    $template->set_global(array("breadcrumbs" => $breadcrumbs));
    $template->content = new View("exif_gps_map.html");
    if ($map_title == "") {
      $template->content->title = t("Map");
    } else {
      $template->content->title = t("Map of") . " " . $map_title;
    }

    // Figure out default map type.
    $int_map_type = module::get_var("exif_gps", "largemap_maptype");
    if ($int_map_type == 0) $map_display_type = "ROADMAP";
    if ($int_map_type == 1) $map_display_type = "SATELLITE";
    if ($int_map_type == 2) $map_display_type = "HYBRID";
    if ($int_map_type == 3) $map_display_type = "TERRAIN";
    $template->content->map_type = $map_display_type;

    // These are used to set up the URL to the xml file.
    $template->content->query_type = $map_type;
    $template->content->query_id = $type_id;
    $template->content->items_count = $items_count;

    // Load in module preferences.
    $template->content->google_map_key = module::get_var("exif_gps", "googlemap_api_key");

    // Display the page.
    print $template;
  }
}
