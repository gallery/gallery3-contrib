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

class basket_plus_event_Core{
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
        ->id("email_template")
        ->label(t("Email templates"))
        ->url(url::site("admin/email_templates")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("translates")
        ->label(t("Translations"))
        ->url(url::site("admin/configure/translates")));
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
        ->url(url::site("basket_plus/view_orders")));
    $basket_menu->append(
      Menu::factory("link")
        ->id("view_all_orders")
        ->label(t("View All Orders"))
        ->url(url::site("basket_plus/view_all_orders")));
  }

  static function item_edit_form($item, $form){
   $group = $form->group("products")->label(t("Available Products"));

   $product_override = ORM::factory("bp_product_override")->where('item_id', "=", $item->id)->find();
   $group->checkbox("all")->label(t("No products except.."));
   if ($product_override->loaded()){
     $group->all->checked($product_override->none);
   }

   $products = ORM::factory("bp_product")->find_all();
   foreach ($products as $product){
      $p_group = $group->group("product_$product->id")->label(t("$product->description"));

      $description = $product->description;
      $cost = $product->cost;
      $checked = false;

      if ($product_override->loaded()){
        $item_product = ORM::factory("bp_item_product")
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
      //$producta[$product->id] = $product->description." (".basket_plus::formatMoney($product->cost).")";
   }
  }

  static function item_edit_form_completed($item, $form){
    $product_override = ORM::factory("bp_product_override")->where('item_id', "=", $item->id)->find();

    if ($form->products->all->checked)
    {
      $product_override->none = $form->products->all->checked;
      $product_override->item_id=$item->id;
      $product_override->save();
      $products = ORM::factory("bp_product")->find_all();
      foreach ($products as $product){
				$p_group = $form->products->__get("product_$product->id");
				$item_product = ORM::factory("bp_item_product")
					->where('product_override_id', "=", $product_override->id)
					->where('product_id', "=", $product->id)->find();

				$item_product->include = $p_group->__get("exclude_$product->id")->checked;
				$item_product->cost = $p_group->__get("cost_$product->id")->value;
				$item_product->product_id = $product->id;
				$item_product->product_override_id = $product_override->id;
				$item_product->save();
      }
    }
    else{
      if ($product_override->loaded()){
        $product_override->delete();
      }
    }
  }

//========================================================================
// User basket functions
//========================================================================
	// Add fields to the user form
  static function user_add_form_admin($user, $form) {
    $form->add_user->input("pickup_location")
      ->label(t("Pickup Location"));
    $form->add_user->checkbox("is_group")
      ->label(t("Group of users"));
    $form->add_user->input("extra_order_info_lbl")
      ->label(t("Extra Order Info Label"));
    $form->add_user->input("extra_order_info")
      ->label(t("Extra Order Info"));
    $form->add_user->input("extra_order_info_lbl2")
      ->label(t("Extra Order Info Label 2"));
    $form->add_user->input("extra_order_info2")
      ->label(t("Extra Order Info 2"));
  }

  // Called after a new user has been added
  static function user_add_form_admin_completed($user, $form) {
		$user_basket = ORM::factory("bp_user_basket");
    $user_basket->id = $user->id;
    $user_basket->pickup_location = $form->add_user->pickup_location->value;
		$user_basket->is_group = $form->add_user->is_group->checked;
    $user_basket->extra_order_info_lbl = $form->add_user->extra_order_info_lbl->value;
    $user_basket->extra_order_info = $form->add_user->extra_order_info->value;
    $user_basket->extra_order_info_lbl2 = $form->add_user->extra_order_info_lbl2->value;
    $user_basket->extra_order_info2 = $form->add_user->extra_order_info2->value;	
    $user_basket->save();
  }

  //Called when admin is editing a user 
  static function user_edit_form_admin($user, $form) {
		$is_group="";
		$extra_order_info_lbl="";
		$extra_order_info="";
		$extra_order_info_lbl2="";
		$extra_order_info2="";
		
		$user_basket = ORM::factory("bp_user_basket")->where("id", "=", $user->id)->find();
    if ($user_basket->loaded()) {
			$is_group = $user_basket->is_group;
      $pickup_location = $user_basket->pickup_location;
      $extra_order_info_lbl = $user_basket->extra_order_info_lbl;
      $extra_order_info = $user_basket->extra_order_info;
      $extra_order_info_lbl2 = $user_basket->extra_order_info_lbl2;
      $extra_order_info2 = $user_basket->extra_order_info2;
    } 
		else {
      $pickup_location = "";
    }
    $form->edit_user->input("pickup_location")
      ->label(t("Pickup Location"))
      ->value($pickup_location);
    $form->edit_user->checkbox("is_group")
      ->label(t("Group of users"))
			->checked($is_group);
    $form->edit_user->input("extra_order_info_lbl")
      ->label(t("Extra Order Info Label"))
      ->value($extra_order_info_lbl);
    $form->edit_user->input("extra_order_info")
      ->label(t("Extra Order Info"))
      ->value($extra_order_info);
    $form->edit_user->input("extra_order_info_lbl2")
      ->label(t("Extra Order Info Label 2"))
      ->value($extra_order_info_lbl2);
    $form->edit_user->input("extra_order_info2")
      ->label(t("Extra Order Info 2"))
      ->value($extra_order_info2);
  }

  //Called after a user had been edited by the admin
  static function user_edit_form_admin_completed($user, $form) {
    $user_basket = ORM::factory("bp_user_basket")->where("id", "=", $user->id)->find();
    //check if the user_basket record exists
		if (!$user_basket->loaded()) { //if not, create a new record for the user
			$user_basket = ORM::factory("bp_user_basket");
			$user_basket->id = $user->id;
		}
		$user_basket->pickup_location = $form->edit_user->pickup_location->value;
		$user_basket->is_group = $form->edit_user->is_group->checked;
		$user_basket->extra_order_info_lbl = $form->edit_user->extra_order_info_lbl->value;
		$user_basket->extra_order_info = $form->edit_user->extra_order_info->value;
		$user_basket->extra_order_info_lbl2 = $form->edit_user->extra_order_info_lbl2->value;
		$user_basket->extra_order_info2 = $form->edit_user->extra_order_info2->value;
		$user_basket->save();
	}

}