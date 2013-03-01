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
class contactowner_block_Core {
  static function get_site_list() {
    return array("contact_owner" => t("Contact Owner"));
  }

  static function get($block_id, $theme) {
    $block = "";

    switch ($block_id) {
    case "contact_owner":

      // Create a new block to display the links in.
      $block = new Block();
      $block->css_id = "g-contact-owner";
      $block->title = t("Contact");
      $block->content = new View("contactowner_block.html");

      // if $displayBlock is true, this block will be displayed,
      //  if there aren't any links to put in the block for whatever reason
      //  then $displayBlock will rename set to false and the
      //  block will not be displayed.
      $displayBlock = false;

      if ($theme->item()) {
        // Locate the record for the user that created the current item.
        //   Their name will be displayed as part of the contact link.
        $userDetails = ORM::factory("user")
          ->where("id", "=", $theme->item->owner_id)
          ->find_all();

        // Figure out if the contact item owner email link should be displayed.
        //   only display it if the current owner has an email address and
        //   the option for allowing item owners to be contacted is set to true.
        if ((count($userDetails) > 0) && ($userDetails[0]->email != "") &&
            (module::get_var("contactowner", "contact_user_link") == true)) {
          $block->content->userLink = "<a href=\"" . url::site("contactowner/emailid/" .
                                      $theme->item->owner_id) . "/" . $theme->item->id . "\" class='g-dialog-link'>" . t("Contact") . " " .
                                      $userDetails[0]->name . "</a>";
          $displayBlock = true;
        }
      }

      // Figure out if the contact site owner link should be displayed.
      if (module::get_var("contactowner", "contact_owner_link")) {
        if ($theme->item()) {
          $block->content->ownerLink = "<a href=\"" . url::site("contactowner/emailowner") . "/" . $theme->item->id . 
                                       "\" class='g-dialog-link'>" . t(module::get_var("contactowner", "contact_button_text")) . "</a>";
        } else {
          $block->content->ownerLink = "<a href=\"" . url::site("contactowner/emailowner") .
                                       "\" class='g-dialog-link'>" . t(module::get_var("contactowner", "contact_button_text")) . "</a>";
        }
        $displayBlock = true;
      }

      break;
    }

    if ($displayBlock) {
      return $block;
    } else {
      return "";
    }
  }
}
