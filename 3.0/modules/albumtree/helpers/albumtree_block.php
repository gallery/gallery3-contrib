<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
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
class albumtree_block_Core {
  static function get_site_list() {
    return array("albumtree" => t("Album tree"));
  }

  static function get($block_id) {
    $block = new Block();
    switch ($block_id) {
    case "albumtree":
      $style = module::get_var("albumtree", "style", "select");
      $block->css_id = "g-albumtree";
      $block->title = t("Album Tree");
      $block->content = new View("albumtree_block_{$style}.html");
      $block->content->root = item::root();
      break;
    }
    return $block;
  }
}