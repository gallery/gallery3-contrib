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
 
 /*
 Album Cover Browser module
 Allows browsing for an album to cover with a thumbnail.
 
 V1.0 By Lord Beowulf, July 26, 2010
 
 Add site and context menus
  */
 
class album_cover_browser_event_Core {
	
	static function site_menu($menu, $theme) {
		$item = $theme->item();

    $csrf = access::csrf_token();

		$options_menu = $menu->get("options_menu");
		$can_edit = $item && access::can("edit", $item);

    if ($can_edit && ($options_menu != null)) {
			$cover_title = t("Browse for an album to cover");
			$options_menu
				->append(Menu::factory("dialog")
                 ->id("browse_album_cover")
                 ->label($cover_title)
								 ->css_class("ui-icon-folder-open")
								 ->url(url::site("browse/browse/$item->id?csrf=$csrf")));
    }
	}
 
 
   static function context_menu($menu, $theme, $item, $thumb_css_selector) {

    $csrf = access::csrf_token();

		$options_menu = $menu->get("options_menu");
		$can_edit = $item && access::can("edit", $item);

    if ($can_edit && ($options_menu != null)) {
			$cover_title = t("Browse for an album to cover");
			$options_menu
				->append(Menu::factory("dialog")
                 ->id("browse_album_cover")
                 ->label($cover_title)
								 ->css_class("ui-icon-folder-open")
								 ->url(url::site("browse/browse/$item->id?csrf=$csrf")));
			
    }
  }

}