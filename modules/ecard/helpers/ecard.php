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

/**
 * This is the API for handling ecards.
 *
 * Note: by design, this class does not do any permission checking.
 */
class ecard_Core {
  static function get_add_form($item) {
    $form = new Forge("ecards/create/{$item->id}", "", "post", array("id" => "g-ecard-form"));
    $group = $form->group("add_ecard")->label(t("Send eCard"));
    $group->input("from_name")
      ->label(t("Your Name"))
      ->id("g-author")
      ->error_messages("required", t("You must enter a name for yourself"));
    $group->input("from_email")
      ->label(t("Your e-mail"))
      ->id("g-email")
      ->error_messages("required", t("You must enter a valid email address"))
      ->error_messages("invalid", t("You must enter a valid email address"));
    $group->input("to_name")
      ->label(t("Recipient's Name"))
      ->id("g-recipient")
      ->error_messages("required", t("You must enter a recipient's name"));
    $group->input("to_email")
      ->label(t("Recipient's e-mail"))
      ->id("g-recip-email")
      ->error_messages("required", t("You must enter a valid email address"))
      ->error_messages("invalid", t("You must enter a valid email address"));
    $group->textarea("text")
      ->label(t("Message"))
      ->id("g-text")
      ->error_messages("required", t("You must enter a message"));
    $group->hidden("item_id")->value($item->id);
    module::event("ecard_add_form", $form);
    $group->submit("")->value(t("Send"))->class("ui-state-default ui-corner-all");

    return $form;
  }

  static function prefill_add_form($form) {
    $active = identity::active_user();
    if (!$active->guest) {
      $group = $form->add_ecard;
      $group->inputs["from_name"]->value($active->full_name)->disabled("disabled");
      $group->from_email->value($active->email)->disabled("disabled");
    }
    return $form;
  }

  static function can_ecard() {
    return !identity::active_user()->guest ||
      module::get_var("ecard", "access_permissions") == "everybody";
  }
}

