<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class batchtag_theme_Core {
  static function sidebar_blocks($theme) {
    // Display form for tagging in the album sidebar.

    // Make sure the current page belongs to an item.
    if (!$theme->item()) {
      return;
    }
    
    $item = $theme->item();
    
    // Only display the form in albums that the user has edit permission in.
    if ($item->is_album() && access::can("edit", $item)) {

      // Make a new sidebar block.
      $block = new Block();
      $block->css_id = "gBatchTag";
      $block->title = t("Batch Tag");
      $block->content = new View("batchtag_block.html");

      // Make a new form to place in the sidebar block.
      $form = new Forge("batchtag/tagitems", "", "post",
                        array("id" => "gBatchTagForm"));
      $label = t("Tag everything in this album:");
      $group = $form->group("add_tag")->label("Add Tag");
      $group->input("name")->label($label)->rules("required|length[1,64]");
      $group->hidden("item_id")->value($item->id);
      $group->submit("")->value(t("Add Tag"));
      $block->content->form = $form;

      // Display the block.
      return $block;
    }
  }
}