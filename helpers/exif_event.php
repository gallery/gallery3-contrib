<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Grey Dragon Theme - a custom theme for Gallery 3
 * This theme was designed and built by Serguei Dosyukov, whose blog you will find at http://blog.dragonsoft.us
 * Copyright (C) 2009-2011 Serguei Dosyukov
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to
 * the Free Software Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<?
class exif_event_Core {
  static function item_created($item) {
    if (!$item->is_album()) {
      exif::extract($item);
    }
  }

  static function item_deleted($item) {
    db::build()
      ->delete("exif_records")
      ->where("item_id", "=", $item->id)
      ->execute();
  }

  static function photo_menu($menu, $theme) {
    $item = $theme->item();
    $menu->append(
      Menu::factory("link")
      ->id("exifdata-link")
      ->label(t("Photo Details"))
      ->url(url::site("exif/show/$item->id"))
      ->css_id("g-exifdata-link")
      ->css_class("g-dialog-link"));
  }
}
?>
