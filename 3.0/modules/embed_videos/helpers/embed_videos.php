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
 * This is the API for handling embedded videos.
 *
 * Note: by design, this class does not do any permission checking.
 */
class embed_videos_Core {

  static function get_add_form($album) {
    $form = new Forge("embedded_videos/create/{$album->id}", "", "post", array("id" => "g-add-embed-form"));
    $group = $form->group("add_embedded_video")
    ->label(t("Add embedded video to %album_title", array("album_title" => $album->title)));
    $group->input("title")->label(t("Title"))
    ->error_messages("required", t("You must provide a title"))
    ->error_messages("length", t("Your title is too long"));
    $group->input("video_url")->label(t("Video URL"))
    ->error_messages(
        "conflict", t("There is already a movie with this ID"))
    ->error_messages("required", t("You must provide a URL"))
    ->error_messages("invalid_id", t("Invalid URL"));
    $group->textarea("description")->label(t("Description"));
    $group->input("slug")->label(t("Internet Address"))
    ->error_messages(
        "conflict", t("There is already a movie, photo or album with this internet address"))
    ->error_messages(
        "not_url_safe",
    t("The internet address should contain only letters, numbers, hyphens and underscores"))
    ->error_messages("required", t("You must provide an internet address"))
    ->error_messages("length", t("Your internet address is too long"));

    module::event("item_add_form", $album, $form);

    $group = $form->group("buttons")->label("");
    $group->submit("")->value(t("Add"));

    return $form;
  }
}