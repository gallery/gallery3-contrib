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
class Tag_Cloud_Page_Controller extends Controller {
  public function index() {
    // Require view permission for the root album for security purposes.
    $album = ORM::factory("item", 1);
    access::required("view", $album);

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "other", "Tag Cloud");
    $template->content = new View("tag_cloud_page_cloud.html");
    $template->content->title = t("Tag Cloud");

    //  If the tag cloud module is active, load its settings from the database.
    if (module::is_active("tag_cloud")) {
      $options = array();
      foreach (array("tagcolor", "background_color", "mouseover", "transparent", "speed", "distribution")
               as $option) {
        $value = module::get_var("tag_cloud", $option, null);
        if (!empty($value)) {
          switch ($option) {
          case "tagcolor":
            $options["tcolor"] = $value;
            break;
          case "mouseover":
            $options["hicolor"] = $value;
            break;
          case "background_color":
            $options["bgColor"] = $value;
            break;
          case "transparent":
            $options["wmode"] = "transparent";
            break;
          case "speed":
            $options["tspeed"] = $value;
            break;
          case "distribution":
            $options["distr"] = "true";
            break;
          }
        }
      }
      $template->content->options = $options;
    }

    // Display the page.
    print $template;
  }
}
