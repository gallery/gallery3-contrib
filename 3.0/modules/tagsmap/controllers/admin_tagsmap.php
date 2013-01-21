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
class Admin_TagsMap_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tagsmap.html");

    // Generate a form for Google Maps Settings.
    $view->content->googlemaps_form = $this->_get_googlemaps_form();

    // Generate a list of tags to display.
    $query = ORM::factory("tag");
    $view->content->tags = $query->order_by("name", "ASC")->find_all();

    // Display the page.
    print $view;
  }

  public function edit_gps($tag_id) {
    // Generate a new admin page to edit gps data for the tag specified by $tag_id.

    // Determine the name of the tag.
    $tagName = ORM::factory("tag")
      ->where("id", "=", $tag_id)
      ->find_all();

    // Set up the admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tagsmap_edit.html");
    $view->content->tagsmapedit_form = $this->_get_tagsgpsedit_form($tag_id);
    $view->content->tag_name = $tagName[0]->name;
    $view->content->zoom = module::get_var("tagsmap", "googlemap_zoom");
    print $view;
  }

  public function orphaned_tags() {
    // Locate and delete any orphaned GPS data.
    $int_deleted_records = 0;

    // Generate a list of all tags with GPS data.
    $existingGPS = ORM::factory("tags_gps")
      ->find_all();

    // Loop through each record and see if a corresponding tag exists.
    foreach ($existingGPS as $oneGPS) {
      $oneTag = ORM::factory("tag")
        ->where("id", "=", $oneGPS->tag_id)
        ->find_all();

      // If the tag no longer exists then delete the record.
      if (count($oneTag) == 0) {
        // Delete the record.
        db::build()->delete("tags_gpses")->where("tag_id", "=", $oneGPS->tag_id)->execute();
        $int_deleted_records++;
      }
    }

    // Redirect back to the main screen and display a "success" message.
    message::success($int_deleted_records . t(" Orphaned Record(s) have been deleted."));
    url::redirect("admin/tagsmap");
  }

  public function confirm_delete_gps($tag_id) {
    // Make sure the user meant to hit the delete button.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tagsmap_delete.html");
    $view->content->tag_id = $tag_id;

    // Determine the name of the tag.
    $tagName = ORM::factory("tag")
      ->where("id", "=", $tag_id)
      ->find_all();
    $view->content->tag_name = $tagName[0]->name;

    print $view;
  }

  public function delete_gps($tag_id) {
    // Delete the GSP data associated with a tag.

    // Delete the record.
    db::build()->delete("tags_gpses")->where("tag_id", "=", $tag_id)->execute();

    // Redirect back to the main screen and display a "success" message.
    message::success(t("Your Settings Have Been Saved."));
    url::redirect("admin/tagsmap");
  }

  private function _get_tagsgpsedit_form($tag_id) {
    // Make a new form for editing GPS data associated with a tag ($tag_id).
    $form = new Forge("admin/tagsmap/savegps", "", "post",
                      array("id" => "g-tags-map-admin-form"));

    // Add a few input boxes for GPS and Description
    $tagsgps_group = $form->group("TagsMapGPS");
    $tagsgps_group->hidden("tag_id")->value($tag_id);

    // Check and see if this ID already has GPS data, then create
    //  input boxes to either update it or enter in new information.
    $existingGPS = ORM::factory("tags_gps")
      ->where("tag_id", "=", $tag_id)
      ->find_all();
    if (count($existingGPS) == 0) {
      $tagsgps_group->input("gps_latitude")
        ->label(t("Latitude"))->value(module::get_var("tagsmap", "googlemap_latitude"));
      $tagsgps_group->input("gps_longitude")
        ->label(t("Longitude"))->value(module::get_var("tagsmap", "googlemap_longitude"));
      $tagsgps_group->textarea("gps_description")->label(t("Description"))->value("");
    } else {
      $tagsgps_group->input("gps_latitude")->label(t("Latitude"))->value($existingGPS[0]->latitude);
      $tagsgps_group->input("gps_longitude")->label(t("Longitude"))->value($existingGPS[0]->longitude);
      $tagsgps_group->textarea("gps_description")->label(t("Description"))->value($existingGPS[0]->description);
    }

    // Add a save button to the form.
    $tagsgps_group->submit("SaveGPS")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }

  public function savegps() {
    // Save the GPS coordinates to the database.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out the values of the text boxes
    $str_tagid = Input::instance()->post("tag_id");
    $str_latitude = Input::instance()->post("gps_latitude");
    $str_longitude = Input::instance()->post("gps_longitude");
    $str_description = Input::instance()->post("gps_description");

    // Save to database.
    // Check and see if this ID already has GPS data,
    //   Update it if it does, create a new record if it doesn't.
    $existingGPS = ORM::factory("tags_gps")
      ->where("tag_id", "=", $str_tagid)
      ->find_all();
    if (count($existingGPS) == 0) {
      $newgps = ORM::factory("tags_gps");
      $newgps->tag_id = $str_tagid;
      $newgps->latitude = $str_latitude;
      $newgps->longitude = $str_longitude;
      $newgps->description = $str_description;
      $newgps->save();
    } else {
      $updatedGPS = ORM::factory("tags_gps", $existingGPS[0]->id);
      $updatedGPS->tag_id = $str_tagid;
      $updatedGPS->latitude = $str_latitude;
      $updatedGPS->longitude = $str_longitude;
      $updatedGPS->description = $str_description;
      $updatedGPS->save();
    }

    // Redirect back to the main screen and display a "success" message.
    message::success(t("Your Settings Have Been Saved."));
    url::redirect("admin/tagsmap");
  }

  private function _get_googlemaps_form() {
    // Make a new form for inputing information associated with google maps.
    $form = new Forge("admin/tagsmap/savemapprefs", "", "post",
                      array("id" => "g-tags-map-admin-form"));

    // Input box for the Maps API Key
    $googlemap_group = $form->group("GoogleMapsKey");
    $googlemap_group->input("google_api_key")
      ->label(t("Google APIs Console key (optional):"))
      ->value(module::get_var("tagsmap", "googlemap_api_key"));

    // Input boxes for the Maps starting location map type and zoom.
    $startingmap_group = $form->group("GoogleMapsPos");
    $startingmap_group->input("google_starting_latitude")
      ->label(t("Starting Latitude"))
      ->value(module::get_var("tagsmap", "googlemap_latitude"))
      ->rules("required");
    $startingmap_group->input("google_starting_longitude")
      ->label(t("Starting Longitude"))
      ->value(module::get_var("tagsmap", "googlemap_longitude"))
      ->rules("required");
    $startingmap_group->input("google_default_zoom")
      ->label(t("Default Zoom Level"))
      ->value(module::get_var("tagsmap", "googlemap_zoom"))
      ->rules("required");
    $startingmap_group->dropdown("google_default_type")
      ->label(t("Default Map Type"))
      ->options(
        array("G_NORMAL_MAP" => "Normal", 
              "G_SATELLITE_MAP" => "Satellite", 
              "G_HYBRID_MAP" => "Hybrid",
              "G_PHYSICAL_MAP" => "Physical", 
              "G_SATELLITE_3D_MAP" => "Google Earth"))
      ->selected(module::get_var("tagsmap", "googlemap_type"));
    $startingmap_group->checkbox("restrict_maps")->label(t("Restrict maps to registered users?"))
      ->checked(module::get_var("tagsmap", "restrict_maps", false));

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }

  public function savemapprefs() {
    // Save information associated with Google Maps to the database.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    $form = $this->_get_googlemaps_form();
    if ($form->validate()) {
      Kohana_Log::add("error",print_r($form,1));
      module::set_var("tagsmap", "googlemap_api_key", $form->GoogleMapsKey->google_api_key->value);
      module::set_var("tagsmap", "googlemap_latitude", $form->GoogleMapsPos->google_starting_latitude->value);
      module::set_var("tagsmap", "googlemap_longitude", $form->GoogleMapsPos->google_starting_longitude->value);
      module::set_var("tagsmap", "googlemap_zoom", $form->GoogleMapsPos->google_default_zoom->value);
      module::set_var("tagsmap", "googlemap_type", $form->GoogleMapsPos->google_default_type->value);
      module::set_var("tagsmap", "restrict_maps", $form->GoogleMapsPos->restrict_maps->value);

      // Display a success message and redirect back to the TagsMap admin page.
      message::success(t("Your settings have been saved."));
      url::redirect("admin/tagsmap");
    }

    // Else show the page with errors
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tagsmap.html");
    $view->content->googlemaps_form = $form;
    $view->content->tags = ORM::factory("tag")->order_by("name", "ASC")->find_all();
    print $view;
  }
}
