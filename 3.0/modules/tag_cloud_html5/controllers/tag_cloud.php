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
class Tag_Cloud_Controller extends Controller {
  public function index() {
    // Require view permission for the root album for security purposes.
    $album = ORM::factory("item", 1);
    access::required("view", $album);

    // Get settings
    $options = module::get_var("tag_cloud_html5", "options_wholecloud", null);
    $maxtags = module::get_var("tag_cloud_html5", "maxtags_wholecloud", null);
   
    $options = json_decode($options, true);
    $options['hideTags'] = !module::get_var("tag_cloud_html5", "show_wholecloud_list", true);
    $options = json_encode($options);

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "other", "Tag cloud");
    $template->content = new View("tag_cloud_html5_page.html");
    $template->content->title = t("Tag cloud");
    $template->content->cloud = tag::cloud($maxtags);
    $template->content->options = $options;

    // Display the page.
    print $template;
  }
}
