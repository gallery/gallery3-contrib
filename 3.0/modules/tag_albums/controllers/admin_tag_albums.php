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

class Admin_Tag_Albums_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tag_albums.html");

    // Generate a form for the admin Settings.
    $view->content->tag_albums_form = $this->_get_admin_form();

    // Display the page.
    print $view;
  }

  private function _get_admin_form() {
    $form = new Forge("admin/tag_albums/saveprefs", "", "post",
                      array("id" => "g-tag-albums-admin-form"));

    $tag_albums_tagsort_group = $form->group("Tag_Albums_Tag_Sort")->label(t("\"All Tags\" Album Preferences"));
    $tag_albums_tagsort_group->input("tag_page_title")
      ->label(t("Page Title"))
      ->value(module::get_var("tag_albums", "tag_page_title"));
    $tag_albums_tagsort_group->dropdown("tag_index")
      ->label(t("Tag album's index should display:"))
      ->options(
        array("default" => "(default) Individual Tag Albums", 
              "tagcloudpage" => "Tag Cloud Page Module", 
              "alltags" => "All Tags Module"))
      ->selected(module::get_var("tag_albums", "tag_index"));

    $tag_albums_tagsort_group->dropdown("tag_sort_by")
      ->label(t("Sort \"All Tags\" Albums By:"))
      ->options(
        array("name" => "Name", 
              "count" => "Count", 
              "id" => "ID Number"))
      ->selected(module::get_var("tag_albums", "tag_sort_by"));
    $tag_albums_tagsort_group->dropdown("tag_sort_direction")
      ->label(t("Display Albums In:"))
      ->options(
        array("ASC" => "Ascending Order", 
              "DESC" => "Descending"))
      ->selected(module::get_var("tag_albums", "tag_sort_direction"));

    $tag_index_scope_options["tag_index_scope"] = Array(t("Use tag album index setting for \"*\" albums as well?"), module::get_var("tag_albums", "tag_index_scope"));
    $tag_albums_tagsort_group->checklist("tag_index_scope")
      ->options($tag_index_scope_options);

    $tag_index_filter_top_options["tag_index_filter_top"] = Array(t("Display filter links on the top of \"All Tags\" album pages?"), module::get_var("tag_albums", "tag_index_filter_top"));
    $tag_albums_tagsort_group->checklist("tag_index_filter_top")
      ->options($tag_index_filter_top_options);

    $tag_index_filter_bottom_options["tag_index_filter_bottom"] = Array(t("Display filter links on the bottom of \"All Tags\" album pages?"), module::get_var("tag_albums", "tag_index_filter_bottom"));
    $tag_albums_tagsort_group->checklist("tag_index_filter_bottom")
      ->options($tag_index_filter_bottom_options);

    $tag_albums_tagitemsort_group = $form->group("Tag_Albums_Tag_Item_Sort")->label(t("\"All Tags\" Sub-Album Preferences"));
    $tag_albums_tagitemsort_group->dropdown("subalbum_sort_by")
      ->label(t("Sort Contents of Sub-Albums By:"))
      ->options(
        array("title" => "Title", 
              "name" => "File name", 
              "captured" => "Date captured", 
              "created" => "Date uploaded", 
              "updated" => "Date modified", 
              "view_count" => "Number of views"))
      ->selected(module::get_var("tag_albums", "subalbum_sort_by"));
    $tag_albums_tagitemsort_group->dropdown("subalbum_sort_direction")
      ->label(t("Display Contents of Sub-Albums In:"))
      ->options(
        array("ASC" => "Ascending Order", 
              "DESC" => "Descending"))
      ->selected(module::get_var("tag_albums", "subalbum_sort_direction"));

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    $form = $this->_get_admin_form();
    if ($form->validate()) {
      Kohana_Log::add("error",print_r($form,1));
      module::set_var("tag_albums", "tag_page_title", $form->Tag_Albums_Tag_Sort->tag_page_title->value);
      module::set_var("tag_albums", "tag_index", $form->Tag_Albums_Tag_Sort->tag_index->value);
      module::set_var("tag_albums", "tag_index_scope", count($form->Tag_Albums_Tag_Sort->tag_index_scope->value));
      module::set_var("tag_albums", "tag_index_filter_top", count($form->Tag_Albums_Tag_Sort->tag_index_filter_top->value));
      module::set_var("tag_albums", "tag_index_filter_bottom", count($form->Tag_Albums_Tag_Sort->tag_index_filter_bottom->value));
      module::set_var("tag_albums", "tag_sort_by", $form->Tag_Albums_Tag_Sort->tag_sort_by->value);
      module::set_var("tag_albums", "tag_sort_direction", $form->Tag_Albums_Tag_Sort->tag_sort_direction->value);
      module::set_var("tag_albums", "subalbum_sort_by", $form->Tag_Albums_Tag_Item_Sort->subalbum_sort_by->value);
      module::set_var("tag_albums", "subalbum_sort_direction", $form->Tag_Albums_Tag_Item_Sort->subalbum_sort_direction->value);
      message::success(t("Your settings have been saved."));

      url::redirect("admin/tag_albums");
    }

    // Else show the page with errors
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_tag_albums.html");
    $view->content->tag_albums_form = $form;
    print $view;
  }
}
