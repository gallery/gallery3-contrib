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

class basket_event_Core{
  /**
   * adds the shopping basket administration controls to the admin menu
   */
  static function admin_menu($menu, $theme){

    $menu->add_after("users_groups",
      $basket_menu = Menu::factory("submenu")
        ->id("basket_menu")
        ->label(t("Basket")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("configure")
        ->label(t("Configure"))
        ->url(url::site("admin/configure")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("templates")
        ->label(t("Templates"))
        ->url(url::site("admin/configure/templates")));
        $basket_menu->append(
      Menu::factory("link")
        ->id("product_line")
        ->label(t("Product Lines"))
        ->url(url::site("admin/product_lines")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("postage_bands")
        ->label(t("Postage Bands"))
        ->url(url::site("admin/postage_bands")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("view_orders")
        ->label(t("View Orders"))
        ->url(url::site("basket/view_orders")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("view_all_orders")
        ->label(t("View All Orders"))
        ->url(url::site("basket/view_all_orders")));
  }

  static function item_edit_form($item, $form){
   $group = $form->group("products")->label(t("Available Products"));

   $product_override = ORM::factory("product_override")->where('item_id', "=", $item->id)->find();
   $group->checkbox("all")->label(t("No products except.."));
   if ($product_override->loaded()){
     $group->all->checked($product_override->none);
   }

   $products = ORM::factory("product")->find_all();
   foreach ($products as $product){
      $p_group = $group->group("product_$product->id")->label(t("$product->description"));

      $description = $product->description;
      $cost = $product->cost;
      $checked = false;

      if ($product_override->loaded()){
        $item_product = ORM::factory("item_product")
            ->where('product_override_id', "=", $product_override->id)
            ->where('product_id', "=", $product->id)->find();
        if ($item_product->loaded()){
          $checked = $item_product->include;
          if ($item_product->cost != -1){
            $cost = $item_product->cost;
          }
        }
      }

      $p_group->checkbox("exclude_$product->id")->label($description)->checked($checked);
      $p_group->input("cost_$product->id")->label(t("Cost"))->value($cost);
      //$producta[$product->id] = $product->description." (".basket::formatMoney($product->cost).")";
   }
  }

  static function item_edit_form_completed($item, $form){
    $product_override = ORM::factory("product_override")->where('item_id', "=", $item->id)->find();

    if ($form->products->all->checked)
    {
      $product_override->none = $form->products->all->checked;
      $product_override->item_id=$item->id;
      $product_override->save();
      $products = ORM::factory("product")->find_all();
      foreach ($products as $product){
          $p_group = $form->products->__get("product_$product->id");
          $item_product = ORM::factory("item_product")
            ->where('product_override_id', "=", $product_override->id)
            ->where('product_id', "=", $product->id)->find();

          $item_product->include = $p_group->__get("exclude_$product->id")->checked;
          $item_product->cost = $p_group->__get("cost_$product->id")->value;
          $item_product->product_id = $product->id;
          $item_product->product_override_id = $product_override->id;
          $item_product->save();
      }
    }
    else
    {
      if ($product_override->loaded()){
        $product_override->delete();
      }
    }
  }
}