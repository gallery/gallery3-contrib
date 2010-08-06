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
class themeroller_task_Core {
  static function available_tasks() {
    // Return empty array so nothing appears in the maintenance screen
    return array();
  }

  static function create_theme($task) {
    $mode = $task->get("mode", "init");
    $start = microtime(true);
    $theme_name = $task->get("theme_name");
    $is_admin = $task->get("is_admin", false);
    $theme_path = THEMEPATH . "$theme_name/";
    $parameters = $task->get("parameters");
    $completed = $task->get("completed", 0);
    switch ($mode) {
    case "init":
      $views = glob(MODPATH . "themeroller/data/views/*.html.php");
      $task->set("mode", "create_directory");
      $parameters = themeroller::get_theme_parameters($task->get("original_name"),
                                                      $task->get("path"),
                                                      $is_admin);
      $task->set("total_activites",
                 7                                 // number of directories to create
                 + 3                               // screen.css, theme.info, thumbnail
                 + count($parameters["standard_css"]) // number of standard css to copy
                 + count($parameters["views"])     // number of views to copy
                 + count($parameters["js"])        // number of javascript files to copy
                 + count($parameters["masks"])     // number of images to generate
                 + count($parameters["icons"])     // number of icon images to generate
                 + count($parameters["css_files"]) // number of css files
                 + count($parameters["images"]));  // number of image files to copy

      $task->status = t("Starting up");
      break;
    case "create_directory":
      $completed = $task->get("completed");
      foreach (array("", "css", "css/themeroller", "css/themeroller/images", "images",
                     "js", "views") as $dir) {
        $path = "{$theme_path}$dir";
        $completed++;
        if (!file_exists($path)) {
          mkdir($path);
          chmod($path, 0755);
         }
      }
      $task->status = t("Directory created");
      $task->set("mode", "copy_views");
      break;
    case "copy_views":
      $task->status = t("Copying views");
      while (!empty($parameters["views"]) && microtime(true) - $start < 1.5) {
        $view = array_shift($parameters["views"]);
        $target = "{$theme_path}views/" . basename($view);
        if (!file_exists($target)) {
          copy($view, $target);
        }
        $completed++;
      }

      if (empty($parameters["views"])){
        $task->status = t("Views copied");
        $task->set("mode", "copy_themeroller_images");
      }
      break;
    case "copy_themeroller_images":
      $task->status = t("Copying themeroller images");
      while (!empty($parameters["images"]) && microtime(true) - $start < 1.5) {
        $image = array_shift($parameters["images"]);
        $target = "{$theme_path}css/themeroller/images/" . basename($image);
        if (!file_exists($target)) {
          copy($image, $target);
        }
        $completed++;
      }

      if (empty($parameters["views"])){
        $task->status = t("Themeroller images copied");
        $task->set("mode", "copy_css");
      }
      break;
    case "copy_css":
      $task->status = t("Copying themeroller css");
      $target = "{$theme_path}css/themeroller/ui.base.css";
      copy($parameters["css_files"][0], $target);
      $completed++;
      $task->status = t("Themeroller css copied");
      $task->set("mode", "generate_images");
      break;
    case "generate_images":
      $task->status = t("Generating gallery images");
      $target_dir = "{$theme_path}images/";
      $colors = $task->get("colors");
      $image_color = $colors["iconColorHover"];
      while (!empty($parameters["masks"]) && microtime(true) - $start < 1.5) {
        $mask = array_shift($parameters["masks"]);
        themeroller::generate_image($mask, $image_color, $target_dir);
        $completed++;
      }
      if (empty($parameters["masks"])) {
        $task->set("mode", "generate_icons");
        $task->status = t("Gallery images generated");
      }
      break;
    case "generate_icons":
      $task->status = t("Generating icons");
      $target_dir = "{$theme_path}css/themeroller/images/";
      while (!empty($parameters["icons"]) && microtime(true) - $start < 1.5) {
        $color = array_shift($parameters["icons"]);
        themeroller::generate_image($parameters["icon_mask"], $color, $target_dir, $color);
        $completed++;
      }
      if (empty($parameters["icons"])) {
        $task->set("mode", "copy_standard_css");
        $task->status = t("Icons generated");
      }
      break;
    case "copy_standard_css":
      $task->status = t("Copying standard css");
      while (!empty($parameters["standard_css"]) && microtime(true) - $start < 1.5) {
        $css = array_shift($parameters["standard_css"]);
        $target = "{$theme_path}css/" . basename($css);
        if (!file_exists($target)) {
          copy($css, $target);
        }
        $completed++;
      }

      if (empty($parameters["standard_css"])){
        $task->status = t("Standard css copied");
        $task->set("mode", "copy_javascript");
      }
      break;
    case "copy_javascript":
      $task->status = t("Copying javascript");
      while (!empty($parameters["js"]) && microtime(true) - $start < 1.5) {
        $js = array_shift($parameters["js"]);
        $target = "{$theme_path}js/" . str_replace(array("admin_", "site_"), "", basename($js));
        if (!file_exists($target)) {
          copy($js, $target);
        }
        $completed++;
      }

      if (empty($parameters["js"])){
        $task->status = t("Javascript copied");
        $task->set("mode", "generate_screen_css");
      }
      break;
    case "generate_screen_css":
      $file = "{$theme_path}/css/screen.css";
      $v = new View(($is_admin ? "admin" : "site") . "_screen.css");
      $v->display_name = $task->get("display_name");
      foreach ($parameters["colors"] as $color => $value) {
        $v->$color = $value;
      }
      ob_start();
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      $completed++;
      $task->status = t("Screen css generated");
      $task->set("mode", "generate_thumbnail");
      break;
    case "generate_thumbnail":
      themeroller::generate_thumbnail($parameters["thumbnail"],
                                      $parameters["thumbnail_parts"],
                                      "{$theme_path}thumbnail.png");
      $task->status = t("Thumbnail generated");
      $task->set("mode", "generate_theme_info");
      $completed++;
      break;
    case "generate_theme_info":
      $file = "{$theme_path}/theme.info";
      $v = new View("theme.info");
      $v->display_name = $task->get("display_name");
      $v->description = $task->get("description");
      $v->user_name = identity::active_user()->name;
      $v->is_admin = $is_admin;
      ob_start();
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      $completed++;
      $task->status = t("Theme info generated");
      $task->set("mode", "done");
      break;
    case "done":
      themeroller::recursive_directory_delete($task->get("path"));
      $display_name = $task->get("display_name");
      $task->done = true;
      $task->state = "success";
      $task->percent_complete = 100;
      $completed = $task->get("total_activites");
      message::info(t("Successfully generated: %name", array("name" => $display_name)));
    }
    $task->set("completed", $completed);
    $task->set("parameters", $parameters);
    $task->percent_complete = ($completed / $task->get("total_activites")) * 100;
  }

}