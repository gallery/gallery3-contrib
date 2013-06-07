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
class Admin_Movie_overlay_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var(
        "movie_overlay", "icon", $form->movie_overlay->icon->value);
      module::set_var(
        "movie_overlay", "trans", $form->movie_overlay->trans->value);
	  module::set_var(
	    "movie_overlay", "time", $form->movie_overlay->time->value);
		
      message::success(t("Your settings have been saved."));
      url::redirect("admin/movie_overlay");
    }

    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_movie_overlay.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_form() {
    for ($i = 1; $i <= 10; $i++) {
      $range[$i] = "$i";
	}
	for ($i = 5; $i <= 95; $i+=5) {
      $range2[$i] = "$i";
	}
    $form = new Forge("admin/movie_overlay/handler", "", "post", array("id" => "g-admin-form"));
    $group = $form->group("movie_overlay");
    $group->dropdown("icon")->label(t("Choose the icon of the movie play button"))
      	->options($range)
      	->selected(module::get_var("movie_overlay", "icon", "1"));
    $group->dropdown("trans")->label(t("Choose the visability of the play button."))
      	->options($range2)
      	->selected(module::get_var("movie_overlay", "trans", "90"));
	$group->checkbox("time")->label(t("Show durration of movie (ffmpeg required)"))
		->checked(module::get_var("movie_overlay", "time", "0"));
		
    $group->submit("submit")->value(t("Save"));

    return $form;
  }
}