<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
    $view->content->postage_bands = ORM::factory("bp_postage_band")->order_by("name")->find_all();

    print $view;
  }

  public function add_postage_band_form() {
    print bp_postage_band::get_add_form_admin();
  }


  public function add_postage_band() {
    access::verify_csrf();

    $form = bp_postage_band::get_add_form_admin();
    $valid = $form->validate();
    $name = $form->add_postage->inputs["name"]->value;
    $postage  = ORM::factory("bp_postage_band")->where("name","=", $name)->find();
    if ($postage->loaded()) {
      $form->add_postage->inputs["name"]->add_error("in_use", 1);
      $valid = false;
    }

    if ($valid) {
      $postage = bp_postage_band::create(
        $name,
        $form->add_postage->flat_rate->value,
        $form->add_postage->per_item->value,
        $form->add_postage->via_download->checked
        );

      $postage->save();
      message::success(t("Created postage band %postage_name", array(
        "postage_name" => html::clean($postage->name))));
      print json::reply(array("result" => "success"));
    } else {
      print $form;
    }
  }

  public function delete_postage_band_form($id) {
    $postage = ORM::factory("bp_postage_band", $id);
    if (!$postage->loaded()) {
      kohana::show_404();
    }
    print bp_postage_band::get_delete_form_admin($postage);
  }

  public function delete_postage_band($id) {
    access::verify_csrf();

    $postage = ORM::factory("bp_postage_band", $id);
    if (!$postage->loaded()) {
      kohana::show_404();
    }

    $form = bp_postage_band::get_delete_form_admin($postage);
    if($form->validate()) {
      $name = $postage->name;
      $postage->delete();
    } else {
      print $form;
    }

    $message = t("Deleted user %postage_band", array("postage_band" => html::clean($name)));
    log::success("user", $message);
    message::success($message);
    print json::reply(array("result" => "success"));
  }

  public function edit_postage_band($id) {
    access::verify_csrf();

    $postage = ORM::factory("bp_postage_band", $id);
    if (!$postage->loaded()) {
      kohana::show_404();
    }

    $form = bp_postage_band::get_edit_form_admin($postage);
    $valid = $form->validate();
    if ($valid) {
      $new_name = $form->edit_postage->inputs["name"]->value;
      if ($new_name != $postage->name &&
          ORM::factory("bp_postage_band")
          ->where("name", "=", $new_name)
          ->where("id","!=", $postage->id)
          ->find()
          ->loaded()) {
        $form->edit_postage->inputs["name"]->add_error("in_use", 1);
        $valid = false;
      } else {
        $postage->name = $new_name;
      }
      $postage->flat_rate = $form->edit_postage->flat_rate->value;
      $postage->per_item = $form->edit_postage->per_item->value;
      $postage->via_download = $form->edit_postage->via_download->checked;
      $postage->save();

      message::success(t("Changed postage band %postage_name",array("postage_name" => html::clean($postage->name))));
      print json::reply(array("result" => "success"));
    } else {
      print $form;
    }
  }

  public function edit_postage_band_form($id) {
    $postage = ORM::factory("bp_postage_band", $id);
    if (!$postage->loaded()) {
      kohana::show_404();
    }

    $form = bp_postage_band::get_edit_form_admin($postage);

    print $form;
  }

}