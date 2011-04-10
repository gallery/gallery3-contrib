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
class twitter_event_Core {

  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
        ->id("twitter_menu")
        ->label(t("Twitter"))
        ->url(url::site("admin/twitter")));
  }

  static function site_menu($menu, $theme) {
    $item = $theme->item();
    if ($item && twitter::is_registered() && (identity::active_user()->id > 1)) {
      $menu->get("options_menu")
        ->append(Menu::factory("dialog")
                 ->id("twitter")
                 ->label(t("Share on Twitter"))
                 ->css_id("g-twitter-link")
                 ->url(url::site("twitter/dialog/{$item->id}")));
    }
  }

  static function context_menu($menu, $theme, $item) {
    if ((identity::active_user()->id > 1) && twitter::is_registered()) {
      $menu->get("options_menu")
        ->append(Menu::factory("dialog")
                 ->id("twitter")
                 ->label(t("Share on Twitter"))
                 ->css_class("ui-icon-link g-twitter-share")
                 ->url(url::site("twitter/dialog/{$item->id}")));
    }
  }
  
  /**
   * Add Twitter account info to user profiles
   * @param object $data 
   */
  static function show_user_profile($data) {
    $twitter_account = ORM::factory("twitter_user")->where("user_id", "=", $data->user->id)->find();
    if ($twitter_account->loaded()) {
      $v = new View("user_profile_info.html");
      $v->user_profile_data = array();              
      $fields = array(
          "screen_name" => t("Screen name")
        );
      foreach ($fields as $field => $label) {
        if (!empty($twitter_account->$field)) {
          $value = $twitter_account->$field;
          if ($field == "screen_name") {
            $value = html::mark_clean(html::anchor(twitter::$url . 
              $twitter_account->screen_name, 
              "@{$twitter_account->screen_name}"));
          }
          $v->user_profile_data[(string) $label] = $value;
        }
      }
      if (identity::active_user()->id == $data->user->id) {
        $button = html::mark_clean(html::anchor(url::site("twitter/reset/"
                . $data->user->id), t("Switch to another Twitter screen name")));
        $v->user_profile_data[""] = $button;
      }
      $data->content[] = (object) array("title" => t("Twitter account"), "view" => $v);
    }
  }

}
