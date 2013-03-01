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

class user_homes_event_Core {
  /**
   * Called when a user logs in. This will setup the session with the
   * user home if it exists on the database. This means when the page
   * is refreshed after logging in the direction can occur.
   */
  static function user_login($user) {
    $home = ORM::factory("user_home")->where("id", "=", $user->id)->find();
    if ($home->loaded() && $home->home != 0) {
      $continue_url = Session::instance()->get("continue_url", null);
      if ($continue_url == null){
        Session::instance()->set("redirect_home", $home->home);
      }
    }
  }

  /**
   * called after a log in occurs and when the first gallery is loaded.
   * if the home variable exists on the session then a redirect will
   * occur to that home and the variable removed from the session to
   */
  static function gallery_ready() {
    $session = Session::instance();
    $home = $session->get("redirect_home");
    if ($home) {
      // Remove from session to ensure redirect does not occur again
      $session->set("redirect_home",null);
      url::redirect("albums/$home");
    }
  }

  /**
   * Called just before a user is deleted. This will remove the user from
   * the user_homes directory.
   */
  static function user_before_delete($user) {
    ORM::factory("user_home")
      ->where("id", "=", $user->id)
      ->delete_all();
  }

  /**
   * Called when admin is adding a user
   */
  static function user_add_form_admin($user, $form) {
    $form->add_user->dropdown("user_home")
      ->label(t("Home Gallery"))
      ->options(self::createGalleryArray())
      ->selected(0);
  }

  /**
   * Called after a user has been added
   */
  static function user_add_form_admin_completed($user, $form) {
    $home = ORM::factory("user_home")->where("id", "=", $user->id)->find();
    $home->id = $user->id;
    $home->home = $form->add_user->user_home->value;
    $home->save();
  }

  /**
   * Called when admin is editing a user
   */
  static function user_edit_form_admin($user, $form) {
    $home = ORM::factory("user_home")->where("id", "=", $user->id)->find();
    if ($home->loaded()) {
      $selected = $home->home;
    } else {
      $selected = 0;
    }
    $form->edit_user->dropdown("user_home")
      ->label(t("Home Gallery"))
      ->options(self::createGalleryArray())
      ->selected($selected);
  }

  /**
   * Called after a user had been edited by the admin
   */
  static function user_edit_form_admin_completed($user, $form) {
    $home = ORM::factory("user_home")->where("id", "=", $user->id)->find();
    if ($home->loaded()) {
      $home->home = $form->edit_user->user_home->value;
      $home->save();
    }
  }


  /**
   * Called when user is editing their own form
   */
  static function user_edit_form($user, $form) {
    $home = ORM::factory("user_home")->where("id", "=", $user->id)->find();

    if ($home->loaded()) {
      $selected = $home->home;
    } else {
      $selected = 0;
    }

    $form->edit_user->dropdown("user_home")
      ->label(t("Home Gallery"))
      ->options(self::createGalleryArray())
      ->selected($selected);
  }

  /**
   * Called after a user had been edited by the user
   */
  static function user_edit_form_completed($user, $form) {
    $home = ORM::factory("user_home")->where("id", "=", $user->id)->find();
    $home->id = $user->id;
    $home->home = $form->edit_user->user_home->value;
    $home->save();
  }

  /**
   * Creates an array of galleries
   */
  static function createGalleryArray() {
    $array[0] = "none";
    $root = ORM::factory("item", 1);
    self::tree($root, "", $array);
    return $array;
  }

  /**
   * recursive function to build array for drop down list
   */
  static function tree($parent, $dashes, &$array) {
    if ($parent->id == "1") {
      $array[$parent->id] = ORM::factory("item", 1)->title;
    } else {
      $array[$parent->id] = "$dashes $parent->name";
    }

    $albums = ORM::factory("item")
      ->where("parent_id", "=", $parent->id)
      ->where("type", "=", "album")
      ->order_by("title", "ASC")
      ->find_all();
    foreach ($albums as $album) {
      self::tree($album, "-$dashes", $array);
    }
    return;
  }

