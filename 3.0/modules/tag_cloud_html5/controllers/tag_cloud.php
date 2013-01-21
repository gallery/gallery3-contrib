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
class Tag_Cloud_Controller extends Controller {
  public function index() {
    // Require view permission for the root album for security purposes.
    $album = ORM::factory("item", 1);
    access::required("view", $album);

    // Get settings
    $options = module::get_var("tag_cloud_html5", "options_wholecloud", null);
    $maxtags = module::get_var("tag_cloud_html5", "maxtags_wholecloud", null);
    $width = module::get_var("tag_cloud_html5", "width_wholecloud", null);
    $height = module::get_var("tag_cloud_html5", "height_wholecloud", null);
   
    $options = json_decode($options, true);
    $options['hideTags'] = !module::get_var("tag_cloud_html5", "show_wholecloud_list", true);
    $options = json_encode($options);

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "other", "Tag cloud");
    $template->content = new View("tag_cloud_html5_page.html");
    $template->content->title = t("Tag cloud");
    $template->content->cloud = tag::cloud($maxtags);
    $template->content->options = $options;
    $template->content->width = $width;
    $template->content->height = $height;

    // Display the page.
    print $template;
  }
  
  public function embed() {
    /**
     * This is used to embed the tag cloud in other things.  New in version 7.
     *
     * It expects the url to be in the form:
     *   tag_cloud/embed/optionsbase/option1/value1/option2/value2/.../optionN/valueN
     * Where:
     *   optionsbase = "sidebar" or "wholecloud" (takes settings from this config)
     *   optionX = option name (either "maxtags" or any of the TagCanvas parameters - no name verification performed!)
     *   valueX = value of option (no value verification performed here!)
     * Here's how the tag cloud is built:
     * 1. Load "maxtags" and "options" variables for optionbase (as defined in admin menu or admin/advanced variables)
     *    Note: width and height are ignored, and the add tag form, wholecloud link, and inline tags are not shown.
     * 2. Use option/value pairs to override and/or append those loaded above.
     * 3. Build tag cloud, using 100% of the size from its parent.
     * Correspondingly, the optionsbase is required, but the options and values are not.
     */

    // Require view permission for the root album for security purposes.
    $album = ORM::factory("item", 1);
    access::required("view", $album);

    // get the function arguments
    $args = func_get_args();
    
    // get/check the number of arguments - must be odd
    $countargs = count($args);
    if ($countargs % 2 == 0) {
      return;
    }
    
    // get/check the first argument - must be sidebar or wholecloud
    $optionsbase = $args[0];
    if (!(in_array($optionsbase, array("sidebar", "wholecloud")))) {
      return;
    }
    
    // get and override/append options/values
    $maxtags = module::get_var("tag_cloud_html5", "maxtags_".$optionsbase, null);
    $options = module::get_var("tag_cloud_html5", "options_".$optionsbase, null);
    $options = json_decode($options, true);
    for ($i = 0; $i < ($countargs-1)/2; $i++) {
      $option = $args[2*$i+1];
      $value = $args[2*$i+2];
      if ($option == "maxtags") {
        // assign to maxtags
        $maxtags = $value;
      } elseif (substr($option,-6) == 'Colour') {
        // assign to options with a hash in front
        $options[$option] = '#'.$value;
      } else {
        // assign to options
        $options[$option] = $value;
      }
    }
    $options = json_encode($options);
      
    // Set up and display the actual page.
    $template = new View("tag_cloud_html5_embed.html");
    $template->cloud = tag::cloud($maxtags);
    $template->options = $options;

    // Display the page.
    print $template;
  }
}