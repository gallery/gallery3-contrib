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
class Videos_Controller extends Admin_Controller {
  public function browse($id) {
    $paths = unserialize(module::get_var("videos", "authorized_paths"));
    foreach (array_keys($paths) as $path) {
      $files[] = $path;
    }

    $item = ORM::factory("item", $id);
    $view = new View("videos_tree_dialog.html");
    $view->item = $item;
    $view->tree = new View("videos_tree.html");
    $view->tree->files = $files;
    $view->tree->parents = array();
    print $view;
  }

  public function children() {
    $path = Input::instance()->get("path");

    $tree = new View("videos_tree.html");
    $tree->files = array();
    $tree->parents = array();

    // Make a tree with the parents back up to the authorized path, and all the children under the
    // current path.
    if (videos::is_valid_path($path)) {
      $tree->parents[] = $path;
      while (videos::is_valid_path(dirname($tree->parents[0]))) {
        array_unshift($tree->parents, dirname($tree->parents[0]));
      }

      $glob_path = str_replace(array("{", "}", "[", "]"), array("\{", "\}", "\[", "\]"), $path);
      foreach (glob("$glob_path/*") as $file) {
        if (!is_readable($file)) {
          continue;
        }
        if (!is_dir($file)) {
          $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
          //if (!in_array($ext, array("gif", "jpeg", "jpg", "png", "flv", "mp4", "m4v"))) {
          if (!in_array($ext, unserialize(module::get_var("videos", "allowed_extensions")))) {
            continue;
          }
        }

        $tree->files[] = $file;
      }
    } else {
      // Missing or invalid path; print out the list of authorized path
      $paths = unserialize(module::get_var("videos", "authorized_paths"));
      foreach (array_keys($paths) as $path) {
        $tree->files[] = $path;
      }
    }
    print $tree;
  }

  /**
   * Begin the task of adding files.
   */
  public function start() {
    access::verify_csrf();
    $item = ORM::factory("item", Input::instance()->get("item_id"));

    foreach (Input::instance()->post("paths") as $path) {
      if (videos::is_valid_path($path)) {
        $paths[] = array($path, null);
      }
    }

    $task_def = Task_Definition::factory()
      ->callback("Videos_Controller::add")
      ->description(t("Add videos from the local server"))
      ->name(t("Add from server"));
    $task = task::create($task_def, array("item_id" => $item->id, "queue" => $paths));

    json::reply(
      array("result" => "started",
            "status" => (string)$task->status,
            "url" => url::site("videos/run/$task->id?csrf=" . access::csrf_token())));
  }

  /**
   * Run the task of adding files
   */
  function run($task_id) {
    access::verify_csrf();

    $task = ORM::factory("task", $task_id);
    if (!$task->loaded() || $task->owner_id != identity::active_user()->id) {
      access::forbidden();
    }

    $task = task::run($task_id);
    // Prevent the JavaScript code from breaking by forcing a period as
    // decimal separator for all locales with sprintf("%F", $value).
    json::reply(array("done" => (bool)$task->done,
                      "status" => (string)$task->status,
                      "percent_complete" => sprintf("%F", $task->percent_complete)));
  }

