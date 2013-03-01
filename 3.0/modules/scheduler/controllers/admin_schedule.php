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
class Admin_Schedule_Controller extends Admin_Maintenance_Controller {
  /**
   * Show a list of all available, running and finished tasks.
   */
  public function index() {
    $query = db::build()
      ->update("tasks")
      ->set("state", "stalled")
      ->where("done", "=", 0)
      ->where("state", "<>", "stalled")
      ->where(new Database_Expression("UNIX_TIMESTAMP(NOW()) - `updated` > 15"))
      ->execute();
    $stalled_count = $query->count();
    if ($stalled_count) {
      log::warning("tasks",
                   t2("One task is stalled",
                      "%count tasks are stalled",
                      $stalled_count),
                   t('<a href="%url">view</a>',
                     array("url" => html::mark_clean(url::site("admin/maintenance")))));
    }

    $view = new Admin_View("admin.html");
    $view->page_title = t("Maintenance tasks");
    $view->content = new View("admin_schedule.html");
    $view->content->task_definitions = task::get_definitions();
    $view->content->running_tasks = ORM::factory("task")
      ->where("done", "=", 0)->order_by("updated", "DESC")->find_all();
    $view->content->finished_tasks = ORM::factory("task")
      ->where("done", "=", 1)->order_by("updated", "DESC")->find_all();
    $view->content->schedule_definitions = scheduler::get_definitions();
    print $view;
  }

  public function form_add($task_callback) {
    access::verify_csrf();

    $schedule = ORM::factory("schedule");
    $schedule->task_callback = $task_callback;
    $schedule->next_run_datetime = time();
    $v = new View("admin_schedule_form.html");
    $v->form = scheduler::get_form("define", $schedule);
    $v->method = "define";
    print $v;
  }

  public function form_edit($id) {
    access::verify_csrf();

    $schedule = ORM::factory("schedule", $id);
    $v = new View("admin_schedule_form.html");
    $v->form = scheduler::get_form("update", $schedule);
    $v->method = "update";
    print $v;
  }

  public function remove_form($id) {
    access::verify_csrf();

    $schedule = ORM::factory("schedule", $id);

    $v = new View("admin_schedule_confirm.html");
    $v->name = $schedule->name;
    $v->form = new Forge("admin/schedule/remove_event/{$id}", "", "post",
                         array("id" => "g-remove-schedule"));
    $group = $v->form->group("remove");
    $group->submit("")->value(t("Continue"));
    print $v;
  }

  public function remove_event($id) {
    access::verify_csrf();
    $schedule = ORM::factory("schedule", $id);
    $schedule->delete();

    message::success(t("Removed scheduled task: %name", array("name" => $schedule->name)));
    json::reply(array("result" => "success", "reload" => 1));
  }

  public function define() {
    $this->_handle_request("define");
   }

  public function update($id=null) {
    $this->_handle_request("update", $id);
  }

  private function _handle_request($method, $id=null) {
    $schedule = ORM::factory("schedule", $id);
    $form = scheduler::get_form($method, $schedule);
    $valid = $form->validate();
    if ($valid) {
      $schedule->name = $form->schedule_group->schedule_name->value;
      $schedule->interval = $form->schedule_group->interval->value;
      $schedule->next_run_datetime =
        $this->_start_date($form->schedule_group->run_date->dow->selected,
                           $form->schedule_group->run_date->time->value);
      $schedule->task_callback = $form->schedule_group->callback->value;
      $schedule->save();
      if ($method == "define") {
        message::success(t("Added scheduled task: %name", array("name" => $schedule->name)));
      } else {
        message::success(t("Updated scheduled task: %name", array("name" => $schedule->name)));
      }
      json::reply(array("result" => "success", "reload" => 1));
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }

  private function _start_date($dow, $time) {
    list ($hour, $minutes) = explode(":", $time);
    $local_time = localtime();
    $days = ($dow < $local_time[6] ? 7 : 0) + $dow - $local_time[6];
    return
      mktime($hour, $minutes, 0, $local_time[4] + 1, $local_time[3] + $days, 1900 + $local_time[5]);
  }
}
