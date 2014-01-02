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

class Admin_Product_Lines_Controller extends Controller
{
  /**
   * the index page of the user homes admin
   */
  public function index()
  {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_product_lines.html");
    $view->content->products = ORM::factory("bp_product")->order_by("name")->find_all();

    print $view;
  }

  public function add_product_form() {
    print bp_product::get_add_form_admin();
  }


  public function add_product() {
    access::verify_csrf();

    $form = bp_product::get_add_form_admin();
    $valid = $form->validate();
    $name = $form->add_product->inputs["name"]->value;
    $product = ORM::factory("bp_product")->where("name", "=", $name)->find();
    if ($product->loaded()) {
      $form->add_product->inputs["name"]->add_error("in_use", 1);
      $valid = false;
    }

    if ($valid) {
      $product = bp_product::create(
        $name,
        $form->add_product->cost->value,
        $form->add_product->description->value,
        $form->add_product->postage_band->value
        );

      $product->save();
      message::success(t("Created product %product_name", array(
        "product_name" => html::clean($product->name))));
      print json::reply(array("result" => "success"));
    } else {
      print $form;
    }
  }

  public function delete_product_form($id) {
    $product = ORM::factory("bp_product", $id);
    if (!$product->loaded()) {
      kohana::show_404();
    }
    print bp_product::get_delete_form_admin($product);
  }

  public function delete_product($id) {
    access::verify_csrf();

    $product = ORM::factory("bp_product", $id);
    if (!$product->loaded()) {
      kohana::show_404();
    }

    $form = bp_product::get_delete_form_admin($product);
    if($form->validate()) {
      $name = $product->name;
      $product->delete();
    } else {
      print $form;
    }

    $message = t("Deleted user %product_name", array("product_name" => html::clean($name)));
    log::success("user", $message);
    message::success($message);
    print json::reply(array("result" => "success"));
  }

  public function edit_product($id) {
    access::verify_csrf();

    $product = ORM::factory("bp_product", $id);
    if (!$product->loaded()) {
      kohana::show_404();
    }

    $form = bp_product::get_edit_form_admin($product);
    $valid = $form->validate();
    if ($valid) {
      $new_name = $form->edit_product->inputs["name"]->value;
      if ($new_name != $product->name &&
          ORM::factory("bp_product")
          ->where("name", "=", $new_name)
          ->where("id","!=", $product->id)
          ->find()
          ->loaded()) {
        $form->edit_product->inputs["name"]->add_error("in_use", 1);
        $valid = false;
      } else {
        $product->name = $new_name;
      }
      $product->cost = $form->edit_product->cost->value;
      $product->description = $form->edit_product->description->value;
      $product->bp_postage_band_id = $form->edit_product->postage_band->value;
      $product->save();

      message::success(t("Changed product %product_name",array("product_name" => html::clean($product->name))));
      print json::reply(array("result" => "success"));
    } else {
      print $form;
    }
  }

  public function edit_product_form($id) {
    $product = ORM::factory("bp_product", $id);
    if (!$product->loaded()) {
      kohana::show_404();
    }

    $form = bp_product::get_edit_form_admin($product);

    print $form;
  }

}