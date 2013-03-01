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
class Admin_Movie_Tools_Controller extends Admin_Controller {
  public function index() {
    // Print screen from new form.
    $form = $this->_get_admin_form();
    $this->_print_view($form);
  }

  public function save() {
    access::verify_csrf();
    $form = $this->_get_admin_form();
    if ($form->validate()) {
      $formats = movie_tools::get_formats();
      foreach ($formats as $id => $data) {
        module::set_var("movie_tools", "allow_$id", ($form->formats->{$id}->value == 1));
      }
      module::set_var("movie_tools", "custom_formats",
        movie_tools::formats_string_to_json($form->formats->custom_formats->value));
      module::set_var("gallery", "movie_extract_frame_time",
        $form->thumbs->extract_frame_time->value);
      // All done - redirect with message.
      message::success(t("Movie tools settings updated successfully"));
      url::redirect("admin/movie_tools");
    }
    // Something went wrong - print view from existing form.
    $this->_print_view($form);
  }

  private function _print_view($form) {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Movie tools");
    $view->content = new View("admin_movie_tools.html");
    $view->content->form = $this->_get_admin_form();
    $view->content->formats =
      array_merge(movie_tools::get_default_formats(), movie_tools::get_formats());
    print $view;
  }

  private function _get_admin_form() {
    $form = new Forge("admin/movie_tools/save", "", "post", array("id" => "g-movie-tools-admin-form"));

    $formats = movie_tools::get_formats();
    $settings_formats = $form->group("formats")->label(t("Movie format supported"));
    foreach ($formats as $id => $data) {
      $settings_formats->checkbox($id)
        ->label($data["name"])
        ->checked(module::get_var("movie_tools", "allow_$id", false));
    }
    $settings_formats->input("custom_formats")
      ->label(t("Additional movie formats (enter using the same formatting as the table above)"))
      ->callback(array($this, "_validate_custom_formats"))
      ->error_messages("valid_custom_formats", t("You must enter valid formats like the examples in the table"))
      ->value(movie_tools::formats_json_to_string(module::get_var("movie_tools", "custom_formats", "")));

    $settings_thumbs = $form->group("thumbs")->label(t("Movie thumbnails"));
    $settings_thumbs->input("extract_frame_time")
      ->label(t("Seconds from start of movie at which thumbnails are extracted (default: 3)"))
      ->rules("required|valid_numeric")
      ->callback(array($this, "_validate_extract_frame_time"))
      ->error_messages("required", t("You must enter a number"))
      ->error_messages("valid_numeric", t("You must enter a number"))
      ->error_messages("valid_min", t("The value cannot be negative"))
      ->value(module::get_var("gallery", "movie_extract_frame_time", 3));

    $form->submit("save")->value(t("Save"));
    return $form;
  }

  function _validate_extract_frame_time($input) {
    if ($input->value < 0) {
      $input->add_error("valid_min", true);
    }
  }

  function _validate_custom_formats($input) {
    if ($input->value && !movie_tools::formats_string_to_array($input->value)) {
      // Input isn't empty, but doesn't parse correctly - it's invalid.
      $input->add_error("valid_custom_formats", true);
    }
  }
}
