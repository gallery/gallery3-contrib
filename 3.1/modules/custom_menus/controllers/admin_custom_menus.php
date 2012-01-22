<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class Admin_Custom_Menus_Controller extends Admin_Controller {
  public function index() {
    // Display the admin page, which contains a list of existing menu items.
    $view = new Admin_View("admin.html");
    $view->page_title = t("Manage menus");
    $view->content = new View("admin_custom_menus.html");
    $view->content->menu_list = $this->get_html_list(0);
    print $view;
  }

  public function form_create($id) {
    // Display the create new menu form.
    print $this->get_new_menu_form($id);
  }

  public function form_edit($id) {
    // Display the edit menu form.
    print $this->get_edit_menu_form($id);
  }

  static function get_new_menu_form($id) {
    // Generate the create new menu form.
    $form = new Forge("admin/custom_menus/create/$id", "", "post", array("id" => "g-create-menu-form"));
    $group = $form->group("create_menu")
             ->label(t("Add new menu"));
    $group->input("menu_title")
          ->label(t("Title"));
    $group->input("menu_url")
          ->label(t("URL (Leave blank if this menu will have sub-menus)"));
    $group->submit("")->value(t("Create menu"));
    return $form;
  }
  
  static function get_edit_menu_form($id) {
    // Generate the edit menu form.
    $existing_menu = ORM::factory("custom_menu", $id);
    $form = new Forge("admin/custom_menus/edit/$id", "", "post", array("id" => "g-edit-menu-form"));
    $group = $form->group("edit_menu")
             ->label(t("Edit menu"));
    $group->input("menu_title")
          ->label(t("Title"))
          ->value($existing_menu->title);
    $group->input("menu_url")
          ->label(t("URL (Leave blank if this menu will have sub-menus)"))
          ->value($existing_menu->url);
    $group->submit("")->value(t("Save changes"));
    return $form;
  }
  
  public function create($id) {
    // Save a new menu to the database.

    access::verify_csrf();

    // Save form variables to the database.
    $new_menu = ORM::factory("custom_menu");
    $new_menu->title = Input::instance()->post("menu_title");
    $new_menu->url = Input::instance()->post("menu_url");
    $new_menu->parent_id = $id;

    // Set menu's location to the last position.
    $existing_menu = ORM::factory("custom_menu")
                     ->where("parent_id", "=", $id)
                     ->order_by("order_by", "DESC")
                     ->find_all(1);
    if (count($existing_menu) > 0) {
      $int_position = $existing_menu[0]->order_by;
      $int_position++;
      $new_menu->order_by = $int_position;
    } else {
      $new_menu->order_by = 0;
    }

    // Save new menu to the database.
    $new_menu->save();
    message::success(t("Menu %menu_name created", array("menu_name" => $new_menu->title)));
    log::success("custom_menus", t("Menu %menu_name created", array("menu_name" => $new_menu->title)));
    json::reply(array("result" => "success"));
  }

  public function edit($id) {
    // Save a new menu to the database.

    access::verify_csrf();

    // Load the existing menu and save changes.
    $existing_menu = ORM::factory("custom_menu", $id);
    if ($existing_menu->loaded()) {
      $existing_menu->title = Input::instance()->post("menu_title");
      $existing_menu->url = Input::instance()->post("menu_url");
      $existing_menu->save();
      message::success(t("Menu %menu_name saved", array("menu_name" => $existing_menu->title)));
      log::success("custom_menus", t("Menu %menu_name saved", array("menu_name" => $existing_menu->title)));
      json::reply(array("result" => "success"));
    } else {
      message::error(t("Unable to load menu %menu_id", array("menu_id" => $id)));
      log::success("custom_menus", t("Unable to load menu %menu_id", array("menu_id" => $id)));
      json::reply(array("result" => "success"));
    }
  }

  function get_html_list($parent_id) {
    // Generate an HTML list of existing menu items.
    $existing_menu = ORM::factory("custom_menu")
                     ->where("parent_id", "=", $parent_id)
                     ->order_by("order_by", "ASC")
                     ->find_all();
    $str_html = "";
    if (count($existing_menu) > 0) {
      $str_html = "<ul style=\"margin-bottom: 0em; margin-left: 2.5em;\">\n";
      foreach ($existing_menu as $one_menu) {
        $str_html .= "<li style=\"list-style:disc outside none; margin: 1em; line-height: 1em;\">" . $one_menu->title . 
                     " <a href=\"" . url::site("admin/custom_menus/form_create/" . $one_menu->id) . 
                     "\" class=\"g-dialog-link ui-icon-plus g-button ui-icon-left\" title=\"" . t("Add sub menu") . 
                     "\"><span class=\"ui-icon ui-icon-plus\"></span></a>" .
                     " <a href=\"" . url::site("admin/custom_menus/form_edit/" . $one_menu->id) . 
                     "\" class=\"g-dialog-link ui-icon-pencil g-button ui-icon-left\" title=\"" . t("Edit menu") . 
                     "\"><span class=\"ui-icon ui-icon-pencil\"></span></a>" .
                     " <a href=\"" . url::site("admin/custom_menus/form_delete/" . $one_menu->id) . 
                     "\" class=\"g-dialog-link ui-icon-trash g-button ui-icon-left\" title=\"" . t("Delete menu") . 
                     "\"><span class=\"ui-icon ui-icon-trash\"></span></a>" .
                     " <a href=\"" . url::site("admin/custom_menus/move_menu_up/" . $one_menu->id) . 
                     "\" class=\"g-button ui-icon-left\" title=\"" . t("Move menu up") . 
                     "\">^</a>" .
                     " <a href=\"" . url::site("admin/custom_menus/move_menu_down/" . $one_menu->id) . 
                     "\" class=\"g-button ui-icon-left\" title=\"" . t("Move menu down") . 
                     "\">v</a>" .
                     "</li>\n";
        $str_html .= $this->get_html_list($one_menu->id);
      }
      $str_html .= "</ul>\n";
    }
    return $str_html;
  }

  public function form_delete($id) {
    // Display a form asking the user if they want to delete a menu.
    $one_menu = ORM::factory("custom_menu", $id);
    if ($one_menu->loaded()) {
      print $this->get_delete_form($one_menu);
    }
  }

  public function delete($id) {
    // Delete the specified menu.

    access::verify_csrf();

    // Make sure $id belongs to an actual menu.
    $one_menu = ORM::factory("custom_menu", $id);
    if (!$one_menu->loaded()) {
      throw new Kohana_404_Exception();
    }

    // If the form validates, delete the specified menu.
    $form = $this->get_delete_form($one_menu);
    if ($form->validate()) {
      $name = $one_menu->title;
      $this->delete_sub_menus($one_menu->id);
      $one_menu->delete();
      message::success(t("Deleted menu %menu_name", array("menu_name" => $name)));
      log::success("custom_menus", t("Deleted menu %menu_name", array("menu_name" => $name)));
      json::reply(array("result" => "success", "location" => url::site("admin/custom_menus")));
    } else {
      print $form;
    }
  }

  function delete_sub_menus($parent_id) {
    // Delete all sub menus associated with $parent_id.
    $existing_menu = ORM::factory("custom_menu")
                     ->where("parent_id", "=", $parent_id)
                     ->order_by("title", "ASC")
                     ->find_all();
    foreach ($existing_menu as $one_menu) {
      $this->delete_sub_menus($one_menu->id);
	  $one_menu->delete();
    }
  }

  static function get_delete_form($one_menu) {
    // Generate a new form asking the user if they want to delete a menu.
    $form = new Forge("admin/custom_menus/delete/$one_menu->id", "", "post", array("id" => "g-delete-menu-form"));
    $group = $form->group("delete_menu")
      ->label(t("Really delete menu %menu_name & sub-menus?", array("menu_name" => $one_menu->title)));
    $group->submit("")->value(t("Delete Menu"));
    return $form;
  }

  public function move_menu_up($id) {
    // Move the specified menu item up one position.
    $one_menu = ORM::factory("custom_menu", $id);
    if ($one_menu->loaded()) {
      $existing_menu = ORM::factory("custom_menu")
                       ->where("parent_id", "=", $one_menu->parent_id)
                       ->where("order_by", "<", $one_menu->order_by)
                       ->order_by("order_by", "DESC")
                       ->find_all(1);
      if (count($existing_menu) > 0) {
        $second_menu = ORM::factory("custom_menu", $existing_menu[0]->id);
	    $temp_position = $one_menu->order_by;
		$one_menu->order_by = $second_menu->order_by;
		$second_menu->order_by = $temp_position;
        $one_menu->save();
        $second_menu->save();
        message::success(t("Menu %menu_title moved up", array("menu_title" => $one_menu->title)));
        log::success("custom_menus", t("Menu %menu_title moved up", array("menu_title" => $one_menu->title)));
      }
    }
    url::redirect("admin/custom_menus");
  }

  public function move_menu_down($id) {
    // Move the specified menu item down one position.
    $one_menu = ORM::factory("custom_menu", $id);
    if ($one_menu->loaded()) {
      $existing_menu = ORM::factory("custom_menu")
                       ->where("parent_id", "=", $one_menu->parent_id)
                       ->where("order_by", ">", $one_menu->order_by)
                       ->order_by("order_by", "ASC")
                       ->find_all(1);
      if (count($existing_menu) > 0) {
        $second_menu = ORM::factory("custom_menu", $existing_menu[0]->id);
	    $temp_position = $one_menu->order_by;
		$one_menu->order_by = $second_menu->order_by;
		$second_menu->order_by = $temp_position;
        $one_menu->save();
        $second_menu->save();
        message::success(t("Menu %menu_title moved down", array("menu_title" => $one_menu->title)));
        log::success("custom_menus", t("Menu %menu_title moved down", array("menu_title" => $one_menu->title)));
      }
    }
    url::redirect("admin/custom_menus");
  }
}
