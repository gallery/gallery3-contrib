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
class GreyDragon_Controller extends Controller {
/*  public function show($id) {
    $v = new Theme_View("page.html", "other", "profile");
    $v->page_title = t("%name Profile", array("name" => $user->display_name()));
    $v->content = new View("user_profile.html");

    $v->content->user = $user;
    $v->content->contactable =
      !$user->guest && $user->id != identity::active_user()->id && $user->email;
    $v->content->editable =
      identity::is_writable() && !$user->guest && $user->id == identity::active_user()->id;

    $event_data = (object)array("user" => $user, "content" => array());
    module::event("show_user_profile", $event_data);
    $v->content->info_parts = $event_data->content;

    print $v;
  }
*/
}
