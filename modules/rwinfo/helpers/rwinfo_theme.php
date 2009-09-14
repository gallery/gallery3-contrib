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
class rwinfo_theme_Core {
  static function sidebar_blocks($theme) {
    if ($theme->item()) {
      if ($theme->item->id == 1) {
        return ;
      }

      $block = new Block();
      $block->css_id = "gMetadata";
      $block->title = $theme->item()->is_album() ? t("Album Info") : t("Photo Info");
      $block->content = new View("rwinfo_block.html");
      return $block;
    }
  }

  static function thumb_info($theme, $item) {
    $results = "";
    if ($item->view_count) {
      $results .= "<li>";
      $results .= t("Views: %view_count", array("view_count" => $item->view_count));
      $results .= "</li>";
    }

    if (module::is_active("tag")) {
      $tagsItem = ORM::factory("tag")
                  ->join("items_tags", "tags.id", "items_tags.tag_id")
                  ->where("items_tags.item_id", $item->id)
                  ->find_all();
      if (count($tagsItem) > 0) {
        $results .= "<li>";
        $results .= t("Tags:") . " ";
        for ($counter=0; $counter<count($tagsItem); $counter++) {
          if ($counter < count($tagsItem)-1) {
            $results .= "<a href=" . url::site("tags/$tagsItem[$counter]") . ">" . html::clean($tagsItem[$counter]->name) . "</a>, ";
          } else {
            $results .= "<a href=" . url::site("tags/$tagsItem[$counter]") . ">" . html::clean($tagsItem[$counter]->name) . "</a>";
          }
        }
      $results .= "</li>";
      }
    }

    if ($item->owner) {
      $results .= "<li>";
      if ($item->owner->url) {
        $results .= t("By: %owner_name", array("owner_name" => "<a href=\"{$item->owner->url}\">{$item->owner->full_name}</a>"));
      } else {
        $results .= t("By: %owner_name", array("owner_name" => "{$item->owner->full_name}"));
      }
      $results .= "</li>";
    }
    return $results;
  }
}