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
class proofsheet_event_Core {

  /**
   * This adds the buttons for the case of an album.  If you want to disable 
   * either the A4 or LTR part, do so here *and* below for tags.
   */
  static function album_menu($menu, $theme) {
    if (access::can("view_full", $theme->item)) {
      $proofsheetA4Link = url::site("proofsheet/makepdf/a4/album/{$theme->item->id}");
      $menu->append(Menu::factory("link")
        ->id("proofsheetA4")
        ->label(t("A4 Proof Sheet"))
        ->url($proofsheetA4Link)
        ->css_id("g-proofsheet-a4-link"));
      $proofsheetLTRLink = url::site("proofsheet/makepdf/ltr/album/{$theme->item->id}");
      $menu->append(Menu::factory("link")
          ->id("proofsheetLTR")
          ->label(t("LTR Proof Sheet"))
          ->url($proofsheetLTRLink)
          ->css_id("g-proofsheet-ltr-link"));
    }
  }

  /**
   * This adds the buttons for the case of a tag.  If you want to disable 
   * either the A4 or LTR part, do so here *and* above for albums.
   */
  static function tag_menu($menu, $theme) {
    $proofsheetA4Link = url::site("proofsheet/makepdf/a4/tag/{$theme->tag()->id}");
    $menu
      ->append(Menu::factory("link")
          ->id("proofsheetA4")
          ->label(t("A4 Proof Sheet"))
          ->url($proofsheetA4Link)
          ->css_id("g-proofsheet-a4-link"));
    $proofsheetLTRLink = url::site("proofsheet/makepdf/ltr/tag/{$theme->tag()->id}");
    $menu
      ->append(Menu::factory("link")
          ->id("proofsheetLTR")
          ->label(t("LTR Proof Sheet"))
          ->url($proofsheetLTRLink)
          ->css_id("g-proofsheet-ltr-link"));
  } 
}
