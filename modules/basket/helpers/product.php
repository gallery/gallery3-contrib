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
class product_Core {

 static function get_add_form_admin() {
    $form = new Forge("admin/product_lines/add_product", "", "post", array("id" => "g-add-product-form"));
    $group = $form->group("add_product")->label(t("Add Product"));
    $group->input("name")->label(t("Name"))->id("g-product-name")
      ->error_messages("in_use", t("There is already a product with that name"));
    $group->input("cost")->label(t("Cost"))->id("gCost");
    $group->input("description")->label(t("Description"))->id("g-description");
    $group->dropdown("postage_band")
        ->label(t("Postage Band"))
        ->options(postage_band::getPostageArray());
    $group->submit("")->value(t("Add Product"));
    return $form;
  }

  static function get_edit_form_admin($product) {
    $form = new Forge("admin/product_lines/edit_product/$product->id", "", "post",
        array("id" => "g-edit-product-form"));
    $group = $form->group("edit_product")->label(t("Edit Product"));
    $group->input("name")->label(t("Name"))->id("g-product-name")->value($product->name);
    $group->inputs["name"]->error_messages(
      "in_use", t("There is already a product with that name"));
    $group->input("cost")->label(t("Cost"))->id("g-cost")->value($product->cost);
    $group->input("description")->label(t("Description"))->id("g-description")->
      value($product->description);
    $group->dropdown("postage_band")
        ->label(t("Postage Band"))
        ->options(postage_band::getPostageArray())
        ->selected($product->postage_band_id);

    $group->submit("")->value(t("Modify Product"));
    return $form;
  }


  static function get_delete_form_admin($product) {
    $form = new Forge("admin/product_lines/delete_product/$product->id", "", "post",
                      array("id" => "g-delete-product-form"));
    $group = $form->group("delete_product")->label(
      t("Are you sure you want to delete product %name?", array("name" => $product->name)));
    $group->submit("")->value(t("Delete product %name", array("name" => $product->name)));
    return $form;
  }

  static function getProductArray($id){
    $producta = array();
    // check for product override
    $product_override = ORM::factory("product_override")->where('item_id', "=", $id)->find();

    if (!$product_override->loaded()){
      // no override found so check parents
      // check parents for product override
      $item = ORM::factory("item",$id);

      $parents = $item->parents();
      foreach ($parents as $parent){
        // check for product override
        $product_override = ORM::factory("product_override")->where('item_id', "=", $parent->id)->find();
        if ($product_override->loaded()){
          break;
        }
      }
    }

    $products = ORM::factory("product")->find_all();
    foreach ($products as $product){
      $show = true;
      $cost = $product->cost;
      if ($product_override->loaded()){
        $show = !$product_override->none;
        $item_product = ORM::factory("item_product")
          ->where('product_override_id', "=", $product_override->id)
          ->where('product_id', "=", $product->id)->find();

        if ($item_product->loaded()){
          $cost = $item_product->cost;
          if (!$show){
            $show = $item_product->include;
          }
        }
      }

      if ($show)
      {
        $producta[$product->id] = $product->description." (".basket::formatMoney($cost).")";
      }
    }

    return $producta;
  }

  static function isForSale($id){

    // check for product override
    $product_override = ORM::factory("product_override")->where('item_id', "=", $id)->find();

    if (!$product_override->loaded()){
      // no override found so check parents
      // check parents for product override
      $item = ORM::factory("item",$id);

      $parents = $item->parents();
      foreach ($parents as $parent){
        // check for product override
        $product_override = ORM::factory("product_override")->where('item_id', "=", $parent->id)->find();
        if ($product_override->loaded()){
          break;
        }
      }
    }

    $products = ORM::factory("product")->find_all();

    if ($product_override->loaded() && $product_override->none){

      foreach ($products as $product){

        $item_product = ORM::factory("item_product")
          ->where('product_override_id', "=", $product_override->id)
          ->where('product_id', "=", $product->id)->find();

        if ($item_product->loaded()){

          if ($item_product->include){
            return true;
          }
        }
      }

      return false;

    } else {
      return count($products) > 0;
    }
  }
}