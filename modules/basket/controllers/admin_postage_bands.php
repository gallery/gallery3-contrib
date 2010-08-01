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

class Admin_Postage_Bands_Controller extends Controller
{
  /**
   * the index page of the user homes admin
   */
  public function index()
  {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_postage_bands.html");
    $view->content->postage_bands = ORM::factory("postage_band")->order_by("name")->find_all();

    print $view;
  }

  public function add_postage_band_form() {
    print postage_band::get_add_form_admin();
  }


  public function add_postage_band() {
    access::verify_csrf();

    $form = postage_band::get_add_form_admin();
    try {
      $valid = $form->validate();
      $postage_band = ORM::factory("postage_band");
      $postage_band->name = $form->add_postage->inputs["name"]->value;
      $postage_band->flat_rate = $form->add_postage->flat_rate->value;
      $postage_band->per_item = $form->add_postage->per_item->value;
      $postage_band->validate();
    } catch (ORM_Validation_Exception $e) {
      // Translate ORM validation errors into form error messages
      foreach ($e->validation->errors() as $key => $error) {
        $form->add_postage->inputs[$key]->add_error($error, 1);
      }
      $valid = false;
    }

    if ($valid) {
      $postage_band->save();
      message::success(t("Created postage band %postage_name", array(
        "postage_name" => html::clean($postage_band->name))));
      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }

  public function delete_postage_band_form($id) {
    $postage = ORM::factory("postage_band", $id);
    if (!$postage->loaded()) {
      throw new Kohana_404_Exception();
    }
    print postage_band::get_delete_form_admin($postage);
  }

  public function delete_postage_band($id) {
    access::verify_csrf();

    if ($id == user::active()->id || $id == user::guest()->id) {
      access::forbidden();
    }

    $postage  = ORM::factory("postage_band", $id);
    if (!$postage->loaded()) {
      throw new Kohana_404_Exception();
    }

    $form = postage_band::get_delete_form_admin($postage);
    if($form->validate()) {
      $name = $postage->name;
      $postage->delete();
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }

    $message = t("Deleted user %postage_band", array("postage_band" => html::clean($name)));
    log::success("user", $message);
    message::success($message);
    json::reply(array("result" => "success"));
  }

  public function edit_postage_band($id) {
    access::verify_csrf();

    $postage = ORM::factory("postage_band", $id);
    if (!$postage->loaded()) {
      throw new Kohana_404_Exception();
    }

    $form = postage_band::get_edit_form_admin($postage);
    try {
      $valid = $form->validate();
      $postage->name = $form->edit_postage->inputs["name"]->value;
      $postage->flat_rate = $form->edit_postage->flat_rate->value;
      $postage->per_item = $form->edit_postage->per_item->value;
      $postage->validate();
    } catch (ORM_Validation_Exception $e) {
      // Translate ORM validation errors into form error messages
      foreach ($e->validation->errors() as $key => $error) {
        $form->edit_postage->inputs[$key]->add_error($error, 1);
      }
      $valid = false;
    }

    if ($valid) {
      $postage->save();
      message::success(t("Changed postage band %postage_name",
          array("postage_name" => html::clean($postage->name))));
      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }

  public function edit_postage_band_form($id) {
    $postage = ORM::factory("postage_band", $id);
    if (!$postage->loaded()) {
      throw new Kohana_404_Exception();
    }

    $form = postage_band::get_edit_form_admin($postage);

    print $form;
  }

}