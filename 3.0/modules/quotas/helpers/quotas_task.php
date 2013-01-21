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
class quotas_task_Core {
  static function available_tasks() {
    // Total up all non-guest users in the users table that don't have a corresponding record
    //   in the users_space_usages table.
    //   If the result is greater then 0, display a warning for this task so the admin knows
    //   a user is missing and it should be run.
    $missing_users = ORM::factory("user")
      ->where("users.guest", "=", "0")
      ->join("users_space_usages", "users.id", "users_space_usages.owner_id", "LEFT OUTER")
      ->and_where("users_space_usages.owner_id", "IS", NULL)->count_all();

    $tasks = array();
    $tasks[] = Task_Definition::factory()
               ->callback("quotas_task::update_quotasdb")
               ->name(t("Rebuild user quotas table"))
               ->description(t("Recalculates each users space usage."))
               ->severity($missing_users ? log::WARNING : log::SUCCESS);

    return $tasks;
  }

  static function update_quotasdb($task) {
    // Re-create the users_space_usages table and recalculate all values.

    // Retrieve the total variable.  If this is the first time this function has been run,
    // total will be empty.
    $total = $task->get("total");
    $existing_items = ORM::factory("item")->where("type", "!=", "album")->find_all();

    if (empty($total)) {
      // If this is the first time this function has been run, 
      //  delete and re-create the users_space_usages table, and set up 
      //  some initial variables.
      $db = Database::instance();
      $db->query("DROP TABLE IF EXISTS {users_space_usages};");
      $db->query("CREATE TABLE IF NOT EXISTS {users_space_usages} (
                 `id` int(9) NOT NULL auto_increment,
                 `owner_id` int(9) NOT NULL,
                 `fullsize` BIGINT UNSIGNED NOT NULL,
                 `resize` BIGINT UNSIGNED NOT NULL,
                 `thumb` BIGINT UNSIGNED NOT NULL,
                 PRIMARY KEY (`id`),
                 KEY(`owner_id`, `id`))
                 DEFAULT CHARSET=utf8;");

      // Set the initial values for all variables.
      $task->set("total", count($existing_items));
      $total = $task->get("total");
      $task->set("last_id", 0);
      $task->set("completed_items", 0);
      $task->set("total_users", ORM::factory("user")->where("guest", "=", "0")->count_all());
      $task->set("completed_users", 0);
      $task->set("last_user_id", 0);
    }

    // Retrieve the values for variables from the last time this
    //  function was run.
    $last_id = $task->get("last_id");
    $completed_items = $task->get("completed_items");
    $total_users = $task->get("total_users");
    $completed_users = $task->get("completed_users");
    $last_user_id = $task->get("last_user_id");

    // First set up default values for all non-guest users.
    if ($total_users > $completed_users) {
      $one_user = ORM::factory("user")
                  ->where("guest", "=", "0")
                  ->where("id", ">", $last_user_id)
                  ->order_by("id")
                  ->find_all(1);
      $record = ORM::factory("users_space_usage")->where("owner_id", "=", $one_user[0]->id)->find();
      if (!$record->loaded()) {
        $record->owner_id = $one_user[0]->id;
        $record->fullsize = 0;
        $record->resize = 0;
        $record->thumb = 0;
        $record->save();
      }
      $task->set("last_user_id", $one_user[0]->id);
      $task->set("completed_users", ++$completed_users);
      $task->status = t("Populating quotas table...");

    } else {
      // Loop through each non-album item in Gallery and log its file size to its owner.
      $item = ORM::factory("item")
              ->where("type", "!=", "album")	  
              ->where("id", ">", $last_id)
              ->order_by("id")
              ->find_all(1);
      $record = ORM::factory("users_space_usage")->where("owner_id", "=", $item[0]->owner_id)->find();
      $record->add_item($item[0]);

      // Store the current position and update the status message.
      $task->set("last_id", $item[0]->id);
      $task->set("completed_items", ++$completed_items);
      if ($total == $completed_items) {
        $task->done = true;
        $task->state = "success";
        $task->percent_complete = 100;
        $task->status = t("Complete");
      } else {
        $task->percent_complete = round(100 * $completed_items / $total);
        $task->status = t("Scanning $completed_items of $total files");
      }
    }
  }
}
