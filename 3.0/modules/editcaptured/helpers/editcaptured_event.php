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
class editcaptured_event_Core {
  static function item_edit_form($item, $form) {
    // Add a couple of drop-down boxes to allow the user to edit the date
    // that $item was captured.	  

    // We don't want to allow changes of the root album
    if ($item->id == 1)
      return;

    // Search if there are at least some child items containing a captured date
    $model = ORM::factory("item")
               ->where("parent_id", "=", $item->id)
               ->where("captured", "IS NOT", null)
               ->order_by("captured", "ASC");
    $first_child = $model->find();
    if (!$first_child->id) $first_child = null;

    // Chose preselected option, depending on wheather item already has captured date or not 
    if ($item->captured) {
      $dateoptions_preselect = "selected";
    } else {
      $dateoptions_preselect = "remove";
    }

    // Depending on wheather there are child items or not, we generate the dropdown options
    if ($first_child) {
      $dateoptions = array("selected" => t("Selected Date"),
			   "oldest" => t("Date of oldest Child"),
			   "youngest" => t("Date of youngest Child"),
			   "now" => t("Current Date"),
			   "remove" => t("Remove Date"));
      
      // If there are child items with captured date, we preset the date field with the oldest item
      if (!$item->captured && $first_child) {
        $item->captured = $first_child->captured;
        $dateoptions_preselect = "oldest";
      }
    } else {
      $dateoptions = array("selected" => t("Selected Date"),
			   "now" => t("Current Date"),
			   "remove" => t("Remove Date"));
    }
    
    // Add captured date field to the form
    $form->edit_item->dateselect("capturedate")
                    ->label(t("Captured"))
                    ->minutes(1)
                    ->years(1970, date('Y')+1)
		    ->value($item->captured);

    // Add dropdown menu for options to the form
    $form->edit_item->dropdown("capturedate_usedate")
	            ->options($dateoptions)
                    ->id("g-editcaptured-usedate")
		    ->selected($dateoptions_preselect);

    // Add checkbox for users who want to change the captured date of subitems, too
    if ($item->is_album()) {
      $form->edit_item->checkbox("capturedate_setsubitems")
                      ->label(t("Set also subitems's date"))
		      ->id("g-editcaptured-setsubitems");
    }
  }

  static function item_edit_form_completed($item, $form) {
    // Change the item's captured field to the specified value.
  
    // We don't want to change the root element, so check for that
    if ($item->id == 1) {
      return;
    }
    
    // Depending on the dropdown option, we set the date
    switch ($form->edit_item->capturedate_usedate->value) {
	    
      // Just use the date selected in the form
      case "selected":
	$item->captured = $form->edit_item->capturedate->value;      
	break;

      // Use the date of the oldest child (we check again if there is such a child)
      case "oldest":
        $model = ORM::factory("item")
	           ->where("parent_id", "=", $item->id)
                   ->where("captured", "IS NOT", null)
	           ->order_by("captured", "ASC");
	$first_child = $model->find();
	if ($first_child->id) {
          $item->captured = $first_child->captured;
	} else {
	  $item->captured = null;
	}
	break;

      // Use the date of the youngest child (we check again if there is such a child)
      case "youngest":
        $model = ORM::factory("item")
	           ->where("parent_id", "=", $item->id)
                   ->where("captured", "IS NOT", null)
	           ->order_by("captured", "DESC");
        $first_child = $model->find();
	if ($first_child->id) {
          $item->captured = $first_child->captured;
	} else {
	  $item->captured = null;
	}
	break;

      // Use the current date
      case "now":
	$item->captured = time();
	break;
      
      // Remove the date
      case "remove":
	$item->captured = null;
    }

    $item->save();

    // Set the date also for all subitems (at the moment only direct subitems are supported
    if ($item->is_album() && $form->edit_item->capturedate_setsubitems->checked) {
      foreach (ORM::factory("item")->where("parent_id", "=", $item->id)->find_all() as $subitem) {
        if ($subitem->loaded() && access::can("edit", $subitem)) {
	  $subitem->captured = $item->captured;
          $subitem->save();
        }
      }
      //message::success(t("Changed captured date of subitems"));
    }
  
  }

}

