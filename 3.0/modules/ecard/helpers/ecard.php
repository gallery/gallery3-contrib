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

/**
 * This is the API for handling ecards.
 *
 * Note: by design, this class does not do any permission checking.
 */
class ecard_Core {
  static function get_send_form($item_id) {
    $form = new Forge("ecard/send/{$item_id}", "", "post", array("id" => "g-ecard-form"));
    $group = $form->group("send_ecard")->label(t("Send eCard"));
    $group->input("from_name")
      ->label(t("Your name"))
      ->id("g-author")
      ->rules("required")
      ->error_messages("required", t("You must enter a name for yourself"));
    $group->input("from_email")
      ->label(t("Your e-mail"))
      ->id("g-email")
      ->rules("required|valid_email")
      ->error_messages("required", t("You must enter a valid email address"))
      ->error_messages("invalid", t("You must enter a valid email address"));
    $group->input("to_email")
      ->label(t("Recipient's e-mail. Separate multiple recipients with a comma."))
      ->id("g-recip-email")
      ->rules("required")
      ->error_messages("required", t("You must enter a valid email address"));
    $group->textarea("text")
      ->label(t("Message (".module::get_var("ecard","max_length")." chars max)"))
      ->id("g-text")
	  ->maxlength(module::get_var("ecard","max_length"))
      ->rules("required")
      ->error_messages("required", t("You must enter a message"));
	$group->checkbox("send_to_self")
      ->label(t("Send yourself a copy"))
	  ->value(true)
	  ->checked(false);	  
	$group->checkbox("send_thumbnail")
      ->label(t("Send thumbnail image, instead of resized image."))
	  ->value(true)
	  ->checked(false);	  
	if(module::get_var("ecard","send_plain") == true && module::is_active("watermark")) {
		$group->checkbox("send_fresh")
		  ->label(t("Send non-watermarked image."))
		  ->value(true)
		  ->checked(false);		  
	}
	$group->hidden("item_id")->value($item_id);
    module::event("ecard_send_form", $form);
    module::event("captcha_protect_form", $form);
    $group->submit("")->value(t("Send"))->class("ui-state-default ui-corner-all");

    return $form;
  }

  static function prefill_send_form($form) {
    $active = identity::active_user();
    if (!$active->guest) {
      $group = $form->send_ecard;
      $group->inputs["from_name"]->value($active->full_name);
      $group->from_email->value($active->email);
    }
    return $form;
  }

  static function can_send_ecard() {
    return !identity::active_user()->guest ||
      module::get_var("ecard", "access_permissions") == "everybody";
  }
}

