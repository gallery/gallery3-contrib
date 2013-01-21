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
class tag_albums_event_Core {
  static function pre_deactivate($data) {
    // Warn the user that the Tags module is required.
    if ($data->module == "tag") {
      $data->messages["warn"][] = t("The Tag Albums module requires the Tags module.");
    }
  }

  static function module_change($changes) {
    // See if the Tags module is installed,
    //   tell the user to install it if it isn't.
    if (!module::is_active("tag") || in_array("tag", $changes->deactivate)) {
      site_status::warning(
        t("The Tag Albums module requires the Tags module.  <a href=\"%url\">Activate the Tags module now</a>",
          array("url" => url::site("admin/modules"))),
        "tag_albums_needs_tag");
    } else {
      site_status::clear("tag_albums_needs_tag");
    }
  }

  static function admin_menu($menu, $theme) {
    // Add a link to the admin page to the Content menu.
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("tag_albums")
               ->label(t("Tag Albums Settings"))
               ->url(url::site("admin/tag_albums")));
  }

  static function item_edit_form($item, $form) {
    // Create fields on the album edit screen to allow the user to link
    //   the album to a tag_albums page.
    if (!($item->is_album())) {
      return;
    }

    $url = url::site("tags/autocomplete");
    $form->script("")
         ->text("$('form input[name=tag_albums]').ready(function() {
                $('form input[name=tag_albums]').autocomplete(
                  '$url', {max: 30, multiple: true, multipleSeparator: ',', cacheLength: 1});
                });");

    $album_tags = ORM::factory("tags_album_id")
      ->where("album_id", "=", $item->id)
      ->find_all();

    $tag_names = "";
    $tag_album_type = "OR";
    if (count($album_tags) > 0) {
      $tag_names = $album_tags[0]->tags;
      $tag_album_type = $album_tags[0]->search_type;
    }

    $tags_album_group = $form->edit_item->group("tags_album_group");
    $tags_album_group->dropdown("tags_album_type")
          ->options(
            array("OR" => t("Display items that contain ANY of the following tags:"),
                  "AND" => t("Display items that contain ALL of the following tags:")))
          ->selected($tag_album_type);
    $tags_album_group->input("tag_albums")
         ->value($tag_names);
  }

  static function item_deleted($item) {
    // Whenever an item is deleted, delete any corresponding data.
    db::build()->delete("tags_album_ids")->where("album_id", "=", $item->id)->execute();
  }

  static function item_edit_form_completed($item, $form) {
    // Update the database with any changes to the tag_albums field.
    if (!($item->is_album())) {
      return;
    }

    $record = ORM::factory("tags_album_id")->where("album_id", "=", $item->id)->find();

    if ($form->edit_item->tags_album_group->tag_albums->value != "") {
      if (!$record->loaded()) {
        $record->album_id = $item->id;
      }
      $record->tags = $form->edit_item->tags_album_group->tag_albums->value;
      $record->search_type = $form->edit_item->tags_album_group->tags_album_type->value;
      $record->save();
    } else {
      db::build()->delete("tags_album_ids")->where("album_id", "=", $item->id)->execute();
    }
  }

  static function site_menu($menu, $theme) {
    if ($item = $theme->item()) {
      if ($item->is_photo()) {
        if ((identity::active_user()->admin) && (isset($theme->is_tagalbum_page))) {
          $menu->get("options_menu")
            ->append(Menu::factory("link")
                     ->id("g-tag-albums-set-cover")
                     ->label(t("Choose as the tag album cover"))
                     ->css_id("g-tag-albums-set-cover")
                     ->url(url::site("tag_albums/make_tag_album_cover/" . $item->id . "/" . $theme->tag_id . "/" . $theme->album_id)));
        }
      }
    }
  }
}
