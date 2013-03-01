<?php //defined("SYSPATH") or die("No direct script access.");
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
class Navcarousel_Controller extends Controller {
  public function item($itemid) {
    // This function creates the xml file for jCarousel
    
    $curritem = ORM::factory("item", $itemid);
    $parent = $curritem->parent();
    $item_count = -1;

    // Array indexes are 0-based, jCarousel positions are 1-based.
    $first = max(0, intval($_GET['first']) - 1);
    $last  = max($first + 1, intval($_GET['last']) - 1);

    $length = $last - $first + 1;

    // Build the array with the thumbnail URLs
    foreach ($parent->viewable()->children() as $photo) {
      if (!$photo->is_album()) { 
        $item_count++;
        $itemlist[$item_count] = $photo->thumb_url();
      }
    }

    $total    = count($itemlist);
    $selected = array_slice($itemlist, $first, $length);

    // ---

    header('Content-Type: text/xml');

    echo '<data>';

    // Return total number of images so the callback
    // can set the size of the carousel.
    echo '  <total>' . $total . '</total>';

    foreach ($selected as $img) {
        echo '  <image>' . $img . '</image>';
    }

    echo '</data>';

 }
}
