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
class quotas_theme_Core {
  static function page_bottom($theme) {
    // If a user is logged in, display their space usage at the bottom of the page.
    if (!identity::active_user()->guest) {
      $record = ORM::factory("users_space_usage")->where("owner_id", "=", identity::active_user()->id)->find();
      if ($record->get_usage_limit() == 0) {
        print t("You are using %usage", array("usage" => $record->total_usage_string()));
      } else {
        print t("You are using %usage of your %limit limit (%percentage%)", 
                array("usage" => $record->current_usage_string(), 
                "limit" => $record->get_usage_limit_string(), 
                "percentage" => number_format((($record->current_usage() / $record->get_usage_limit()) * 100), 2)));
      }
    }
  }
}
