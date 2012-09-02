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

/**
 * Generate a PDF proof sheet on-the-fly of the current album or tag.
 * By Shad Laws.  Version 5, 2012/04/05
 * 
 * 2012/04/05, version 5
 *  Added ability to include GIF thumbnails if GD is installed (FPDF uses GD)
 *  Changed behavior of unhandled file types - now provides missing image icon instead of throwing an exception
 * 2012/03/30, version 4
 *  Major rewrite.  Output is similar, but everything "under the hood" is much cleaner and (I hope) more easily understood and tweakable by other users.
 *  Header link is now an icon.
 *  Fixed encoding problems with diacritic marks and special characters.
 *  Now includes FPDF as a library instead of requiring a separate installtion.
 * 2012/03/28, version 3
 *  Made sizing configuration more flexible
 *  Prettified code so it's easier to understand and tweak as desired
 *  Added header link
 *  First version properly documented and linked to Gallery wiki
 * 2012/03/27, version 2
 *  Determines jpg/png type by file header, not extension, which makes it robust against misnamed extensions
 *  (N.B.: there's a bug in some movie modules that copy missing_movie.png as a jpg thumbnail!)
 *  Made caption size limits to prevent overrun
 * 2012/03/27, version 1
 *  Initial release
 */

class proofsheet_event_Core {

  /**
   * This adds the buttons for the case of an album.  If you want to disable 
   * either the A4 or LTR part, do so here *and* below for tags.
   */
  static function album_menu($menu, $theme) {
    $proofsheetA4Link = url::site("proofsheet/makepdf/a4/album/{$theme->item->id}");
    $menu
      ->append(Menu::factory("link")
          ->id("proofsheetA4")
          ->label(t("A4 Proof Sheet"))
          ->url($proofsheetA4Link)
          ->css_id("g-proofsheet-a4-link"));
    $proofsheetLTRLink = url::site("proofsheet/makepdf/ltr/album/{$theme->item->id}");
    $menu
      ->append(Menu::factory("link")
          ->id("proofsheetLTR")
          ->label(t("LTR Proof Sheet"))
          ->url($proofsheetLTRLink)
          ->css_id("g-proofsheet-ltr-link"));
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
