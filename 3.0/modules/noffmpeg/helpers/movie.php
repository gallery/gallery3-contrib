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

/**
 * This is the API for handling movies.
 *
 * Note: by design, this class does not do any permission checking.
 */
class movie_Core {
  static function get_edit_form($movie) {
    $form = new Forge("movies/update/$movie->id", "", "post", array("id" => "g-edit-movie-form"));
    $form->hidden("from_id")->value($movie->id);
    $group = $form->group("edit_item")->label(t("Edit Movie"));
    $group->input("title")->label(t("Title"))->value($movie->title)
      ->error_messages("required", t("You must provide a title"))
      ->error_messages("length", t("Your title is too long"));
    $group->textarea("description")->label(t("Description"))->value($movie->description);
    $group->input("name")->label(t("Filename"))->value($movie->name)
      ->error_messages(
        "conflict", t("There is already a movie, photo or album with this name"))
      ->error_messages("no_slashes", t("The movie name can't contain a \"/\""))
      ->error_messages("no_trailing_period", t("The movie name can't end in \".\""))
      ->error_messages("illegal_data_file_extension", t("You cannot change the movie file extension"))
      ->error_messages("required", t("You must provide a movie file name"))
      ->error_messages("length", t("Your movie file name is too long"));
    $group->input("slug")->label(t("Internet Address"))->value($movie->slug)
      ->error_messages(
        "conflict", t("There is already a movie, photo or album with this internet address"))
      ->error_messages(
        "not_url_safe",
        t("The internet address should contain only letters, numbers, hyphens and underscores"))
      ->error_messages("required", t("You must provide an internet address"))
      ->error_messages("length", t("Your internet address is too long"));

    module::event("item_edit_form", $movie, $form);

    $group = $form->group("buttons")->label("");
    $group->submit("")->value(t("Modify"));

    return $form;
  }

  static function extract_frame($input_file, $output_file) {
    // rWatcher Edit:  Just copy the generic thumb instead of extracting a frame.
    copy(MODPATH . "noffmpeg/images/missing_movie.png", $output_file);
  }

  /**
   * Return the path to the ffmpeg binary if one exists and is executable, or null.
   */
  static function find_ffmpeg() {
    // rWatcher Edit:  Return true to trick the system into thinking ffmpeg is present.
    return true;
  }

  /**
   * Return the width, height, mime_type and extension of the given movie file.
   */
  static function get_file_metadata($file_path) {
    // rWatcher Edit:  Use FLVMetaData lib instead of ffmpeg for .flv files.
    //  For other files, just set a 320x240 default video resolution.
    $pi = pathinfo($file_path);
    $extension = isset($pi["extension"]) ? $pi["extension"] : "flv"; // No extension?  Assume FLV.
    $mime_type = in_array(strtolower($extension), array("mp4", "m4v")) ?
      "video/mp4" : "video/x-flv";
    $vid_width = 320;
    $vid_height = 240;
    if (strtolower($extension) == "flv") {
      $flvinfo = new FLVMetaData($file_path);
      $info = $flvinfo->getMetaData();
      if (($info["width"] != "") && ($info["height"] != "")) {
        $vid_width = $info["width"];
        $vid_height = $info["height"];
      }
    }
    return array($vid_width, $vid_height, $mime_type, $extension);
  }
}
