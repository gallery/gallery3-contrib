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
class exif_gps_event_Core {
  static function pre_deactivate($data) {
    // If the admin is about to deactivate EXIF, warn them that this module requires it.
    if ($data->module == "exif") {
      $data->messages["warn"][] = t("The EXIF_GPS module requires the EXIF module.");
    }
  }

  static function module_change($changes) {
    // If EXIF is deactivated, display a warning that it is required for this module to function properly.
    if (!module::is_active("exif") || in_array("exif", $changes->deactivate)) {
      site_status::warning(
        t("The EXIF_GPS module requires the EXIF module.  " .
          "<a href=\"%url\">Activate the EXIF module now</a>",
          array("url" => html::mark_clean(url::site("admin/modules")))),
        "exif_gps_needs_exif");
    } else {
      site_status::clear("exif_gps_needs_exif");
    }
  }

  static function item_created($item) {
    // Whenever a new non-album item is created, check it for GPS coordinates.
    if (!$item->is_album()) {
      exif_gps::extract($item);
    }
  }

  static function item_deleted($item) {
    // Whenever an item is deleted, delete any corresponding GPS coordinates.
    db::build()
      ->delete("exif_coordinates")
      ->where("item_id", "=", $item->id)
      ->execute();
  }

  static function item_edit_form($item, $form) {
    // Allow users to set / edit the GPS coordinates associated with the current item.
    $record = ORM::factory("exif_coordinate")->where("item_id", "=", $item->id)->find();
    $gpsdata = $form->edit_item->group("gps_data")->label("GPS Data");
    if ($record->loaded()) {
      $gpsdata->input("latitude")->label(t("Latitude"))
           ->value($record->latitude);
      $gpsdata->input("longitude")->label(t("Longitude"))
           ->value($record->longitude);
    } else {
      $gpsdata->input("latitude")->label(t("Latitude"));
      $gpsdata->input("longitude")->label(t("Longitude"));
    }
  }

  static function item_edit_form_completed($item, $form) {
    // Update the db records with the user-specified coordinates.

    // Require a set of coordinates (both latitude and longitude).
    //   If one or both fields are blank, completely delete any coordinates associated with this item.
    if (($form->edit_item->gps_data->latitude->value == "") || ($form->edit_item->gps_data->longitude->value == "")) {
      db::build()
        ->delete("exif_coordinates")
        ->where("item_id", "=", $item->id)
        ->execute();	
    } else {
      $record = ORM::factory("exif_coordinate")->where("item_id", "=", $item->id)->find();
      if (!$record->loaded()) {
        $record->item_id = $item->id;
      }
      $record->latitude = $form->edit_item->gps_data->latitude->value;
      $record->longitude = $form->edit_item->gps_data->longitude->value;
      $record->save();
    }
  }