  /**
   * This is the task code that adds photos and albums.  It first examines all the target files
   * and creates a set of Server_Add_File_Models, then runs through the list of models and adds
   * them one at a time.
   */
  static function add($task) {
    $mode = $task->get("mode", "init");
    $start = microtime(true);

    switch ($mode) {
    case "init":
      $task->set("mode", "build-file-list");
      $task->percent_complete = 0;
      $task->status = t("Starting up");
      batch::start();
      break;

    case "build-file-list":  // 0% to 10%
      // We can't fit an arbitrary number of paths in a task, so store them in a separate table.
      // Don't use an iterator here because we can't get enough control over it when we're dealing
      // with a deep hierarchy and we don't want to go over our time quota.  The queue is in the
      // form [path, parent_id] where the parent_id refers to another Server_Add_File_Model.  We
      // have this extra level of abstraction because we don't know its Item_Model id yet.
      $queue = $task->get("queue");
      $paths = unserialize(module::get_var("videos", "authorized_paths"));

      while ($queue && microtime(true) - $start < 0.5) {
        list($file, $parent_entry_id) = array_shift($queue);
        // Ignore the staging directories as directories to be imported.
        if (empty($paths[$file])) {
          $entry = ORM::factory("videos_file");
          $entry->task_id = $task->id;
          $entry->file = $file;
          $entry->parent_id = $parent_entry_id;
          $entry->save();
          $entry_id = $entry->id;
        } else {
          $entry_id = null;
        }

        $file = preg_quote($file);
        foreach (glob("$file/*") as $child) {
          if (is_dir($child)) {
            $queue[] = array($child, $entry_id);
          } else {
            $ext = strtolower(pathinfo($child, PATHINFO_EXTENSION));
            //if (in_array($ext, array("gif", "jpeg", "jpg", "png", "flv", "mp4", "m4v")) &&
            if (in_array($ext, unserialize(module::get_var("videos", "allowed_extensions"))) &&
                filesize($child) > 0) {
              $child_entry = ORM::factory("videos_file");
              $child_entry->task_id = $task->id;
              $child_entry->file = $child;
              $child_entry->parent_id = $entry_id;
              $child_entry->save();
            }
          }
        }
      }

      // We have no idea how long this can take because we have no idea how deep the tree
      // hierarchy rabbit hole goes.  Leave ourselves room here for 100 iterations and don't go
      // over 10% in percent_complete.
      $task->set("queue", $queue);
      $task->percent_complete = min($task->percent_complete + 0.1, 10);
      $task->status = t2(
        "Found one file", "Found %count files",
        ORM::factory("videos_file")->where("task_id", "=", $task->id)->count_all());

      if (!$queue) {
        $task->set("mode", "add-files");
        $task->set(
          "total_files",
          ORM::factory("videos_file")->where("task_id", "=", $task->id)->count_all());
        $task->percent_complete = 10;
      }
      break;

    case "add-files": // 10% to 100%
      $completed_files = $task->get("completed_files", 0);
      $total_files = $task->get("total_files");

      // Ordering by id ensures that we add them in the order that we created the entries, which
      // will create albums first.  Ignore entries which already have an Item_Model attached,
      // they're done.
      $entries = ORM::factory("videos_file")
        ->where("task_id", "=", $task->id)
        ->where("item_id", "IS", null)
        ->order_by("id", "ASC")
        ->limit(10)
        ->find_all();
      if ($entries->count() == 0) {
        // Out of entries, we're done.
        $task->set("mode", "done");
      }

      $owner_id = identity::active_user()->id;
      foreach ($entries as $entry) {
        if (microtime(true) - $start > 0.5) {
          break;
        }

        // Look up the parent item for this entry.  By now it should exist, but if none was
        // specified, then this belongs as a child of the current item.
        $parent_entry = ORM::factory("videos_file", $entry->parent_id);
        if (!$parent_entry->loaded()) {
          $parent = ORM::factory("item", $task->get("item_id"));
        } else {
          $parent = ORM::factory("item", $parent_entry->item_id);
        }

        $name = basename($entry->file);
        $title = item::convert_filename_to_title($name);
        if (is_dir($entry->file)) {
          $album = ORM::factory("item");
          $album->type = "album";
          $album->parent_id = $parent->id;
          $album->name = $name;
          $album->title = $title;
          $album->owner_id = $owner_id;
          $album->save();
          $entry->item_id = $album->id;
        } else {
          try {
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($extension, unserialize(module::get_var("videos", "allowed_extensions")))) {
              $movie = ORM::factory("item");
              $movie->type = "movie";
              $movie->parent_id = $parent->id;
              $movie->set_data_file($entry->file);
              $movie->name = $name;
              $movie->title = $title;
              $movie->owner_id = $owner_id;
              $movie->save();
              $entry->item_id = $movie->id;
              $items_video = ORM::factory("items_video");
              $items_video->item_id = $movie->id;
              $items_video->save();
              if (file_exists($entry->file . ".flv")) {
                copy($entry->file . ".flv", $movie->resize_path() . ".flv");
                list ($vid_width, $vid_height, $mime_type) = movie::get_file_metadata($entry->file . ".flv");
                $movie->height = $vid_height;
                $movie->width = $vid_width;
                $movie->save();
              }
            } else {
              // This should never happen, because we don't add stuff to the list that we can't
              // process.  But just in, case.. set this to a non-null value so that we skip this
              // entry.
              $entry->item_id = 0;
              $task->log("Skipping unknown file type: $entry->file");
            }
          } catch (Exception $e) {
            // This can happen if a photo file is invalid, like a BMP masquerading as a .jpg
            $entry->item_id = 0;
            $task->log("Skipping invalid file: $entry->file");
          }
        }

        $completed_files++;
        $entry->save();
      }
      $task->set("completed_files", $completed_files);
      $task->status = t("Adding photos / albums (%completed of %total)",
                        array("completed" => $completed_files,
                              "total" => $total_files));
      $task->percent_complete = $total_files ? 10 + 100 * ($completed_files / $total_files) : 100;
      break;

    case "done":
      batch::stop();
      $task->done = true;
      $task->state = "success";
      $task->percent_complete = 100;
      db::build()
        ->delete("videos_files")
        ->where("task_id", "=", $task->id)
        ->execute();
      message::info(t2("Successfully added one file",
                       "Successfully added %count files",
                       $task->get("completed_files")));
    }
  }
}
