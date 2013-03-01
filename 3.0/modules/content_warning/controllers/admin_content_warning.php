<?php defined("SYSPATH") or die("No direct script access.");/**
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
class Admin_Content_Warning_Controller extends Admin_Controller {
 	public function index() {
    	print $this->_get_view();
	}

	public function handler() {
    	access::verify_csrf();

    	$form = $this->_get_form();
    
    	if ($form->validate())  {
      		module::set_var("content_warning", "title", $form->content_warning->inputs["title"]->value);	
      		module::set_var("content_warning", "message", $form->content_warning->inputs["message"]->value);
      		module::set_var("content_warning", "enter_link_text", $form->content_warning->inputs["enter_link_text"]->value);
      		//module::set_var("content_warning", "enter_link_url", $form->content_warning->inputs["enter_link_url"]->value);
      		module::set_var("content_warning", "exit_link_text", $form->content_warning->inputs["exit_link_text"]->value);
      		module::set_var("content_warning", "exit_link_url", $form->content_warning->inputs["exit_link_url"]->value);  
      		url::redirect("admin/content_warning");
    	}
    	print $this->_get_view($form);
	}

	private function _get_view($form=null) {
    	$v = new Admin_View("admin.html");
    	$v->content = new View("admin_content_warning.html");
    	$v->content->form = empty($form) ? $this->_get_form() : $form;
    	return $v;
	}
  
	private function _get_form() {
    	$form = new Forge("admin/content_warning/handler", "", "post",
                      array("id" => "gAdminContentWerning"));
    	$group = $form->group("content_warning");
    	$group->input("title")->label(t('Title (Will be displayed within H3)'))->rules("required")->value(module::get_var("content_warning", "title"));
    	$group->textarea("message")->label(t('Message (you can use HTML tags)'))->rules("required")->value(module::get_var("content_warning", "message"));
    	$group->input("enter_link_text")->label(t('Enter Label'))->rules("required")->value(module::get_var("content_warning", "enter_link_text"));
    	//$group->input("enter_link_url")->label(t('Enter Url (Leave empty to redirect to the previous page)'))->value(module::get_var("content_warning", "enter_link_url"));
    	$group->input("exit_link_text")->label(t('Exit Label'))->rules("required")->value(module::get_var("content_warning", "exit_link_text"));
    	$group->input("exit_link_url")->label(t('Exit Url'))->rules("required")->value(module::get_var("content_warning", "exit_link_url"));
    	
    	$group->submit("submit")->value(t("Save"));
    	return $form;
  	}
}