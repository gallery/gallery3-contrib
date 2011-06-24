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
class tag_cloud_block {
  static function get_site_list() {
    return array(
      "tag_cloud_site" => t("Tag Cloud"));
  }

  static function get($block_id, $theme) {
    $block = "";
    switch ($block_id) {
    case "tag_cloud_site":
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
      $block = new Block();
      $block->css_id = "g-tag";
      $block->title = t("Tag Cloud");
      $block->content = new View("tag_cloud_block.html");
      $block->content->cloud = tag::cloud(30);
      $block->content->options = $options;

      if ($theme->item() && $theme->page_subtype() != "tag" && access::can("edit", $theme->item())) {
        $controller = new Tags_Controller();
        $block->content->form = tag::get_add_form($theme->item());
      } else {
        $block->content->form = "";
      }
      break;
    }
    return $block;
  }
}
