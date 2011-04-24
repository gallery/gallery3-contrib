<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class bitly_Controller extends Controller {

  /**
   * Shorten a G3 item's link and display the result in a status message.
   * @param int   $item_id
   */
  public function shorten($item_id) {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    $item = ORM::factory("item", $item_id);

    // Ensure user has permission
    access::required("view", $item);
    access::required("edit", $item);

    // Shorten the item's URL
    $short_url = bitly::shorten_url($item_id);
    
    if ($short_url) {
      message::success("Item URL shortened to $short_url");
    } else {
      message::error("Unable to shorten " . url::abs_site($item->relative_url_cache));
    }

    // Redirect back to the item
    url::redirect(url::abs_site($item->relative_url_cache));
  }

}