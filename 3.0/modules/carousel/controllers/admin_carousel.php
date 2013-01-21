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
class Admin_Carousel_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var(
        "carousel", "circular", $form->carousel->circular->value);
      module::set_var(
        "carousel", "autoscroll", $form->carousel->autoscroll->value);
      module::set_var(
        "carousel", "autostart", $form->carousel->autostart->value);
      module::set_var(
        "carousel", "speed", $form->carousel->speed->value);
      module::set_var(
        "carousel", "mousewheel", $form->carousel->mousewheel->value);

      module::set_var(
        "carousel", "title2", $form->recent->title2->value);
      module::set_var(
        "carousel", "thumbsize2", $form->recent->thumbsize2->value);
      module::set_var(
	  	"carousel", "visible2", $form->recent->visible2->value);
      module::set_var(
        "carousel", "quantity2", $form->recent->quantity2->value);
      module::set_var(
        "carousel", "onphoto2", $form->recent->onphoto2->value);
      module::set_var(
        "carousel", "onalbum2", $form->recent->onalbum2->value);
        
      module::set_var(
        "carousel", "title3", $form->popular->title3->value);
	  module::set_var(
        "carousel", "thumbsize3", $form->popular->thumbsize3->value);
	  module::set_var(
        "carousel", "visible3", $form->popular->visible3->value);
      module::set_var(
        "carousel", "quantity3", $form->popular->quantity3->value);
      module::set_var(
        "carousel", "onphoto3", $form->popular->onphoto3->value);
      module::set_var(
        "carousel", "onalbum3", $form->popular->onalbum3->value);

      module::set_var(
        "carousel", "title", $form->random->title->value);
	  module::set_var(
        "carousel", "thumbsize", $form->random->thumbsize->value);
	  module::set_var(
        "carousel", "visible", $form->random->visible->value);
      module::set_var(
        "carousel", "quantity", $form->random->quantity->value);
      module::set_var(
        "carousel", "onphoto", $form->random->onphoto->value);
      module::set_var(
        "carousel", "onalbum", $form->random->onalbum->value);
        
      message::success(t("Your settings have been saved."));
      url::redirect("admin/carousel");
	}
    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_carousel.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_form() {
    for ($i = 5; $i <= 50; $i+=5) {
      $range[$i] = "$i";
	   }
	$shortrange = array();
	for ($i = 1; $i < 21; $i++) {
		$key=((float)$i / 2);
  		$shortrange["$key"] = sprintf("%.1f", (float)$i / 2);
  	}
  	if (module::get_var("carousel", "autoscroll") == true) {
		$disableme = "false"; 
		} else {
		$disableme = "true";
  	}
    $form = new Forge("admin/carousel/handler", "", "post", array("id" => "g-admin-form"));
    
    $group = $form->group("carousel")->label(t("General carousel settings"));
	$group->checkbox("circular")->label(t('Enable the carousel to be circular so it starts over again from the beginning.'))
		->checked(module::get_var("carousel", "circular", "0"));
    $group->checkbox("autoscroll")->label(t('Carousel should auto scroll. Toggle value to change settings below.'))
        ->onClick("toggle()")
        ->id("autoscroll")
		->checked(module::get_var("carousel", "autoscroll", "0"));
    $group->input("autostart")->label(t("Enter the value of the auto start. (800)"))
      	->value(module::get_var("carousel", "autostart", "800"))
      	->id("auto")
      	->disabled("false")
      	->rules("valid_numeric|length[1,5]");
    $group->input("speed")->label(t('Enter the scrolling speed of the carousel. (1000)'))
		->value(module::get_var("carousel", "speed", "1000"))
		->id("speed")
		->disabled($disableme)
		->rules("valid_numeric|length[1,5]");
	$group->checkbox("mousewheel")->label(t('Enable mouse wheel.  Allows for mouse wheel to scroll items.'))
		->checked(module::get_var("carousel", "mousewheel", "0"));
	  
    $group = $form->group("recent")->label(t("Recent carousel block"));
	$group->input("title2")->label(t('Enter the title of the recent block.'))
		->value(module::get_var("carousel", "title2", "Recent items"));
    $group->input("thumbsize2")->label(t('Enter the size of the thumbs. (pixels)'))
		->value(module::get_var("carousel", "thumbsize2", "200"))
		->rules("valid_numeric|length[2,3]");
    $group->dropdown("visible2")->label(t('Enter number of thumbs to show. (height of carousel)'))
    	->options($shortrange)
		->selected(module::get_var("carousel", "visible2", "1"));
    $group->dropdown("quantity2")->label(t("Choose the total quantity of thumbs in recent carousel."))
      	->options($range)
      	->selected(module::get_var("carousel", "quantity2", "25"));
	$group->checkbox("onalbum2")->label(t("Show on album & collection pages"))
		->checked(module::get_var("carousel", "onalbum2", "0"));
	$group->checkbox("onphoto2")->label(t("Show on photo pages"))
		->checked(module::get_var("carousel", "onphoto2", "0")); 
   
    $group = $form->group("popular")->label(t("Popular carousel block"));
	$group->input("title3")->label(t('Enter the title of the popular block.'))
		->value(module::get_var("carousel", "title3", "Popular items"));
    $group->input("thumbsize3")->label(t('Enter the thumb size. (pixels)'))
		->value(module::get_var("carousel", "thumbsize3", "200"))
		->rules("valid_numeric|length[2,3]");
    $group->dropdown("visible3")->label(t('Enter number of thumbs to show. (height of carousel)'))
    	->options($shortrange)
		->selected(module::get_var("carousel", "visible3", "1"));
    $group->dropdown("quantity3")->label(t("Choose the total quantity of thumbs in popular carousel."))
      	->options($range)
      	->selected(module::get_var("carousel", "quantity3", "25"));
	$group->checkbox("onalbum3")->label(t("Show on album & collection pages"))
		->checked(module::get_var("carousel", "onalbum3", "0"));
	$group->checkbox("onphoto3")->label(t("Show on photo pages"))
		->checked(module::get_var("carousel", "onphoto3", "0"));

    $group = $form->group("random")->label(t("Random carousel block.  Some issues with smaller galleries."));
	$group->input("title")->label(t('Enter the title of the random block.'))
		->value(module::get_var("carousel", "title", "Random items"));
    $group->input("thumbsize")->label(t('Enter the thumb size. (pixels)'))
		->value(module::get_var("carousel", "thumbsize", "200"))
		->rules("valid_numeric|length[2,3]");
    $group->dropdown("visible")->label(t('Enter number of thumbs to show. (height of carousel)'))
    	->options($shortrange)
		->selected(module::get_var("carousel", "visible", "1"));
    $group->dropdown("quantity")->label(t("Choose the total quantity of thumbs in random carousel."))
      	->options($range)
      	->selected(module::get_var("carousel", "quantity", "25"));
	$group->checkbox("onalbum")->label(t("Show on album & collection pages"))
		->checked(module::get_var("carousel", "onalbum", "0"));
	$group->checkbox("onphoto")->label(t("Show on photo pages"))
		->checked(module::get_var("carousel", "onphoto", "0"));

	$form->submit("submit")->value(t("Save"));
    return $form;
  }
}
