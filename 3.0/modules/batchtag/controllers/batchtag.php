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
class BatchTag_Controller extends Controller {
  public function tagitems() {
    // Tag all non-album items in the current album with the specified tags.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    $input = Input::instance();
    url::redirect(url::abs_site("batchtag/tagitems2?name={$input->post('name')}&item_id={$input->post('item_id')}&tag_subitems={$input->post('tag_subitems')}&csrf={$input->post('csrf')}"));

  }
  
  public function tagitems2() {
    // Tag all non-album items in the current album with the specified tags.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    $input = Input::instance();

    // Variables
    if (($input->get("batchtag_max") == false) || ($input->get("batchtag_max") == "0")) {
      $batchtag_max = "50";
    } else {
      $batchtag_max = $input->get("batchtag_max");      
    }
    if ($input->get("batchtag_items_processed") == false) {
      $batchtag_items_processed = "0";
    } else {
      $batchtag_items_processed = $input->get("batchtag_items_processed");
    }
    
    // Figure out if the contents of sub-albums should also be tagged
    $str_tag_subitems = $input->get("tag_subitems");

    $children = "";
    if ($str_tag_subitems == false) {
      // Generate an array of all non-album items in the current album.
      $children = ORM::factory("item")
        ->where("parent_id", "=", $input->get("item_id"))
        ->where("type", "!=", "album")
        ->find_all();
    } else {
      // Generate an array of all non-album items in the current album
      //   and any sub albums.
      $item = ORM::factory("item", $input->get("item_id"));
      $children = $item->descendants();
    }
    
    // Loop through each item in the album and make sure the user has
    //   access to view and edit it.
    $children_count = "0";
    $tag_count = "0";

    //echo Kohana::debug($children);

    echo '<style>.continue { margin: 5em auto; text-align: center; }</style>';

    foreach ($children as $child) {
      
      if ($tag_count < $batchtag_max) {
      
        if ($children_count >= $batchtag_items_processed) {
          if (access::can("view", $child) && access::can("edit", $child) && !$child->is_album()) {

            // Assuming the user can view/edit the current item, loop
            //   through each tag that was submitted and apply it to
            //   the current item.
            foreach (explode(",", $input->get("name")) as $tag_name) {
              $tag_name = trim($tag_name);
              if ($tag_name) {
                tag::add($child, $tag_name);
              }
              // $tag_count should be inside the foreach loop as it is depending on the number of time tag:add is run
              $tag_count++;
            }
          }
          echo '<style>.c' . $children_count . ' { display:none; }</style>' . "\n";
          $children_count++;
          $batchtag_max_new = $tag_count;
          echo '<div class="continue c' . $children_count . '"><a href="' . url::abs_site("batchtag/tagitems2?name={$input->get('name')}&item_id={$input->get('item_id')}&tag_subitems={$input->get('tag_subitems')}&batchtag_items_processed=$children_count&batchtag_max=$batchtag_max_new&csrf={$input->get('csrf')}") . '">Continue</a></div>';
        } else { $children_count++; }
        
      } else { break; }

    }

    if ($tag_count < $batchtag_max) {
      // Redirect back to the album.
      $item = ORM::factory("item", $input->get("item_id"));
      url::redirect(url::abs_site("{$item->type}s/{$item->id}"));
      //echo url::abs_site("{$item->type}s/{$item->id}");
    } else {
      url::redirect(url::abs_site("batchtag/tagitems2?name={$input->get('name')}&item_id={$input->get('item_id')}&tag_subitems={$input->get('tag_subitems')}&batchtag_items_processed=$children_count&batchtag_max=$batchtag_max&csrf={$input->get('csrf')}"));
      //echo url::abs_site("batchtag/tagitems2?name={$input->get('name')}&item_id={$input->get('item_id')}&tag_subitems={$input->get('tag_subitems')}&batchtag_items_processed=$children_count&batchtag_max=$batchtag_max&csrf={$input->get('csrf')}");
    }
  }
}
