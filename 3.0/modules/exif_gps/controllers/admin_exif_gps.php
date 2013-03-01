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

class Admin_EXIF_GPS_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_exif_gps.html");
    $view->content->exifgps_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Save user preferences to the database.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Make sure the user filled out the form properly.
    $form = $this->_get_admin_form();
    if ($form->validate()) {
      // Save settings to Gallery's database.
      module::set_var("exif_gps", "googlemap_api_key", $form->Global->google_api_key->value);
      module::set_var("exif_gps", "googlemap_max_autozoom", $form->Global->max_auto_zoom_level->value);
      module::set_var("exif_gps", "markercluster_gridsize", $form->markercluster->markercluster_gridsize->value);
      module::set_var("exif_gps", "markercluster_maxzoom", $form->markercluster->markercluster_maxzoom->value);
      module::set_var("exif_gps", "sidebar_zoom", $form->Sidebar->sidebar_default_zoom->value);
      module::set_var("exif_gps", "sidebar_mapformat", $form->Sidebar->sidebar_mapformat->value);
      module::set_var("exif_gps", "sidebar_maptype", $form->Sidebar->sidebar_maptype->value);
      module::set_var("exif_gps", "largemap_maptype", $form->LargeMap->largemap_maptype->value);
      module::set_var("exif_gps", "toolbar_map_album", $form->Global->toolbar_map_album->value);
      module::set_var("exif_gps", "toolbar_map_user", $form->Global->toolbar_map_user->value);
      module::set_var("exif_gps", "restrict_maps", $form->Global->restrict_maps->value);

      // Display a success message and redirect back to the TagsMap admin page.
      message::success(t("Your settings have been saved."));
      url::redirect("admin/exif_gps");
    }

    // Else show the page with errors
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_exif_gps.html");
    $view->content->exifgps_form = $form;
    print $view;
  }

  private function _get_admin_form() {
    // Make a new Form.
    $form = new Forge("admin/exif_gps/saveprefs", "", "post",
                      array("id" => "g-exif-gps-adminForm"));

    // Create group for global settings, like the Maps API Key
    $gps_global_group = $form->group("Global")
                             ->label(t("Global Settings"));
    $gps_global_group->input("google_api_key")
      ->label(t("Google APIs Console key (optional):"))
      ->value(module::get_var("exif_gps", "googlemap_api_key"));
    $gps_global_group->input("max_auto_zoom_level")
      ->label(t("Maximum Auto-Zoom Level:"))
      ->value(module::get_var("exif_gps", "googlemap_max_autozoom"));
    $gps_global_group->checkbox("toolbar_map_album")->label(t("Show \"Map this album\" icon?"))
      ->checked(module::get_var("exif_gps", "toolbar_map_album", false));
    $gps_global_group->checkbox("toolbar_map_user")->label(t("Show \"Map this user\" icon?"))
      ->checked(module::get_var("exif_gps", "toolbar_map_user", false));
    $gps_global_group->checkbox("restrict_maps")->label(t("Restrict maps to registered users?"))
      ->checked(module::get_var("exif_gps", "restrict_maps", false));

    // Create a group for marker cluster settings
    $gps_markercluster = $form->group("markercluster")
                        ->label(t("Marker Cluster Settings"));
    $gps_markercluster->input("markercluster_gridsize")
                      ->label(t("Grid Size"))
                      ->value(module::get_var("exif_gps", "markercluster_gridsize"))
                      ->rules("required");
    $gps_markercluster->input("markercluster_maxzoom")
                      ->label(t("Max Zoom"))
                      ->value(module::get_var("exif_gps", "markercluster_maxzoom"))
                      ->rules("required");

    // Create a group for sidebar settings
    $gps_sidebar = $form->group("Sidebar")
                        ->label(t("Sidebar Settings"));
    $gps_sidebar->input("sidebar_default_zoom")
                ->label(t("Default Zoom Level"))
                ->value(module::get_var("exif_gps", "sidebar_zoom"))
                ->rules("required");
    $gps_sidebar->dropdown("sidebar_mapformat")
                ->label(t("Map Interface"))
                ->options(array(t("Static"), t("Interactive")))
                ->selected(module::get_var("exif_gps", "sidebar_mapformat"));
    $gps_sidebar->dropdown("sidebar_maptype")
                ->label(t("Default Map Type"))
                ->options(array(t("Map"), t("Satellite"), 
                                t("Hybrid"), t("Terrain")))
                ->selected(module::get_var("exif_gps", "sidebar_maptype"));

    // Create a group for map album/user settings
    $gps_large_map_group = $form->group("LargeMap")
                                ->label(t("Map Album/User Settings"));
    $gps_large_map_group->dropdown("largemap_maptype")
                        ->label(t("Default Map Type"))
                        ->options(array(t("Map"), t("Satellite"), 
                                        t("Hybrid"), t("Terrain")))
                        ->selected(module::get_var("exif_gps", "largemap_maptype"));

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
}
