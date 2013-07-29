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
class rwinfo_theme_Core {
  static function thumb_info($theme, $item) {
    $results = "";
    if ($item->view_count) {
      $results .= "<li>";
      $results .= t("Views: %view_count", array("view_count" => $item->view_count));
      $results .= "</li>";
    }

    // rWatcher Edit:  Display Tags on Thumbnails
    if (module::is_active("tag")) {
      $tags = ORM::factory("tag")
                  ->join("items_tags", "tags.id", "items_tags.tag_id")
                  ->where("items_tags.item_id", "=", $item->id)
                  ->find_all();
      if (count($tags) > 0) {
        $results .= "<li>";
        $results .= t("Tags:") . " ";
        $anchors = array();
        foreach ($tags as $tag) {
          $anchors[] = "<a href=" . $tag->url() . ">" . html::clean($tag->name) . "</a>";
        }
        $results .= join(", ", $anchors) . "</li>";
      }
    }
    // rWatcher End Edit

    if ($item->owner) {
      // rWatcher Edit:  Display profile instead of web site, if viewable.
      $str_owner_url = $item->owner->url;
      if (rwinfo_theme_Core::_can_view_profile_pages(identity::lookup_user($item->owner->id))) {
        $str_owner_url = user_profile::url($item->owner->id);
      }
      // rWatcher End Edit

      $results .= "<li>";
      if ($str_owner_url) {  //rW Edit str_owner_url
        $results .= t("By: <a href=\"%owner_url\">%owner_name</a>",
                      array("owner_name" => $item->owner->display_name(),
                            "owner_url" => $str_owner_url));  // rW Edit str_owner_url
      } else {
        $results .= t("By: %owner_name", array("owner_name" => $item->owner->display_name()));
      }
      $results .= "</li>";
    }
    return $results;
  }

  // rWatcher Edit:  This came from modules/gallery/controllers/user_profile.php, I modified it to be static.
  static private function _can_view_profile_pages($user) {
    if (!$user->loaded()) {
      return false;
    }

    if ($user->id == identity::active_user()->id) {
      // You can always view your own profile
      return true;
    }

    switch (module::get_var("gallery", "show_user_profiles_to")) {
    case "admin_users":
      return identity::active_user()->admin;

    case "registered_users":
      return !identity::active_user()->guest;

    case "everybody":
      return true;

    default:
      // Fail in private mode on an invalid setting
      return false;
    }
  }
}