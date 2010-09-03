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
class User_Profile_Controller extends Controller {
  public function show($id) {
    // If we get here, then we should have a user id other than guest.
    $user = identity::lookup_user($id);
    if (!$user) {
      throw new Kohana_404_Exception();
    }
    $v = new Theme_View("page.html", "photoannotationuserprofile", "dynamic");
    $item_users = ORM::factory("items_user")->where("user_id", "=", $id)->find_all();
    $children_count = count($item_users);
    foreach ($item_users as $item_user) {
      $item_thumb = ORM::factory("item")
          ->viewable()
          ->where("type", "!=", "album")
          ->where("id", ">=", $item_user->item_id)
          ->find();
      $item_thumbs[] = $item_thumb;
    }
    $page_size = module::get_var("gallery", "page_size", 9);
    $page = (int) Input::instance()->get("page", "1");
    $offset = ($page-1) * $page_size;
    $max_pages = max(ceil($children_count / $page_size), 1);

    // Make sure that the page references a valid offset
    if ($page < 1) {
      url::redirect($album->abs_url());
    } else if ($page > $max_pages) {
      url::redirect($album->abs_url("page=$max_pages"));
    }

    $v->set_global("page", $page);
    $v->set_global("max_pages", $max_pages);
    $v->set_global("page_size", $page_size);
    $v->set_global("userid", $id);
    $v->set_global("children", array_slice($item_thumbs, $offset, $page_size));
    $v->set_global("children_count", $children_count);
    $v->content = new View("photoannotation_user_profile.html");
    $v->content->user = $user;
    $v->content->contactable =
      !$user->guest && $user->id != identity::active_user()->id && $user->email;
    $v->content->editable =
      identity::is_writable() && !$user->guest && $user->id == identity::active_user()->id;
    $event_data = (object)array("user" => $user, "content" => array());
    module::event("show_user_profile", $event_data);
    $v->content->info_parts = $event_data->content;
    $v->content = new View("dynamic.html");
    print $v;
  }

  public function contact($id) {
    $user = identity::lookup_user($id);
    print user_profile::get_contact_form($user);
  }

  public function send($id) {
    access::verify_csrf();
    $user = identity::lookup_user($id);
    $form = user_profile::get_contact_form($user);
    if ($form->validate()) {
      Sendmail::factory()
        ->to($user->email)
        ->subject(html::clean($form->message->subject->value))
        ->header("Mime-Version", "1.0")
        ->header("Content-type", "text/html; charset=UTF-8")
        ->reply_to($form->message->reply_to->value)
        ->message(html::purify($form->message->message->value))
        ->send();
      message::success(t("Sent message to %user_name", array("user_name" => $user->display_name())));
      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }
}
