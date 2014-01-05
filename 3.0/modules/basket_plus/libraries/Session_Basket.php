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
class basket_item
{
  public $product;
  public $item;
  public $quantity;

  public $product_cost = 0;
  public $product_cost_per = 0;

  public $items;

  public function __construct($aProduct, $aItem, $aQuantity){
    // TODO check individual product.
    $this->product = $aProduct;
    $this->item = $aItem;
    $this->quantity = $aQuantity;
    $this->calculate_product_cost();
  }

  private function calculate_product_cost(){
    $prod = ORM::factory("bp_product", $this->product);
    $this->product_cost = $prod->cost * $this->quantity;
    $this->product_cost_per = $prod->cost;

    // check for product override
    $product_override = ORM::factory("bp_product_override")->where('item_id', "=", $this->item)->find();
    if ($product_override->loaded()){
      $item_product = ORM::factory("bp_item_product")
            ->where('product_override_id', "=", $product_override->id)
            ->where('product_id', "=", $this->product)->find();
      if ($item_product->loaded()){
				$this->product_cost_per = $item_product->cost;
				$this->product_cost = $this->product_cost_per * $this->quantity;
			}
		}
    if (!$product_override->loaded()){
      // no override found so check parents
      // check parents for product override
      $item = ORM::factory("item",$this->item);

      $parents = $item->parents();
      foreach ($parents as $parent){
        // check for product override
        $temp_override = ORM::factory("bp_product_override")->where('item_id', "=", $parent->id)->find();
        if ($temp_override ->loaded()){
          $product_override = $temp_override;
          //break;
        }
      }
    }
		$item_product = ORM::factory("bp_item_product")
					->where('product_override_id', "=", $product_override->id)
					->where('product_id', "=", $this->product)->find();
		if ($item_product->loaded()){
			$this->product_cost_per = $item_product->cost;
			$this->product_cost = $this->product_cost_per * $this->quantity;
		}
  }

	// PUBLIC FUNCTIONS
  public function add($quantity){
    $this->quantity += $quantity;
    $this->calculate_product_cost();
  }

  public function size(){
    return $this->quantity;
  }

  public function getItem(){
     $photo = ORM::factory("item", $this->item);
     return $photo;
  }

  public function product_description(){
     $prod = ORM::factory("bp_product", $this->product);
     return $prod->description;
  }
/* added for basket sidebar labels */
  public function product_name(){
     $prod = ORM::factory("bp_product", $this->product);
     return $prod->name;
  }

  public function getProduct(){
     $prod = ORM::factory("bp_product", $this->product);
     return $prod;
   }

  public function getCode(){
     $photo = ORM::factory("item", $this->item);
     $prod = ORM::factory("bp_product", $this->product);
     return $photo->id." - ".$photo->title." - ".$prod->name;
  }
}

class Session_Basket_Core {

  public $contents = array();

// added for customer record
  public $title = "";
  public $initials = "";
  public $insertion = "";

  public $fname = "";
  public $house = "";
  public $street = "";
  public $suburb = "";
  public $postalcode = "";
  public $town = "";
  public $province = "";
  public $country = "";
  public $email = "";
  public $phone = "";
// added for user comment
  public $comments = "";
// added for reference with delivery method 'pickup'
  public $order_ref1 = "";
  public $order_ref2 = "";
// added for agreement to General Terms
  public $agreeterms = "";
	public $paypal = "";
  //
	public $pickup = "";

//clear the basket
  public function clear(){
    if (isset($this->contents)){
      foreach ($this->contents as $key => $item){
        unset($this->contents[$key]);
      }
    }
	// get default pickup setting
    $this->pickup = basket_plus::getBasketVar(IS_PICKUP_DEFAULT);
  }

//enable pickup
  public function enable_pickup(){
    $this->pickup = true;
  }

//disable pickup
  public function disable_pickup(){
    $this->pickup = false;
  }

  private function create_key($product, $id){
    return "$product _ $id";
  }

//get size 
  public function size(){
    $size = 0;
    if (isset($this->contents)){
      foreach ($this->contents as $product => $basket_item){
        $size += $basket_item->size();
      }
    }
    return $size;
  }

	// to add an item to the basket
  public function add($id, $product, $quantity){
    $key = $this->create_key($product, $id);
    // add more of a product already in the basket 
		if (isset($this->contents[$key])){
      $this->contents[$key]->add($quantity);
    }
    // new product in the basket 
    else {
      $this->contents[$key] = new basket_item($product, $id, $quantity);
    }
  }

	// to remove an item from the basket
  public function remove($key){
    unset($this->contents[$key]);
  }

  //calculate total postage cost 
  public function postage_cost(){
    $postage_cost = 0;
    $postage_bands = array();
    $postage_quantities = array();
    if (isset($this->contents)){
      // create array of used postage bands and product qty's
      foreach ($this->contents as $product => $basket_item){
        $postage_band = $basket_item->getProduct()->bp_postage_band;
        if (isset($postage_bands[$postage_band->id])){
          $postage_quantities[$postage_band->id] += $basket_item->quantity;
        }
        else{
          $postage_quantities[$postage_band->id] = $basket_item->quantity;
          $postage_bands[$postage_band->id] = $postage_band;
        }
      }
			//calculate total postage: for each postband used in the ordered products: flatrate + 'per item'-rate * qty
      foreach ($postage_bands as $id => $postage_band){
        $postage_cost += $postage_band->flat_rate + ($postage_band->per_item * $postage_quantities[$id]);
      }		
		}
    return $postage_cost;
  }

  //calculate total basket product cost
	public function product_cost(){
    $product_cost = 0;
    if (isset($this->contents)){
      foreach ($this->contents as $product => $basket_item){
        $product_cost += $basket_item->product_cost;
      }
    }
    return $product_cost;
  }

  //return the basket of the session
	public static function get(){
    return Session::instance()->get("basket");
  }

  //get the current basket, or create a new basket, if there isn't one
  public static function getOrCreate(){	
		$session = Session::instance();

		$basket = self::get();
    if (!$basket){
      $basket = new Session_Basket();
      $session->set("basket", $basket);
    }
    return $basket;
  }
}