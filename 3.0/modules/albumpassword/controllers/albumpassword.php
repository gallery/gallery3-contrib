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
class albumpassword_Controller extends Controller {
  public function assign($id) {
    // Display prompt for assigning a new password.

    // Make sure user has view/edit access for this item.
    $item = ORM::factory("item", $id);
    access::required("view", $item);
    access::required("edit", $item);

    // Create the page.
    $view = new View("assignpassword.html");
    $view->form = $this->_get_password_form($id);
    print $view;
  }
  
  public function login() {
    // Display prompt to allow visitors to use their passwords.

    // Create the page.
    $view = new View("loginpassword.html");
    $view->form = $this->_get_login_form();
    print $view;
  }

  public function remove($id) {
    // Remove a password from an album

    // Make sure user has view/edit privileges for this item
    $item = ORM::factory("item", $id);
    access::required("view", $item);
    access::required("edit", $item);

    // Check for and delete the password and any cached ids assigned to it.
    $existing_password = ORM::factory("items_albumpassword")->where("album_id", "=", $id)->find_all();
    if (count($existing_password) > 0) {
      foreach ($existing_password as $one_password) {
        db::build()->delete("albumpassword_idcaches")->where("password_id", "=", $one_password->id)->execute();
      }
      db::build()->delete("items_albumpasswords")->where("album_id", "=", $id)->execute();
      message::success(t("Password Removed."));
    }

    // Redirect the user back to the album.
    url::redirect(url::abs_site("albums/" . $id));
  }
  
  public function savepassword() {
    // Save a newly assigned password.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Convert submitted data to local variables.
    $album_id = Input::instance()->post("item_id");
    $album_password = strtolower(Input::instance()->post("assignpassword_password"));

    // Check for, and remove, any existing passwords and cached ids.
    $existing_password = ORM::factory("items_albumpassword")->where("album_id", "=", $album_id)->find_all();
    if (count($existing_password) > 0) {
      foreach ($existing_password as $one_password) {
        db::build()->delete("albumpassword_idcaches")->where("password_id", "=", $one_password->id)->execute();
      }
      db::build()->delete("items_albumpasswords")->where("album_id", "=", $album_id)->execute();
    }

    // Save the new password.
    $new_password = ORM::factory("items_albumpassword");
    $new_password->album_id = $album_id;
    $new_password->password = $album_password;
    $new_password->save();

    // Add the album to the id cache.
    $cached_album = ORM::factory("albumpassword_idcache");
    $cached_album->password_id = $new_password->id;
    $cached_album->item_id = $album_id;
    $cached_album->save();

    // Check for any sub-items within the album, add all of them to the id cache.
    $items = ORM::factory("item", $album_id)
      ->viewable()
      ->descendants();
    if (count($items) > 0) {
      foreach ($items as $one_item) {
        $cached_item = ORM::factory("albumpassword_idcache");
        $cached_item->password_id = $new_password->id;
        $cached_item->item_id = $one_item->id;
        $cached_item->save();
      }
    }

    // Display a success message and close the dialog.
    message::success(t("Password saved."));
    json::reply(array("result" => "success"));
  }

  public function logout() {
    // Delete a stored password cookie.
    cookie::delete("g3_albumpassword");
    cookie::delete("g3_albumpassword_id");
    url::redirect(url::abs_site("albums/1"));
  }
  
  public function checkpassword() {
    // Check that a password is valid, then store in a browser cookie.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Convert submitted data to local variables.
    $album_password = strtolower(Input::instance()->post("albumpassword_password"));

    // See if the submitted password matches any in the database.
    $existing_password = ORM::factory("items_albumpassword")
                              ->where("password", "=", $album_password)
                              ->find_all();

    if (count($existing_password) > 0) {
      // If the password if valid, then store it, and display a success message.
      // If not, close the dialog and display a rejected message.
      cookie::delete("g3_albumpassword_id");
      cookie::set("g3_albumpassword", $album_password);
      message::success(t("Password Accepted."));
      json::reply(array("result" => "success"));
    } else {
      message::error(t("Password Rejected."));
      json::reply(array("result" => "success"));
    }
  }

  private function _get_password_form($id) {
    // Generate a form for assigning a new password.
    $form = new Forge("albumpassword/savepassword", "", "post",
                      array("id" => "g-assign-password-form"));
    $assignpassword_group = $form->group("Enter Password")
                                 ->label(t("Enter Password:"));
    $assignpassword_group->hidden("item_id")->value($id);
    $assignpassword_group->input("assignpassword_password")
                         ->id('assignpassword_password')
                         ->label(t("Password:"));
    $assignpassword_group->submit("save_password")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }
  
  private function _get_login_form($id) {
    // Generate a form for allowing visitors to enter in their passwords.
    $form = new Forge("albumpassword/checkpassword", "", "post",
                      array("id" => "g-login-password-form"));

    $assignpassword_group = $form->group("Enter Password")
                                 ->label(t("Enter Password:"));
    $assignpassword_group->password("albumpassword_password")
                         ->id('albumpassword_password')
                         ->label(t("Password:"));

    $assignpassword_group->submit("")->value(t("Login"));

    // Return the newly generated form.
    return $form;
  }
}
