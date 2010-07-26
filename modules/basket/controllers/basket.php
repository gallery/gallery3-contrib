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
class Basket_Controller extends Controller {

  public function view_basket() {

    $template = new Theme_View("page.html", "other", "basket");

    $view = new View("view_basket.html");
    $view->basket = Session_Basket::get();

    $template->content = $view;

    print $template;
  }

    private function getCheckoutForm(){
    $form = new Forge("basket/confirm", "", "post", array("id" => "checkout", "name" =>"checkout"));
    $group = $form->group("contact")->label(t("Contact Details"));
    $group->input("fullname")->label(t("Name"))->id("fullname");
    $group->input("house")->label(t("House Number / Name"))->id("house");
    $group->input("street")->label(t("Street"))->id("street");
    $group->input("suburb")->label(t("Suburb"))->id("suburb");
    $group->input("town")->label(t("Town or City"))->id("town");
    $group->input("postcode")->label(t("Postcode"))->id("postcode");
    $group->input("email")->label(t("E-Mail Address"))->id("email");
    $group->input("phone")->label(t("Telephone Number"))->id("phone");

    return $form;
  }

  public function checkout () {

    $template = new Theme_View("page.html", "other", "basket");

    $view = new View("checkout.html");

    $basket = Session_Basket::get();

    $form = self::getCheckoutForm();
    $form->contact->fullname->value($basket->name);
    $form->contact->house->value($basket->house);
    $form->contact->street->value($basket->street);
    $form->contact->suburb->value($basket->suburb);
    $form->contact->town->value($basket->town);
    $form->contact->postcode->value($basket->postcode);
    $form->contact->email->value($basket->email);
    $form->contact->phone->value($basket->phone);
    $view->form = $form;

    $template->content = $view;


    print $template;
  }

  public function confirm () {
    access::verify_csrf();

    $form = $this->getCheckoutForm();

    $valid = $form->validate();

    if ($valid){
      $basket = Session_Basket::get();
      $basket->name = $form->contact->fullname->value;
      $basket->house = $form->contact->house->value;
      $basket->street = $form->contact->street->value;
      $basket->suburb = $form->contact->suburb->value;
      $basket->town = $form->contact->town->value;
      $basket->postcode = $form->contact->postcode->value;
      $basket->email = $form->contact->email->value;
      $basket->phone = $form->contact->phone->value;

      $template = new Theme_View("page.html", "other", "basket");

      $form = new Forge("basket/complete", "", "post", array("id" => "confirm", "name" =>"confirm"));
      $view = new View("confirm_order.html");
      $view->basket = $basket;
      $template->content = $view;
      $view->form = $form;
      print $template;
    }
    else
    {
      die("Invalid confirmation!");

    }
  }

  public function complete () {
    access::verify_csrf();
    $basket = Session_Basket::get();

    //$admin_address = basket::getEmailAddress();
    $postage = $basket->postage_cost();
    $product_cost = $basket->cost();

    $admin_email = "Order for :
".$basket->name."
".$basket->house."
".$basket->street."
".$basket->suburb."
".$basket->town."
".$basket->postcode."
".$basket->email."
".$basket->phone."
Placed at ".date("d F Y - H:i" ,time())."
Cost of Ordered Products = ".$product_cost."
Postage and Packaging Costs + ".$postage."
Total Owed ".($product_cost+$postage)." Total in ".basket::getCurrency()."

Items Ordered:

";

    // create the order items
    foreach ($basket->contents as $basket_item){
      $item = $basket_item->getItem();
      $prod = ORM::factory("product", $basket_item->product);
      $admin_email = $admin_email."
".$item->title." - ".$item->url()."
".$prod->name." - ".$prod->description."
".$basket_item->quantity." @ ".$prod->cost."

";
    }


    $from = "From: ".basket::getEmailAddress();
    mail(basket::getEmailAddress(), "Order from ".$basket->name, $admin_email, $from);

    $basket->clear();

    $template = new Theme_View("page.html", "other", "basket");
    $view = new View("order_complete.html");
    $template->content = $view;
    print $template;
  }

  private function getAddToBasketForm($id){

    $form = new Forge("basket/add_to_basket", "", "post", array("id" => "g-add-to-basket-form"));
    $group = $form->group("add_to_basket")->label(t("Add To Basket"));
    $group->hidden("id");
    $group->dropdown("product")
        ->label(t("Product"))
        ->options(product::getProductArray($id));
    $group->input("quantity")->label(t("Quantity"))->id("g-quantity");
    $group->submit("")->value(t("Add"));
    //$group->submit("proceedToCheckout")->value(t("Proceed To Checkout"));

    return $form;
  }

  public function add_to_basket(){

    access::verify_csrf();


    if (!isset($_POST['id']))
    {
      die("no id");
    }
    $form = self::getAddToBasketForm($_POST['id']);
    $valid = $form->validate();

    if ($valid){
    $basket = Session_Basket::getOrCreate();
    $basket->add(
      $form->add_to_basket->id->value,
      $form->add_to_basket->product->value,
      $form->add_to_basket->quantity->value);

    json::reply(array("result" => "success"));
    }
    else
    {
      log_error("invalid form!");

    }

  }

  public function add_to_basket_ajax($id) {

    $view = new View("add_to_basket_ajax.html");

    // get the item to add
    $item = ORM::factory("item", $id);
    if (!$item->loaded())
    {
      //TODO
      die("Not loaded id");
    }

    // get the basket to add to
    $form = self::getAddToBasketForm($id);
    $form->add_to_basket->id->value($id);
    $form->add_to_basket->quantity->value(1);

    $view->form = $form;
    $view->item = $item;

    print $view;
  }

  public function remove_item($key) {

    $basket = Session_Basket::getOrCreate();
    $basket->remove($key);
    url::redirect("basket/view_basket");
  }

}
