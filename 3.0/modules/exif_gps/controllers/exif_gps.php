<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
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
  public function map($map_type, $type_id) {
    // Map all items in the specified album or user.

    $map_title = "";
    if ($map_type == "album") {
      // Generate an array of all items in the current album that have exif gps 
      //   coordinates and order by latitude (to group items w/ the same
      //   coordinates together).
      $items = ORM::factory("item", $type_id)
               ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
               ->viewable()
               ->order_by("exif_coordinates.latitude", "ASC")
               ->descendants();
      $curr_album = ORM::factory("item")->where("id", "=", $type_id)->find_all();
      $map_title = $curr_album[0]->title;
    } elseif ($map_type == "user") {
      // Generate an array of all items uploaded by the current user that 
      //   have exif gps coordinates and order by latitude (to group items 
      //   w/ the same coordinates together).
      $items = ORM::factory("item")
               ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
               ->where("items.owner_id", "=", $type_id)
               ->viewable()
               ->order_by("exif_coordinates.latitude", "ASC")
               ->find_all();
      $curr_user = ORM::factory("user")->where("id", "=", $type_id)->find_all();
      $map_title = $curr_user[0]->full_name . "'s " . t("Photos");
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
    if ($int_map_type == 0) $map_type = "ROADMAP";
    if ($int_map_type == 1) $map_type = "SATELLITE";
    if ($int_map_type == 2) $map_type = "HYBRID";
    if ($int_map_type == 3) $map_type = "TERRAIN";
    $template->content->map_type = $map_type;

    // Load in module preferences.
    $template->content->items = $items;
    $template->content->google_map_key = module::get_var("exif_gps", "googlemap_api_key");

    // Display the page.
    print $template;
  }
}
