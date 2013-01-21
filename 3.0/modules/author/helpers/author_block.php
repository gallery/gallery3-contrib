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
class author_block_Core {
	static function get_site_list() {
    	return array("author" => t("Author"));
	}
	
	static function get($block_id, $theme) {
		$block = "";

		$item = $theme->item;

		if ((!isset($theme->item)) || ($item->is_album())) { 
			return;
		}

		switch ($block_id) {
		case "author":
			$record = ORM::factory("author_record")->where("item_id", "=", $item->id)->find();
			
			$byline = "";
			if ($record->loaded()) {
				$byline = $record->author;
			}

			if ($byline == '') {
				$byline = author::fix($item);
			}
		
			$block = new Block();
			$block->css_id = "g-author";
			$block->content = new View("author_block.html");
			$block->content->author = $byline;
		
			break;
		}
		return $block;

	}
}