  static function show_user_profile($data) {
    $home = ORM::factory("user_home")->where("id", "=", $data->user->id)->find();

    if ($home->loaded()) {
      $view = new View("user_profile_home.html");
      $item = ORM::factory("item")->where("id", "=", $home->home)->find();
      if ($item->loaded()) {
        $view->item = $item;
        $data->content[] = (object)array("title" => t("Home album"), "view" => $view);
      }
    }
  }

  static function album_add_form($parent, $form){

    $group = $form->group("privacy")
      ->label(t("album privacy settings"));
    $group->checkbox("private")->label(t("Private"))->id("uh_private")->onClick("pc()");
    $group->input("username")->label(t("Username"))->id("uh_username")
      ->callback("user_homes_event::valid_name")
      ->error_messages("in_use", t("There is already a user with that username"))
      ->error_messages("required", t("You must enter a username"))->callback("user_homes_event::valid_name")->rules("length[1,32]");
    $group->password("password")->label(t("Password"))->id("uh_password")
      ->callback("user_homes_event::valid_password")
      ->error_messages("required", t("You must enter a password"))
      ->error_messages("minlength", t("Password is not long enough"));
    $group->password("password2")->label(t("Confirm password"))->id("uh_password2")
      ->error_messages("matches", t("Passwords do not match"))
      ->matches($group->password);

    $form->script("")->url(url::abs_file("modules/user_homes/js/user_homes_add_album.js"));
  }

  /**
   * Validate the user name.  Make sure there are no conflicts.
   */
  static function valid_password($field) {
    $input = Input::instance();
    $method = $field->method;
    $checked = $input->$method("private", false);
    if ($checked)
    {
      if (!$field->value || strlen($field->value)==0){
        $field->add_error("required", 1);
      }
      else
      {
        $minimum_length = module::get_var("user", "mininum_password_length", 5);
        if (strlen($field->value) < $minimum_length) {
          $field->add_error("minlength", 1);
        }
      }
    }

  }

  /**
   * Validate the user name.  Make sure there are no conflicts.
   */
  static function valid_name($field) {
    $input = Input::instance();
    $method = $field->method;
    $checked = $input->$method("private", false);

    if ($checked)
    {
      if (!$field->value || strlen($field->value)==0){
        $field->add_error("required", 1);
      }
      elseif (db::build()->from("users")
        ->where("name", "=", $field->value)
        ->count_records() == 1) {
        $field->add_error("in_use", 1);
      }
    }
  }


  static function album_add_form_completed($album, $form){
    if ($form->privacy->private->checked)
    {
      $username = $form->privacy->username->value;
      $password = $form->privacy->password->value;

      // TODO validation

      // create a group based on username
      $group = identity::create_group($username);

      // create a user based on username
      $user = identity::create_user($username, $username, $password, $username."@unknown.com");

      identity::add_user_to_group($user,$group);

      // create user home
      $home = ORM::factory("user_home")->where("id", "=", $user->id)->find();
      $home->id = $user->id;
      $home->home = $album->id;
      $home->save();

      // reload album
      $album->reload();

      // set permissions
      // deny all groups.
      $groups = ORM::factory("group")->find_all();
      foreach ($groups as $group2){
        if ($group->id != $group2->id){
          access::deny($group2 , "view", $album);
          access::deny($group2 , "view_full", $album);
        }
      }

      // deny all other albums
      $albums = ORM::factory("item")
        ->where("type", "=", "album")->find_all();
      foreach($albums as $albumt){
        access::deny($group,"view", $albumt);
      }

      // allow access to newly created group
      access::allow($group, "view_full", $album);
      $parents = $album->parents();
      foreach ($parents as $parent){
        access::allow($group, "view", $parent);
      }
      access::allow($group, "view", $album);
    }
  }
}
