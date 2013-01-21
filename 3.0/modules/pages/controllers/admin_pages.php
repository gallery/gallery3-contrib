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
class Admin_Pages_Controller extends Admin_Controller {
  public function index() {
    // Display the admin page.
    $view = new Admin_View("admin.html");
    $view->page_title = t("Manage pages");
    $view->content = new View("admin_pages.html");
    $query = ORM::factory("static_page");
    $view->content->pages = $query->order_by("name", "ASC")->find_all();
    $view->content->form = $this->get_prefs_form();
    print $view;
  }

  public function createpage() {
    // Display a form for creating a new page.
    $view = new Admin_View("admin.html");
    $view->page_title = t("Create page");
    $view->content = new View("admin_pages_new.html");
    $view->content->form = $this->get_new_page_form();
    print $view;
  }

  public function editpage($id) {
    // Display a form for editing an existing page.
    $existing_page = ORM::factory("static_page", $id);
    $view = new Admin_View("admin.html");
    $view->page_title = t("Edit page");
    $view->content = new View("admin_pages_new.html");
    $view->content->form = $this->get_edit_page_form($existing_page);
    print $view;
  }

  public function savepage() {
    // Save a page to the database.

    access::verify_csrf();

    // Store form values into variables.
    $page_id = Input::instance()->post("page_id");
    $page_name = urlencode(trim(Input::instance()->post("page_name")));
    $page_title = Input::instance()->post("page_title");
    $page_code = stripslashes($_REQUEST["page_code"]); // access var directly to get around xss filtering.
    $display_menu = Input::instance()->post("display_menu");

    // If $page_id is set, update an existing page.
    if (isset($page_id)) {
      $update_page = ORM::factory("static_page", $page_id);
      $update_page->title = $page_title;
      $update_page->html_code = $page_code;
      $update_page->display_menu = $display_menu;
      $update_page->save();
      message::success(t("Page %page_name updated", array("page_name" => $update_page->name)));
      log::success("pages", t("Page %page_name updated", array("page_name" => $update_page->name)));
      url::redirect("admin/pages");
    } else {

      // If $page_id is not set, we are dealing with a new page.
      // Check and make sure a page with the same names doesn't already exist.
      $existing_page = ORM::factory("static_page")
                       ->where("name", "=", $page_name)
                       ->find_all();

      // If the page doesn't exist, save it to the database.
      if (count($existing_page) == 0) {
        $new_page = ORM::factory("static_page");
        $new_page->name = $page_name;
        $new_page->title = $page_title;
        $new_page->html_code = $page_code;
        $new_page->display_menu = $display_menu;
        $new_page->save();
        message::success(t("Page %page_name created", array("page_name" => $page_name)));
        log::success("pages", t("Page %page_name created", array("page_name" => $page_name)));
        url::redirect("admin/pages");
      } else {

        // If the page does exist, ask the user if they want to overwrite the old page with the new one.
        message::error(t("Page %page_name already exists, press Save again to overwrite.", array("page_name" => $page_name)));
        $view = new Admin_View("admin.html");
        $view->page_title = t("Edit page");
        $view->content = new View("admin_pages_new.html");
        $view->content->form = $this->get_overwrite_page_form($existing_page[0]->id, $page_name, $page_title, $page_code, $display_menu);
        print $view;
      }
    }
  }

  public function form_delete($id) {
    // Display a form asking the user if they want to delete a page.
    $one_page = ORM::factory("static_page", $id);
    if ($one_page->loaded()) {
      print $this->get_delete_form($one_page);
    }
  }

  public function delete($id) {
    // Delete the specified page.

    access::verify_csrf();

    // Make sure $id belongs to an actual page.
    $one_page = ORM::factory("static_page", $id);
    if (!$one_page->loaded()) {
      throw new Kohana_404_Exception();
    }

    // If the form validates, delete the specified page.
    $form = $this->get_delete_form($one_page);
    if ($form->validate()) {
      $name = $one_page->name;
      $one_page->delete();
      message::success(t("Deleted page %page_name", array("page_name" => $name)));
      log::success("pages", t("Deleted page %page_name", array("page_name" => $name)));
      json::reply(array("result" => "success", "location" => url::site("admin/pages")));
    } else {
      print $form;
    }
  }

  public function form_rename($id) {
    // Display a form to allow the user to rename a page.
    $one_page = ORM::factory("static_page", $id);
    if ($one_page->loaded()) {
      print InPlaceEdit::factory(urldecode($one_page->name))
        ->action("admin/pages/rename/$id")
        ->render();
    }
  }

