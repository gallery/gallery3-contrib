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
class G1_Controller extends Controller {
  /**
   * Redirect Gallery 1 urls to their appropriate matching Gallery 3 url.
   *
   * We use mod_rewrite to create this path, so Gallery 1 urls like this:
   *   /gallery/Wedding/
   *   /gallery/Wedding/aaa
   *   /albums/Wedding/aaa.jpg
   *
   * Show up here like this:
   *   /g1/map?path=/gallery/Wedding/
   *   /g1/map?path=/gallery/Wedding/aaa
   *   /g1/map?path=/albums/Wedding/aaa.jpg
   */
  public function map() {
    $input = Input::instance();
    $path = $input->get('path');
    $page = $input->get('page');
    if(!is_numeric($page)) $page = '';

    if($path=='view_album.php' || $path=='slideshow.php') $path = $input->get('set_albumName');
    if($path=='view_photo.php') $path = $input->get('set_albumName').'/'.$input->get('id');
    
    if($path=='main.php') { //we do only name based g2 mapping here
      $item = item::root();
      access::required('view', $item);
      url::redirect($item->abs_url(), '301');
    }

    // Item names come in as FolderX/ItemX
    $album = 0;
    $pos = strrpos($path, '/');
    if($pos!==false) {
      // Get ItemX into g1_item
      $g1_item = substr($path,$pos+1,strlen($path));
      // Get FolderX into g1_item
      $g1_album = substr($path,0,$pos);
    }
    else {
      $album = 1;
      $g1_item = '';
      $g1_album = $path;
    }

    // Only binary files (the item itself, not the html) may have file extensions
    $binary = 0;
    $pos = strrpos($g1_item, '.');
    if($pos!==false) {
      $binary = 1;
      $g1_item = substr($g1_item, 0, $pos);
    }
    
    if(($pos=strrpos($g1_item, '.sized'))!==false||($pos=strrpos($g1_item, '.thumb'))!==false) {
      $mapping = ORM::factory('g1_map')->where('album', '=', $g1_album)->where('item', '=', substr($g1_item,0, $pos))->where('resource_type', '=', $album ? 'album':'item')->find();
    }
    else {
      $mapping = ORM::factory('g1_map')->where('album', '=', $g1_album)->where('item', '=', $g1_item)->where('resource_type', '=', $album ? 'album':'item')->find();
    }
    if(!$mapping->loaded()) {
      throw new Kohana_404_Exception();
    }
    $item = ORM::factory('item', $mapping->id);
    if (!$item->loaded()) {
      throw new Kohana_404_Exception();
    }
    access::required('view', $item);

    if($binary) {
		  if(strrpos($g1_item, '.sized')!==false) {
      	url::redirect($item->resize_url(true), '301');
      }
		  else if(strrpos($g1_item, '.thumb')!==false) {
      	url::redirect($item->thumb_url(true), '301');
      }
      else {
      	url::redirect($item->file_url(true), '301');
      }
    }
    else {
      $url = $item->abs_url();
      if($page!='') {
        $url .= (strpos($url,'?')!==false ? '&':'?').'page='.$page;
      }
      url::redirect($url, '301');
    }
  }
}
