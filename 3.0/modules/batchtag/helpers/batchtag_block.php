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
class batchtag_block_Core {
  static function get_site_list() {
    return array("batch_tag" => t("Batch Tag"));
  }

  static function get($block_id, $theme) {
    $block = "";

    // Only display on album pages that the user can edit.
    $item = $theme->item();
    if (!$item || !$item->is_album() || !access::can("edit", $item)) {
      return;
    }

    switch ($block_id) {
    case "batch_tag":
      // Make a new sidebar block.
      $block = new Block();
      $block->css_id = "g-batch-tag";
      $block->title = t("Batch Tag");
      $block->content = new View("batchtag_block.html");

      // Make a new form to place in the sidebar block.
      $form = new Forge("batchtag/tagitems", "", "post",
                        array("id" => "g-batch-tag-form"));
      $label = t("Tag everything in this album:");
      $group = $form->group("add_tag")->label("Add Tag");
      $group->input("name")->label($label)->rules("required|length[1,64]");
      $group->checkbox("tag_subitems")
            ->label(t("Include sub-albums?"))
            ->value(true)
            ->checked(false);

      $group->hidden("item_id")->value($item->id);
      $group->submit("")->value(t("Add Tag"));
      $block->content->batch_tag_form = $form;

      break;
	}
    return $block;
  }
}
