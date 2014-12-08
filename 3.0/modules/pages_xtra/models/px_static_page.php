<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2014 Bharat Mediratta
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
class PX_Static_Page_Model extends ORM {

/**  
   * Add a set of restrictions to any following queries to restrict access only to items
   * viewable by the active user.
   * @chainable
   */
  public function viewable() {
    return item::viewable($this);
  }

/**
   * Is this item a (static) page?
   * @return true if it's a page
   */
  public function is_page() {
    return $this->type == 'page';    
  }
  
/**
   * Return the server-relative url to this item,     
   * @param string $query the query string (eg "page=2")
   */
  public function url($query=null) {
    $url = url::site($this->relative_url());
    if ($query) {
      $url .= "?$query";
    }
    return $url;
  }

/**
   * Return the full url to this item, eg:  
   * @param string $query the query string (eg "page=2")
   */
/**  public function abs_url($query=null) {
    $url = url::abs_site($this->relative_url());
    if ($query) {
      $url .= "?$query";
    }
    return $url;
  }
*/

/**
   * Return the relative url to this item's file.
   * @return string
   */
  public function relative_url() {
    if (!$this->loaded()) {
      return;
    }

    if (!isset($this->relative_url_cache)) {
      $this->_build_relative_caches()->save();
    }
    return $this->relative_url_cache;
  }

// Next function from models/item.php - should work, but needs testing. Use function below instead.
/**  public function save() {
    $significant_changes = $this->changed;
    foreach (array("name", "description", "tags", "relative_url_cache", "html_code" ) as $key) {
      unset($significant_changes[$key]);
    }

    if ((!empty($this->changed) && $significant_changes) || isset($this->data_file)) {
      $this->updated = time();
   }
   return parent::save();
 } 
*/

// Change date in 'updated' column of PX_STATIC_PAGES . Used for Sitemap info - last modified. 
  public function save() {
    if (!empty($this->changed)) {
      $this->updated = time();
    }
    return parent::save();
  }
 
}