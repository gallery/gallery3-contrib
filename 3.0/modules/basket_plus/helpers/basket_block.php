<?php defined("SYSPATH") or die("No direct script access.");

class basket_block_Core {
  static function get_site_list() {
    return array("shopping" => t("Basket"));
  }

  static function get($block_id, $theme) {
    $block = "";
    switch ($block_id) {
      case "shopping":
        $block = new Block();
        $block->css_id = "g-view-basket";
        $block->title = t("Basket");
        $block->content = new View("basket-side-bar.html");
        $block->content->basket = Session_Basket::get();
        break;
    }
    return $block;
  }
}