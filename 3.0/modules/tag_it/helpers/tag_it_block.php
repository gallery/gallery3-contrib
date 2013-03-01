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
class tag_it_block_Core {
  static function get_site_list() {
    return array("untagged_photo" => t("Tag it"));
  }

  static function get($block_id, $theme) {
    if (identity::active_user()->guest) {
      return;
    }

    $block = "";
    switch ($block_id) {
    case "untagged_photo":
      $attempts = 0;
      do {
        $item = item::random_query()
          ->join("items_tags", "items.id", "items_tags.item_id", "left")
          ->where("items.type", "!=", "album")
          ->where("items_tags.item_id", "IS", null)
          ->find_all(1)
          ->current();
      } while (!$item && $attempts++ < 3);
      if ($item && $item->loaded()) {
        $block = new Block();
        $block->css_id = "g-tag-it-block";
        $block->title = t("Tag it");
        $block->content = new View("tag_it_block.html");
        $block->content->item = $item;

        $form = new Forge("tags/create/{$item->id}", "", "post",
                          array("id" => "g-tag-it-add-tag-form", "class" => "g-short-form"));
        $label = $item->is_album() ?
          t("Add tag to album") :
          ($item->is_photo() ? t("Add tag to photo") : t("Add tag to movie"));

        $group = $form->group("add_tag")->label("Add Tag");
        $group->input("name")->label($label)->rules("required")->id("name");
        $group->hidden("item_id")->value($item->id);
        $group->submit("")->value(t("Add Tag"));

        $block->content->form = $form;
      }
      break;
    }

    return $block;
  }
}
