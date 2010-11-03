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
class albumpassword_event_Core {
  static function site_menu($menu, $theme) {
    // Add menu options for Adding / Removing / Using passwords to the menu.

    // If this page doesn't belong to an item, don't display the menu.
    if (!$theme->item()) {
      return;
    }
    $item = $theme->item();

    // If there isn't currently a password stored in the cookie, 
    //   then display the enter password link.
    if (cookie::get("g3_albumpassword") == "") {
      $menu->append(Menu::factory("dialog")
                      ->id("albumpassword_login")
                      ->css_id("g-album-password-login")
                      ->url(url::site("albumpassword/login"))
                      ->label(t("Enter password")));
    } else {
      // If a password has been entered already
      // display the log out link, and links to the protected albums
      $menu->append(Menu::factory("submenu")
                      ->id("albumpassword_protected")
                      ->css_id("g-album-password-protected")
                      ->label(t("Protected albums")));
      $menu->get("albumpassword_protected")
                      ->append(Menu::factory("link")
                      ->id("albumpassword_logout")
                      ->css_id("g-album-password-logout")
                      ->url(url::site("albumpassword/logout"))
                      ->label(t("Clear password")));
      $existing_password = ORM::factory("items_albumpassword")
                      ->where("password", "=", cookie::get("g3_albumpassword"))
                      ->find_all();
      if (count($existing_password) > 0) {
        $counter = 0;
        while ($counter < count($existing_password)) {
          $item_album = ORM::factory("item")->where("id", "=", $existing_password[$counter]->album_id)->find();
          $menu->get("albumpassword_protected")
               ->append(Menu::factory("link")
               ->id("albumpassword_album" . $counter)
               ->label(html::purify($item_album->title))
               ->css_id("g-album-password-album" . $counter)
               ->url(url::abs_site("{$item_album->type}s/{$item_album->id}")));
          $counter++;
        }
      }
    }

    // If this is an album without a password, display a link for assigning one.
    // If this is an album with a password, display a link to remove it.
    if ($item->is_album()) {
      if ((access::can("view", $item)) && (access::can("edit", $item))) {
        $existing_password = ORM::factory("items_albumpassword")
                             ->where("album_id", "=", $item->id)
                             ->find_all();
        if (count($existing_password) > 0) {
          $menu->get("options_menu")
               ->append(Menu::factory("link")
               ->id("albumpassword_remove")
               ->label(t("Remove password"))
               ->css_id("g-album-password-remove")
               ->url(url::site("albumpassword/remove/" . $item->id)));
        } else {
          $menu->get("options_menu")
               ->append(Menu::factory("dialog")
               ->id("albumpassword_assign")
               ->label(t("Assign password"))
               ->css_id("g-album-password-assign")
               ->url(url::site("albumpassword/assign/" . $item->id)));
        }
      }
    }
  }

  static function item_deleted($item) {
    // If an album is deleted, remove any associated passwords.
    $existingPasswords = ORM::factory("items_albumpassword")
                          ->where("album_id", "=", $item->id)
                          ->find_all();
    if (count($existingPasswords) > 0) {
      db::build()->delete("items_albumpassword")->where("album_id", "=", $item->id)->execute();
    }
  }
}
