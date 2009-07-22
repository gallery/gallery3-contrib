<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class rescue_task_Core {
  const LEFT = 0;
  const RIGHT = 1;

  static function available_tasks() {
    return array(Task_Definition::factory()
                 ->callback("rescue_task::fix_mptt")
                 ->name(t("Fix Album/Photo hierarchy"))
                 ->description(t("Fix problems where your album/photo breadcrumbs are out of " .
                                 "sync with your actual hierarchy."))
                 ->severity(log::SUCCESS));
  }

  static function fix_mptt($task) {
    $start = microtime(true);

    $total = $task->get("total");
    if (empty($total)) {
      $task->set("total", $total = Database::instance()->count_records("items"));
      $task->set("stack", array(array(1, self::LEFT)));
      $task->set("ptr", 1);
      $task->set("completed", 0);
    }

    $ptr = $task->get("ptr");
    $stack = $task->get("stack");
    $completed = $task->get("completed");

    // Implement a depth-first tree walk using a stack.  Not the most efficient, but it's simple.
    while ($stack && microtime(true) - $start < 1.5) {
      list($id, $state) = array_pop($stack);
      switch ($state) {
      case self::LEFT:
        self::set_left($id, $ptr++);
        $item = ORM::factory("item", $id);
        array_push($stack, array($id, self::RIGHT));
        foreach (self::children($id) as $child) {
          array_push($stack, array($child->id, self::LEFT));
        }
        break;

      case self::RIGHT:
        self::set_right($id, $ptr++);
        $completed++;
        break;
      }
    }

    $task->set("stack", $stack);
    $task->set("ptr", $ptr);
    $task->set("completed", $completed);

    if ($total == $completed) {
      $task->done = true;
      $task->state = "success";
      $task->percent_complete = 100;
    } else {
      $task->percent_complete = round(100 * $completed / $total);
    }
    $task->status = t2("One row updated", "%count / %total rows updated", $completed,
                       array("total" => $total));
  }

  static function children($parent_id) {
    return Database::instance()
      ->select("id")
      ->from("items")
      ->where("parent_id", $parent_id)
      ->orderby("left", "ASC")
      ->get();
  }

  static function set_left($id, $value) {
    Database::instance()->update("items", array("left" => $value), array("id" => $id));
  }

  static function set_right($id, $value) {
    Database::instance()->update("items", array("right" => $value), array("id" => $id));
  }
}
