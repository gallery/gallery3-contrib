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
class pear4gallery3_event_Core {
  static function item_edit_form($item, $form) {
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {pear_album_views} (
               `id` int(9) NOT NULL auto_increment,
               `album_id` int(9) NOT NULL,
               `view_mode` varchar(64) NOT NULL,
               PRIMARY KEY (`id`))
               DEFAULT CHARSET=utf8;");
    if (!$item->is_album()){
      return;
    } else {
      $pearsettings = $form->edit_item->group("pear4gallery")->label("Pear settings" . $item->id);

      $record = ORM::factory("pear_album_view")->where("album_id", "=", $item->id)->find();
      if ($record->loaded()) {
        $pearsettings->dropdown("view_mode")
          ->label(t("View Mode"))
          ->options(array("grid" => t("Grid (Default)"), "mosaic" => t("Mosaic"), "carousel" => t("Carousel")))
          ->selected($record->view_mode);
      } else {
       $pearsettings->dropdown("view_mode")
          ->label(t("View Mode"))
          ->options(array("grid" => t("Grid (Default)"), "mosaic" => t("Mosaic"), "carousel" => t("Carousel")))
          ->selected("grid");
      }
    }
  }

  static function item_edit_form_completed($item, $form) {
    if (!$item->is_album()){
      return;
    }
    $view_mode = $form->edit_item->pear4gallery->view_mode->value;
    if (!(($view_mode == "mosaic") || ($view_mode == "carousel"))) {
      db::build()
        ->delete("pear_album_views")
        ->where("album_id", "=", $item->id)
        ->execute();
    } else {
      $record = ORM::factory("pear_album_view")->where("album_id", "=", $item->id)->find();
      if (!$record->loaded()) {
        $record->album_id = $item->id;
      }
      $record->view_mode = $view_mode;
      $record->save();
    }
  }
}
