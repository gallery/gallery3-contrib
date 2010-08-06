<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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
class ecards_Controller extends Controller {
  /**
   * Send the ecard.
   */
  public function send($id) {
    $item = ORM::factory("item", $id);
    access::required("view", $item);
    if (!ecard::can_send_ecard()) {
      access::forbidden();
    }

    $form = ecard::get_send_form($item);
    if ($form->validate()) {
      Kohana_Log::add("error",print_r($form,1));
      // Send the ecard here, based on the form data
      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }

  /**
   * Present a form for adding a new ecard to this item or editing an existing ecard.
   */
  public function form_send($item_id) {
    $item = ORM::factory("item", $item_id);
    access::required("view", $item);
    if (!ecard::can_send_ecard()) {
      access::forbidden();
    }

    print ecard::prefill_send_form(ecard::get_send_form($item));
  }
}
