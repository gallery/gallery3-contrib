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

class Admin_Product_Lines_Controller extends Controller
{
  /**
   * the index page of the user homes admin
   */
  public function index()
  {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_product_lines.html");
    $view->content->products = ORM::factory("product")->order_by("name")->find_all();

    print $view;
  }

  public function add_product_form() {
    print product::get_add_form_admin();
  }


  public function add_product() {
    access::verify_csrf();

    $form = product::get_add_form_admin();
    try {
      $valid = $form->validate();
      $product = ORM::factory("product");
      $product->name = $form->add_product->inputs["name"]->value;
      $product->description = $form->add_product->description->value;
      $product->postage_band_id = $form->add_product->postage_band->value;
      $product->validate();
    } catch (ORM_Validation_Exception $e) {
      // Translate ORM validation errors into form error messages
      foreach ($e->validation->errors() as $key => $error) {
        $form->add_product->inputs[$key]->add_error($error, 1);
      }
      $valid = false;
    }

    if ($valid) {
      $product->save();
      message::success(t("Created product %product_name", array(
        "product_name" => html::clean($product->name))));
      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }

  public function delete_product_form($id) {
    $product = ORM::factory("product", $id);
    if (!$product->loaded()) {
      throw new Kohana_404_Exception();
    }
    print product::get_delete_form_admin($product);
  }

  public function delete_product($id) {
    access::verify_csrf();

    if ($id == user::active()->id || $id == user::guest()->id) {
      access::forbidden();
    }

    $product = ORM::factory("product", $id);
    if (!$product->loaded()) {
      throw new Kohana_404_Exception();
    }

    $form = product::get_delete_form_admin($product);
    if($form->validate()) {
      $name = $product->name;
      $product->delete();
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }

    $message = t("Deleted user %product_name", array("product_name" => html::clean($name)));
    log::success("user", $message);
    message::success($message);
    json::reply(array("result" => "success"));
  }

  public function edit_product($id) {
    access::verify_csrf();

    $product = ORM::factory("product", $id);
    if (!$product->loaded()) {
      throw new Kohana_404_Exception();
    }

    $form = product::get_edit_form_admin($product);
    try {
      $valid = $form->validate();
      $product->name = $form->edit_product->inputs["name"]->value;
      $product->cost = $form->edit_product->cost->value;
      $product->description = $form->edit_product->description->value;
      $product->postage_band_id = $form->edit_product->postage_band->value;
      $product->validate();
    } catch (ORM_Validation_Exception $e) {
      // Translate ORM validation errors into form error messages
      foreach ($e->validation->errors() as $key => $error) {
        $form->edit_product->inputs[$key]->add_error($error, 1);
      }
      $valid = false;
    }

    if ($valid) {
      $product->save();
      message::success(t("Changed product %product_name",
          array("product_name" => html::clean($product->name))));
      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }

  public function edit_product_form($id) {
    $product = ORM::factory("product", $id);
    if (!$product->loaded()) {
      throw new Kohana_404_Exception();
    }

    $form = product::get_edit_form_admin($product);

    print $form;
  }

}