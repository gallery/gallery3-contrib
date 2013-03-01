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
class Admin_Quotas_Controller extends Admin_Controller {
  public function index() {
    // Set up a new admin page for the quotas module.
    $view = new Admin_View("admin.html");
    $view->page_title = t("Users and groups");
    $view->page_type = "collection";
    $view->page_subtype = "admin_users_quotas";
    $view->content = new View("admin_quotas.html");

    $page_size = module::get_var("user", "page_size", 10);
    $page = Input::instance()->get("page", "1");
    $builder = db::build();
    $user_count = $builder->from("users")->count_records();

    $view->page = $page;
    $view->page_size = $page_size;
    $view->children_count = $user_count;
    $view->max_pages = ceil($view->children_count / $view->page_size);

    $view->content->pager = new Pagination();
    $view->content->pager->initialize(
      array("query_string" => "page",
            "total_items" => $user_count,
            "items_per_page" => $page_size,
            "style" => "classic"));

    if ($page < 1) {
      url::redirect(url::merge(array("page" => 1)));
    } else if ($page > $view->content->pager->total_pages) {
      url::redirect(url::merge(array("page" => $view->content->pager->total_pages)));
    }

    $view->content->users = ORM::factory("user")
      ->order_by("users.name", "ASC")
      ->find_all($page_size, $view->content->pager->sql_offset);
    $view->content->groups = ORM::factory("group")->order_by("name", "ASC")->find_all();
    $view->content->quota_options = $this->_get_quota_settings_form();
    print $view;
  }

  public function form_group_quota($id) {
    // Display the form for setting a quota for the specified group ($id).
    $group = ORM::factory("group", $id);
    if (empty($group)) {
      throw new Kohana_404_Exception();
    }
    print $this->_get_edit_group_quota($group);
  }

  static function _get_edit_group_quota($group) {
    // Generate a form for setting a quota for the specified group ($group).
    $record = ORM::factory("groups_quota")->where("group_id", "=", $group->id)->find();
    $form = new Forge(
      "admin/quotas/edit_quota/$group->id", "", "post", array("id" => "g-edit-quota-form"));
    $group = $form->group("edit_quota")->label(t("Edit group quota"));
    $group->input("group_quota")->label(t("Limit (MB)"))->id("g-group_quota")->value($record->storage_limit / 1024 / 1024)
      ->error_messages("required", t("A value is required"));

    $group->submit("")->value(t("Save"));
    return $form;
  }

  public function edit_quota($id) {
    // Save the specified quota to the database.
    access::verify_csrf();

    $group = ORM::factory("group", $id);
    if (empty($group)) {
      throw new Kohana_404_Exception();
    }

    $record = ORM::factory("groups_quota")->where("group_id", "=", $group->id)->find();
    $form = $this->_get_edit_group_quota($group);
    try {
      $valid = $form->validate();
      $record->group_id = $id;
      $record->storage_limit = $form->edit_quota->inputs["group_quota"]->value * 1024 * 1024;
    } catch (ORM_Validation_Exception $e) {
      // Translate ORM validation errors into form error messages
      foreach ($e->validation->errors() as $key => $error) {
        $form->edit_quota->inputs[$key]->add_error($error, 1);
      }
      $valid = false;
    }

    if ($valid) {
      $record->save();
      message::success(t("Limit for group %group_name set", array("group_name" => $group->name)));
      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string) $form));
    }
  }

  private function _get_quota_settings_form() {
    // Make a new form to allow the admin to specify how the system should calculate a user's quota.
    $form = new Forge("admin/quotas/saveprefs", "", "post",
                      array("id" => "g-quotas-admin-form"));

    // Setup a checkbox for the form.
    $quota_options["use_all_sizes"] = array(t("Count resizes and thumbnails towards a users limit?"), module::get_var("quotas", "use_all_sizes"));
    $add_links = $form->group("quota_preferences");
    $add_links->checklist("quota_preferences_list")
      ->options($quota_options);

    // Add a save button to the form.
    $form->submit("save_preferences")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out which boxes where checked
    $checkboxes_array = Input::instance()->post("quota_preferences_list");
    $use_all_sizes = false;
    for ($i = 0; $i < count($checkboxes_array); $i++) {
      if ($checkboxes_array[$i] == "use_all_sizes") {
        $use_all_sizes = true;
      }
    }

    // Save Settings.
    module::set_var("quotas", "use_all_sizes", $use_all_sizes);
    message::success(t("Your Selection Has Been Saved."));

    // Load Admin page.
    url::redirect("admin/quotas");
  }
}