  public function rename($id) {
    // Rename an existing page.
    access::verify_csrf();

    // Make sure the page specified by $id exists.
    $one_page = ORM::factory("static_page", $id);
    if (!$one_page->loaded()) {
      throw new Kohana_404_Exception();
    }

    $in_place_edit = InPlaceEdit::factory($one_page->name)
      ->action("admin/pages/rename/$one_page->id")
      ->rules(array("required", "length[1,64]"));

    // If the form validates, and if the new name doesn't already exist, rename the page.
    if ($in_place_edit->validate()) {
      $old_name = $one_page->name;
      $new_name = urlencode(trim($in_place_edit->value()));
      $new_name_exists = ORM::factory("static_page")->where("name", "=", $new_name)->find_all();
      if (count($new_name_exists) == 0) {
        $one_page->name = $new_name;
        $one_page->save();
        $message = t("Renamed page <i>%old_name</i> to <i>%new_name</i>",
                     array("old_name" => $old_name, "new_name" => $new_name));
        message::success($message);
        log::success("pages", $message);
        json::reply(array("result" => "success", "location" => url::site("admin/pages")));
      } else {
        json::reply(array("result" => "error", "form" => (string)$in_place_edit->render()));
      }
    } else {
      json::reply(array("result" => "error", "form" => (string)$in_place_edit->render()));
    }
  }

  static function get_delete_form($one_page) {
    // Generate a new form asking the user if they want to delete a page.
    $form = new Forge("admin/pages/delete/$one_page->id", "", "post", array("id" => "g-delete-pages-form"));
    $group = $form->group("delete_page")
      ->label(t("Really delete page %page_name?", array("page_name" => $one_page->name)));
    $group->submit("")->value(t("Delete Page"));
    return $form;
  }

  private function get_new_page_form() {
    // Generate a form for creating a new page.
    $form = new Forge("admin/pages/savepage", "", "post",
                      array("id" => "g-pages-admin-form"));

    $pages_group = $form->group("new_page");
    $pages_group->input("page_name")
                ->label(t("Name"));
    $pages_group->input("page_title")
                ->label(t("Title"));
    $pages_group->textarea("page_code")
                ->label(t("HTML Code"));
    $pages_group->checkbox("display_menu")
                ->label(t("Display in menu?"))
                ->checked(false);
    $pages_group->submit("save_page")
                ->value(t("Save"));

    return $form;
  }

  private function get_overwrite_page_form($id, $name, $title, $html_code, $display_menu) {
    // Generate a form for overwriting an existing page.
    $form = new Forge("admin/pages/savepage", "", "post",
                      array("id" => "g-pages-admin-form"));

    $pages_group = $form->group("new_page");
    $pages_group->hidden("page_id")
                ->value($id);
    $pages_group->input("page_name")
                ->label(t("Name"))
                ->readonly()
                ->value($name);
    $pages_group->input("page_title")
                ->label(t("Title"))
                ->value($title);
    $pages_group->textarea("page_code")
                ->label(t("HTML Code"))
                ->value($html_code);
    $pages_group->checkbox("display_menu")
                ->label(t("Display in menu?"))
                ->checked($display_menu);
    $pages_group->submit("save_page")
                ->value(t("Save"));

    return $form;
  }

  private function get_edit_page_form($existing_page) {
    // Generate a form for editing an existing page.  Reuse the overwrite form for as it's basically the same thing.
    return ($this->get_overwrite_page_form($existing_page->id, $existing_page->name, $existing_page->title, $existing_page->html_code, $existing_page->display_menu));
  }

  private function get_prefs_form() {
    // Generate a form for global preferences.
    $form = new Forge("admin/pages/saveprefs", "", "post",
                      array("id" => "g-pages-admin-form"));

    $pages_group = $form->group("preferences")->label(t("Settings"));
    $pages_group->checkbox("display_sidebar")
                ->label(t("Hide sidebar on Pages?"))
                ->checked(module::get_var("pages", "show_sidebar"));
    $pages_group->checkbox("disable_rich_editor")
                ->label(t("Disable rich text editor?"))
                ->checked(module::get_var("pages", "disable_rte"));
    $pages_group->submit("save_prefs")
                ->value(t("Save"));

    return $form;
  }

  public function saveprefs() {
    // Save a preferences to the database.

    access::verify_csrf();

    // Save form variables.
    module::set_var("pages", "show_sidebar", Input::instance()->post("display_sidebar"));
    module::set_var("pages", "disable_rte", Input::instance()->post("disable_rich_editor"));

    // Display message and load main pages admin screen.
    message::success(t("Your settings have been saved."));
    url::redirect("admin/pages");
  }
}