  static function admin_menu($menu, $theme) {
    // Add a link to the EXIF_GPS admin page to the Settings menu.
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("exif_gps")
               ->label(t("EXIF_GPS Settings"))
               ->url(url::site("admin/exif_gps")));
  }

  static function photo_menu($menu, $theme) {
    $album_id = "";
    $item = $theme->item;
    if ($item->is_album()) {
      $album_id = $item->id;
    } else {
      $album_id = $item->parent_id;
    }
    $curr_user = ORM::factory("user")->where("id", "=", $item->owner_id)->find_all();
    $user_name = $curr_user[0]->full_name;

    // Make sure there are actually map-able items to display.
    $album_items_count = ORM::factory("item", $album_id)
      ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
      ->viewable()
      ->order_by("exif_coordinates.latitude", "ASC")
      ->descendants_count();
    $user_items_count = ORM::factory("item")
      ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
      ->where("items.owner_id", "=", $item->owner_id)
      ->viewable()
      ->order_by("exif_coordinates.latitude", "ASC")
      ->count_all();

    if (($album_items_count > 0) && (module::get_var("exif_gps", "toolbar_map_album") == true)) {
      $menu->append(Menu::factory("link")
           ->id("exif_gps_album")
           ->label(t("Map this album"))
           ->url(url::site("exif_gps/map/album/" . $album_id))
           ->css_id("g-exif-gps-album-link"));
    }
    if (($user_items_count > 0) && (module::get_var("exif_gps", "toolbar_map_user") == true)) {
      $menu->append(Menu::factory("link")
           ->id("exif_gps_user")
           ->label(t("Map ") . $user_name . t("'s photos"))
           ->url(url::site("exif_gps/map/user/" . $item->owner_id))
           ->css_id("g-exif-gps-user-link"));
    }
  }

  static function movie_menu($menu, $theme) {
    $album_id = "";
    $item = $theme->item;
    if ($item->is_album()) {
      $album_id = $item->id;
    } else {
      $album_id = $item->parent_id;
    }
    $curr_user = ORM::factory("user")->where("id", "=", $item->owner_id)->find_all();
    $user_name = $curr_user[0]->full_name;

    // Make sure there are actually map-able items to display.
    $album_items_count = ORM::factory("item", $album_id)
      ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
      ->viewable()
      ->order_by("exif_coordinates.latitude", "ASC")
      ->descendants_count();
    $user_items_count = ORM::factory("item")
      ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
      ->where("items.owner_id", "=", $item->owner_id)
      ->viewable()
      ->order_by("exif_coordinates.latitude", "ASC")
      ->count_all();

    if (($album_items_count > 0) && (module::get_var("exif_gps", "toolbar_map_album") == true)) {
      $menu->append(Menu::factory("link")
           ->id("exif_gps_album")
           ->label(t("Map this album"))
           ->url(url::site("exif_gps/map/album/" . $album_id))
           ->css_id("g-exif-gps-album-link"));
    }
    if (($user_items_count > 0) && (module::get_var("exif_gps", "toolbar_map_user") == true)) {
      $menu->append(Menu::factory("link")
           ->id("exif_gps_user")
           ->label(t("Map ") . $user_name . t("'s photos"))
           ->url(url::site("exif_gps/map/user/" . $item->owner_id))
           ->css_id("g-exif-gps-user-link"));
    }
  }
  
  static function album_menu($menu, $theme) {
    $album_id = "";
    $item = $theme->item;
    if ($item->is_album()) {
      $album_id = $item->id;
    } else {
      $album_id = $item->parent_id;
    }
    $curr_user = ORM::factory("user")->where("id", "=", $item->owner_id)->find_all();
    $user_name = $curr_user[0]->full_name;

    // Make sure there are actually map-able items to display.
    $album_items_count = ORM::factory("item", $album_id)
      ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
      ->viewable()
      ->order_by("exif_coordinates.latitude", "ASC")
      ->descendants_count();
    $user_items_count = ORM::factory("item")
      ->join("exif_coordinates", "items.id", "exif_coordinates.item_id")
      ->where("items.owner_id", "=", $item->owner_id)
      ->viewable()
      ->order_by("exif_coordinates.latitude", "ASC")
      ->count_all();

    if (($album_items_count > 0) && (module::get_var("exif_gps", "toolbar_map_album") == true)) {
      $menu->append(Menu::factory("link")
           ->id("exif_gps_album")
           ->label(t("Map this album"))
           ->url(url::site("exif_gps/map/album/" . $album_id))
           ->css_id("g-exif-gps-album-link"));
    }
    if (($user_items_count > 0) && (module::get_var("exif_gps", "toolbar_map_user") == true)) {
      $menu->append(Menu::factory("link")
           ->id("exif_gps_user")
           ->label(t("Map ") . $user_name . t("'s photos"))
           ->url(url::site("exif_gps/map/user/" . $item->owner_id))
           ->css_id("g-exif-gps-user-link"));
    }
  }
}
