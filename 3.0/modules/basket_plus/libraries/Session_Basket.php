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
class basket_item
{
  public $product;
  public $item;
  public $quantity;

  public $cost = 0;
  public $cost_per = 0;

  public $items;

  public function __construct($aProduct, $aItem, $aQuantity){
    // TODO check individual product.
    $this->product = $aProduct;
    $this->item = $aItem;
    $this->quantity = $aQuantity;
    $this->calculate_cost();
  }

  private function calculate_cost(){
    $prod = ORM::factory("product", $this->product);
    $this->cost = $prod->cost * $this->quantity;
    $this->cost_per = $prod->cost;
  }
// PUBLIC FUNCTIONS
  public function add($quantity){
    $this->quantity += $quantity;
    $this->calculate_cost();
  }

  public function size(){
    return $this->quantity;
  }

  public function getItem(){
     $photo = ORM::factory("item", $this->item);
     return $photo;
  }

  public function product_description(){
     $prod = ORM::factory("product", $this->product);
     return $prod->description;
  }
/* added for basket sidebar labels */
  public function product_name(){
     $prod = ORM::factory("product", $this->product);
     return $prod->name;
  }

  public function getProduct(){
     $prod = ORM::factory("product", $this->product);
     return $prod;
   }

  public function getCode(){
     $photo = ORM::factory("item", $this->item);
     $prod = ORM::factory("product", $this->product);
     return $photo->id." - ".$photo->title." - ".$prod->name;
  }
}

class Session_Basket_Core {

  public $contents = array();

// added for customer record
  public $title = "";
  public $initials = "";
  public $insertion = "";

  public $name = "";
  public $house = "";
  public $street = "";
  public $suburb = "";
  public $town = "";
  public $postcode = "";
  public $email = "";
  public $phone = "";
// added for user comment
  public $comments = "";
// added for reference with pickup
  public $childname = "";
  public $childgroup = "";
// added for agreement to General Terms
  public $agreeterms = "";
  
  public $ppenabled = true;

//clear the basket
  public function clear(){
    if (isset($this->contents)){
      foreach ($this->contents as $key => $item){
        unset($this->contents[$key]);
      }
    }
    $this->ppenabled = true;
  }

//enable/disble pack&post
  public function enablepp(){
    $this->ppenabled = true;
  }

  public function disablepp(){
    $this->ppenabled = false;
  }

//get pack&post choice
  public function ispp(){
    return $this->ppenabled;
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
      // create array of postage bands
      foreach ($this->contents as $product => $basket_item){
        $postage_band = $basket_item->getProduct()->postage_band;
        if (isset($postage_bands[$postage_band->id])){
          $postage_quantities[$postage_band->id] += $basket_item->quantity;
        }
        else{
          $postage_quantities[$postage_band->id] = $basket_item->quantity;
          $postage_bands[$postage_band->id] = $postage_band;
        }
      }

      foreach ($postage_bands as $id => $postage_band){
        $postage_cost += $postage_band->flat_rate + ($postage_band->per_item * $postage_quantities[$id]);
      }
    }
    return $postage_cost;
  }

  //calculate total basket cost 
	public function cost(){
    $cost = 0;
    if (isset($this->contents)){
      foreach ($this->contents as $product => $basket_item){
        $cost += $basket_item->cost;
      }
    }
    return $cost;
  }

  //return the basket of the session
	public static function get(){
    return Session::instance()->get("basket");
  }

  public static function getOrCreate(){
    $session = Session::instance();

    $basket = $session->get("basket");
    if (!$basket){
      $basket = new Session_Basket();
      $session->set("basket", $basket);
    }
    return $basket;
  }
}