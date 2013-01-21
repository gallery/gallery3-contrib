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
class favourites_theme_Core {
  static function head($theme) {
    return $theme->css("favourites.css")
      . $theme->script("favourites.js");
  }

  static function header_top($theme) {

    if (favourites_configuration::isUsersOnly() && identity::active_user()->name =="guest"){
      return;
    }

    if ($theme->page_subtype=="favourites"){
      $view = new View("save_favourites.html");
      $view->favourites = Favourites::getOrCreate();
      return $view->render();
    }
    else{
      $view = new View("view_favourites.html");
      $view->favourites = Favourites::getOrCreate();
      return $view->render();
    }
  }

  static function photo_top($theme){
    if (!favourites_configuration::canSelectItems() ||
        (favourites_configuration::isUsersOnly() && identity::active_user()->name =="guest")){
      return;
    }

    $view = new View("add_to_favourites.html");
    $view->item = $theme->item();
    $view->favourites = Favourites::getOrCreate();
    return $view->render();
  }

  static function thumb_top($theme, $item){
    if (favourites_configuration::isUsersOnly() && identity::active_user()->name =="guest"){
      return;
    }

    if (($item->type=="album" && favourites_configuration::canSelectAlbums()) ||
      ($item->type!="album" && favourites_configuration::canSelectItems())){
      $view = new View("add_to_favourites.html");
      $view->item = $item;
      $view->favourites = Favourites::getOrCreate();
      return $view->render();
    }
  }

}