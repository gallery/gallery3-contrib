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
class exif_gps_task_Core {
  static function available_tasks() {
    // Automatically delete extra exif_coordinates whenever the maintance screen is loaded.
    db::build()
      ->delete("exif_coordinates")
      ->where("item_id", "NOT IN",
              db::build()->select("id")->from("items"))
      ->execute();

    // Display an option on the maintance screen for scanning existing photos
    // for GPS data (in case photos were uploaded before the module was active).
    return array(Task_Definition::factory()
                 ->callback("exif_gps_task::update_gps_index")
                 ->name(t("Extract Exif GPS data"))
                 ->description(t("Scan all photos for missing GPS data"))
                 ->severity(log::SUCCESS));
  }

  static function update_gps_index($task) {
    $start = microtime(true);

    // Figure out the total number of photos in the database.
    // If this is the first run, also set last_id and completed to 0.
    $total = $task->get("total");
    if (empty($total)) {
      $task->set("total", $total = count(ORM::factory("item")->where("type", "=", "photo")->find_all()));
      $task->set("last_id", 0);
      $task->set("completed", 0);
    }
    $last_id = $task->get("last_id");
    $completed = $task->get("completed");

    // Generate an array of the next 100 photos to check.

    // Check each photo in the array to see if it already has exif gps data associated with it.
    //  If it doesn't, attempt to extract gps coordinates.
    foreach (ORM::factory("item")
             ->where("id", ">", $last_id)
             ->where("type", "=", "photo")
             ->order_by("id")
             ->find_all(100) as $item) {

      $record = ORM::factory("exif_coordinate")->where("item_id", "=", $item->id)->find();
      if (!$record->loaded()) {
        exif_gps::extract($item);
      }
      $last_id = $item->id;
      $completed++;

      if ($completed == $total || microtime(true) - $start > 1.5) {
        break;
      }
    }

    $task->set("completed", $completed);
    $task->set("last_id", $last_id);

    if ($total == $completed) {
      $task->done = true;
      $task->state = "success";
      $task->percent_complete = 100;
    } else {
      $task->percent_complete = round(100 * $completed / $total);
    }
    $task->status = t2("One photo scanned", "%count / %total photos scanned", $completed,
                       array("total" => $total));
  }
}
