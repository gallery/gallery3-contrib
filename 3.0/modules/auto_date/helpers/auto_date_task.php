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
class auto_date_task_Core {

  static function available_tasks() {
    $tasks[] = Task_Definition::factory()
      ->callback("auto_date_task::set_auto_date_all")
      ->name(t("Set captured date for all unset items"))
      ->description(t("Attempt to guess the captured date based on the filename (NULL capture dates only)"))
      ->severity(log::SUCCESS);
    return $tasks;
  }

  /**
   * @param Task_Model the task
   */
  static function set_auto_date_all($task) {
    $errors = array();
    try {
      $start = microtime(true);
      $last_item_id= $task->get("last_item_id", null);
      $current = 0;
      $total = 0;

      switch ($task->get("mode", "init")) {
      case "init":
        $task->set("total", ORM::factory("item")->where("type", "!=", "album")->and_where("captured", "is", NULL)->count_all());
        $task->set("mode", "set_auto_date_all");
        $task->set("completed", 0);
        $task->set("last_item_id", 0);

      case "set_auto_date_all":
        $completed = $task->get("completed");
        $total = $task->get("total");
        $last_item_id= $task->get("last_item_id");
        $items = ORM::factory("item")
          ->where("id", ">", $last_item_id)
          ->and_where("type", "!=", "album")
          ->and_where("captured", "is", NULL)
          ->find_all(100); /* TODO: should we fetch more at a time? Less? */
        while ($current < $total && microtime(true) - $start < 1 && $item = $items->current()) {
          $last_tem_id = $item->id;
          $task->log("Looking at item {$item->name} (id: {$item->id})");
          auto_date::set_auto_date($item);

          $completed++;
          $items->next();
          $task->percent_complete = $completed / $total * 100;
          $task->set("completed", $completed);
          $task->set("last_item_id", $item->id);


          $task->status = t2("Examined %count items", "Examined %count items", $completed);

          if ($completed == $total) {
            $task->done = true;
            $task->state = "success";
            $task->percent_complete = 100;
          }
        }
      }
    } catch (Exception $e) {
      Kohana_Log::add("error",(string)$e);
      $task->done = true;
      $task->state = "error";
      $task->status = $e->getMessage();
      $errors[] = (string)$e;
    }
    if ($errors) {
      $task->log($errors);
    }
  }
}
