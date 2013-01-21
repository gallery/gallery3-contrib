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
class themeroller_task_Core {
  static function available_tasks() {
    // Return empty array so nothing appears in the maintenance screen
    return array();
  }

  static function create_theme($task) {
    try {
      $mode = $task->get("mode", "init");
      $start = microtime(true);
      $theme_name = $task->get("theme_name");
      $is_admin = $task->get("is_admin", false);
      $theme_path = THEMEPATH . "$theme_name/";
      $parameters = Cache::instance()->get("create_theme_cache:{$task->id}");
      if ($parameters) {
        $parameters = unserialize($parameters);
      }
      $completed = $task->get("completed", 0);
      switch ($mode) {
      case "init":
        $task->log(t("Starting theme '%theme' creation", array("theme" => $task->get("display_name"))));
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
                   + count($parameters["gifs"])      // number of static files
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
            $task->log(t("Created directory: %path", array("path" => $path)));
          }
        }
        $task->status = t("Directories created");
        $task->set("mode", "copy_views");
        break;
      case "copy_views":
        $task->status = t("Copying views");
        while (!empty($parameters["views"]) && microtime(true) - $start < 1.5) {
          $view = array_shift($parameters["views"]);
          $target = "{$theme_path}views/" . basename($view);
          if (!file_exists($target)) {
            copy($view, $target);
            $task->log(t("Copied view: %path", array("path" => basename($view))));
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
            $task->log(t("Copied themeroller image: %path", array("path" => basename($image))));
          }
          $completed++;
        }

        if (empty($parameters["views"])){
          $task->status = t("Themeroller images copied");
          $task->set("mode", "copy_gif_images");
        }
        break;
      case "copy_gif_images":
        $task->status = t("Copying gif images");
        while (!empty($parameters["gifs"]) && microtime(true) - $start < 1.5) {
          $gif = array_shift($parameters["gifs"]);
          $target = "{$theme_path}images/" . basename($gif);
          if (!file_exists($target)) {
            copy($gif, $target);
            $task->log(t("Copied gif image: %path", array("path" => basename($gif))));
          }
          $completed++;
        }

        if (empty($parameters["gifs"])){
          $task->status = t("Gif images copied");
          $task->set("mode", "copy_css");
        }
        break;
      case "copy_css":
        $task->status = t("Copying themeroller css");
        $target = "{$theme_path}css/themeroller/ui.base.css";
        copy($parameters["css_files"][0], $target);
        $completed++;
        $task->log(t("Copied themeroller css: themeroller/ui.base.css"));
        $task->status = t("Themeroller css copied");
        $task->set("mode", "generate_images");
        break;
      case "generate_images":
        $task->status = t("Generating gallery images");
        $target_dir = "{$theme_path}images/";
        $colors = $task->get("colors");
        $image_color = $parameters["colors"]["iconColorContent"];
        while (!empty($parameters["masks"]) && microtime(true) - $start < 1.5) {
          $mask = array_shift($parameters["masks"]);
          $basename = basename($mask);
          if (preg_match("/(.*)_mask(\[(\w*)\])?(\.png)$/", $basename, $matches)) {
            $basename = "{$matches[1]}{$matches[4]}";
            $image_color = empty($matches[3]) ? $parameters["colors"]["iconColorContent"] :
                                                $parameters["colors"][$matches[3]];
          } else {
            $image_color = $parameters["colors"]["iconColorContent"];
          }
          $image_file = "{$target_dir}$basename";
          themeroller::generate_image($mask, $image_file, $image_color);
          $completed++;
          $task->log(t("Generated image: %path", array("path" => $image_file)));
        }
        if (empty($parameters["masks"])) {
          $task->set("mode", "generate_icons");
          $task->status = t("Gallery images generated");
        }
        break;
      case "generate_icons":
        $task->status = t("Generating icons");
        $target_dir = "{$theme_path}css/themeroller/images/";
        $mask_file = $parameters["icon_mask"];
        while (!empty($parameters["icons"]) && microtime(true) - $start < 1.5) {
          $color = array_shift($parameters["icons"]);
          $icon_file = $target_dir . str_replace("mask", $color, basename($mask_file));
          themeroller::generate_image($mask_file, $icon_file, $color);
          $completed++;
          $task->log(t("Generated themeroller icon: %path", array("path" => $icon_file)));
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
            $task->log(t("Copied css file: %path", array("path" => basename($target))));
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
            $task->log(t("Copied js file: %path", array("path" => basename($target))));
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
        foreach (array("screen", "screen-rtl") as $file) {
          $css_file = "{$theme_path}/css/$file.css";
          $v = new View(($is_admin ? "admin" : "site") . "_{$file}.css");
          $v->display_name = $task->get("display_name");
          foreach ($parameters["colors"] as $color => $value) {
            $v->$color = $value;
          }
          ob_start();
          print $v->render();
          file_put_contents($css_file, ob_get_contents());
          ob_end_clean();
        }
        $completed++;
        $task->log(t("Generated screen css: %path", array("path" => $file)));
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
        $task->log(t("Generated theme thumbnail: %path",
                   array("path" => "{$theme_path}thumbnail.png")));
        break;
      case "generate_theme_info":
        $file = "{$theme_path}/theme.info";
        $v = new View("theme.info");
        $v->display_name = $task->get("display_name");
        $v->description = $task->get("description");
        $v->user_name = $task->get("user_name");
        $v->author_url = $task->get("author_url");
        $v->info_url = $task->get("info_url");
        $v->discuss_url = $task->get("discuss_url");
        $v->is_admin = $is_admin;
        $v->definition = json_encode($parameters["colors"]);
        ob_start();
        print $v->render();
        file_put_contents($file, ob_get_contents());
        ob_end_clean();
        $completed++;
        $task->log(t("Generated theme info: %path", array("path" => "{$theme_path}theme.info")));
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
        Cache::instance()->delete("create_theme_cache:{$task->id}");
        $message = t("Successfully generated: %name", array("name" => $display_name));
        message::info($message);
        $task->log($message);
        $task->status = t("'%name' generated", array("name" => $display_name));
      }
      $task->set("completed", $completed);
      if (!$task->done) {
        Cache::instance()->set("create_theme_cache:{$task->id}", serialize($parameters));
        $task->percent_complete = ($completed / $task->get("total_activites")) * 100;
      }
    } catch (Exception $e) {
      Kohana_Log::add("error",(string)$e);
      $task->done = true;
      $task->state = "error";
      $task->status = $e->getMessage();
      $task->log((string)$e);
    }
  }

}