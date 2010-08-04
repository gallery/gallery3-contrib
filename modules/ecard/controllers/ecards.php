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
   * Add a new ecard to the collection.
   */
  public function create($id) {
    $item = ORM::factory("item", $id);
    access::required("view", $item);
    if (!ecard::can_ecard()) {
      access::forbidden();
    }

    $form = ecard::get_add_form($item);
    try {
      $valid = $form->validate();
      $form->item_id = $id;
      $form->author_id = identity::active_user()->id;
      $form->text = $form->add_ecard->text->value;
      $form->to_name = $form->add_ecard->inputs["to_name"]->value;
      $form->to_email = $form->add_ecard->to_email->value;
      $form->validate();
    } catch (ORM_Validation_Exception $e) {
      // Translate ORM validation errors into form error messages
      foreach ($e->validation->errors() as $key => $error) {
        switch ($key) {
        case "to_name":  $key = "name";  break;
        case "to_email": $key = "email"; break;
        }
        $form->add_ecard->inputs[$key]->add_error($error, 1);
      }
      $valid = false;
    }

    if ($valid) {
      ecard::save();

      print json_encode(
        array("result" => "success",
              "view" => (string) $view,
              "form" => (string) ecard::get_add_form($item)));
    } else {
      $form = ecard::prefill_add_form($form);
      print json_encode(array("result" => "error", "form" => (string) $form));
    }
  }

  /**
   * Present a form for adding a new ecard to this item or editing an existing ecard.
   */
  public function form_add($item_id) {
    $item = ORM::factory("item", $item_id);
    access::required("view", $item);
    if (!ecard::can_ecard()) {
      access::forbidden();
    }

    print ecard::prefill_add_form(ecard::get_add_form($item));
  }
}
