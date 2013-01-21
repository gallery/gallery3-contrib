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
class metadescription_theme_Core {
  static function head($theme) {
    if ($theme->tag()) {
      // If the current page belongs to a tag, look up
      //   the information for that tag.
      $tagsItem = ORM::factory("tag")
      ->where("id", "=", $theme->tag()->id)
      ->find_all();

    } elseif ($theme->item()) {
      // If the current page belongs to an item (album, photo, etc.),
      //   look up any tags that have been applied to that item.
      $tagsItem = ORM::factory("tag")
        ->join("items_tags", "tags.id", "items_tags.tag_id")
        ->where("items_tags.item_id", "=", $theme->item->id)
        ->find_all();

    } else {
      // If the current page is neighter an item nor tag, do nothing.
      return;
    }

    // Load the meta tags into the top of the page.
    // @todo: metadescription_block.html requires an item so for now, don't render it unless we
    // have one.
    if ($theme->item() || $theme->tag()) {
      $metaView = new View("metadescription_block.html");
      $metaView->tags = $tagsItem;
      return $metaView;
    }
  }
}
