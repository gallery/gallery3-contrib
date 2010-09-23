<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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
class photoannotation_block_Core {
  static function get_site_list() {
    return array("photoannotation" => t("Users"));
  }

  static function get($block_id, $theme) {
    $block = "";
    if (!identity::active_user()->guest || module::get_var("photoannotation", "allowguestsearch", false)) {
      switch ($block_id) {
      case "photoannotation":
        $block = new Block();
        $block->css_id = "g-photoannotation";
        $block->title = t("People");
        $block->content = new View("photoannotation_block.html");
        $block->content->cloud = photoannotation::cloud(30);
        $block->content->form = photoannotation::get_user_search_form("g-user-cloud-form");
      }
    }
    return $block;
  }
}
