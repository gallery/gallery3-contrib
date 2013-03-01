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
class Admin_ecard_Controller extends Admin_Controller {
  public function index() {
    $view = new Admin_View("admin.html");
    $view->page_title = t("eCard settings");
    $view->content = new View("admin_ecard.html");
    $view->content->form = $this->_get_admin_form();
    print $view;
  }

  public function save() {
    access::verify_csrf();
    $form = $this->_get_admin_form();
    if ($form->validate()) {
	  module::set_var("ecard","send_plain",$form->ecard->send_plain->value);
	  module::set_var("ecard", "sender", $form->ecard->sender->value);
	  module::set_var("ecard", "bcc", $form->ecard->bcc->value);
      module::set_var("ecard", "subject", $form->ecard->subject->value);
      module::set_var("ecard", "message", $form->ecard->message->value);
	  module::set_var("ecard", "max_length", $form->ecard->max_length->value);
      module::set_var("ecard", "access_permissions", $form->ecard->access_permissions->value);
	  module::set_var("ecard", "location", $form->ecard->location->value);
      message::success(t("eCard settings updated"));
      url::redirect("admin/ecard");
    } else {
      print $form;
    }
  }

  private function _get_admin_form() {
    $form = new Forge("admin/ecard/save", "", "post", array("id" => "g-ecard-admin-form"));
    $ecard_settings = $form->group("ecard")->label(t("eCard settings"));
    $ecard_settings->input("sender")
      ->label(t("E-mail sender (leave blank for a user-defined address)"))
      ->value(module::get_var("ecard", "sender", ""));
    $ecard_settings->input("bcc")
      ->label(t("BCC (optional)"))
      ->value(module::get_var("ecard", "bcc", ""));
	$ecard_settings->input("subject")->label(t("E-mail subject"))
      ->value(module::get_var("ecard", "subject"));
    $ecard_settings->textarea("message")->label(t("E-mail message. Valid keywords are \"%fromname\" (sender's name))"))
      ->value(module::get_var("ecard", "message"));
	$ecard_settings->input("max_length")
	  ->label(t("Maximum message length"))
	  ->value(module::get_var("ecard","max_length"));
	if(module::is_active("watermark")) {
		$ecard_settings->checkbox("send_plain")
		  ->label(t("Allow users to send non-watermarked versions"))
		  ->value(true)
		  ->checked(module::get_var("ecard","send_plain"));
	}
	$ecard_settings->dropdown("access_permissions")
      ->label(t("Who can send eCards?"))
      ->options(array("everybody" => t("Everybody"),
                      "registered_users" => t("Only registered users")))
      ->selected(module::get_var("ecard", "access_permissions"));
    $ecard_settings->dropdown("location")
      ->label(t("Where should the eCard link be displayed?"))
      ->options(array("top" => t("At the top of the sidebar as an icon"),
                      "sidebar" => t("In the sidebar as a button")))
      ->selected(module::get_var("ecard", "location"));
    $ecard_settings->submit("save")->value(t("Save"));
    return $form;
  }
}

