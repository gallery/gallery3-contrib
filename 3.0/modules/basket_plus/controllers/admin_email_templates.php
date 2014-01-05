<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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

class Admin_Email_Templates_Controller extends Controller
{
  /**
   * the index page of the Email templates admin
   */
	 
  public function index() {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_email_templates.html");
    $view->content->email_templates = ORM::factory("bp_email_template")->order_by("name")->find_all();

    print $view;
  }

  public function add_email_template_form() {
    print bp_email_template::get_add_form_admin();
  }

  public function add_email_template() {
    access::verify_csrf();

    $form = bp_email_template::get_add_form_admin();
    $valid = $form->validate();
    $name = $form->add_email_template->inputs["name"]->value;
    $email_template = ORM::factory("bp_email_template")->where("name", "=", $name)->find();
    if ($email_template->loaded()) {
      $form->add_email_template->inputs["name"]->add_error("in_use", 1);
      $valid = false;
    }

    if ($valid) {
      $email_template = bp_email_template::create(
        $name,
/*				$form->add_email_template->use_header->checked,
				$form->add_email_template->use_footer->checked,
				$form->add_email_template->description->value,
				$form->add_email_template->subject->value,
				$form->add_email_template->text->value
*/
				$form->add_email_template->email_text->value,
				$form->add_email_template->email_html->value
        );

      $email_template->save();
      message::success(t("Created Email_template %email_template_name", array(
        "email_template_name" => html::clean($email_template->name))));
      print json::reply(array("result" => "success"));
    } 
		else {
      print $form;
    }
  }

  public function delete_email_template_form($id) {
    $email_template = ORM::factory("bp_email_template", $id);
    if (!$email_template->loaded()) {
      kohana::show_404();
    }
    print bp_email_template::get_delete_form_admin($email_template);
  }

  public function delete_email_template($id) {
    access::verify_csrf();

    $email_template = ORM::factory("bp_email_template", $id);
    if (!$email_template->loaded()) {
      kohana::show_404();
    }

    $form = bp_email_template::get_delete_form_admin($email_template);
    if($form->validate()) {
      $name = $email_template->name;
      $email_template->delete();
    } 
		else {
      print $form;
    }

    $message = t("Deleted Email template %email_template_name", array("email_template_name" => html::clean($name)));
    log::success("email_template", $message);
    message::success($message);
    print json::reply(array("result" => "success"));
  }

  public function edit_email_template($id) {
    access::verify_csrf();

    $email_template = ORM::factory("bp_email_template", $id);
    if (!$email_template->loaded()) {
      kohana::show_404();
    }

    $form = bp_email_template::get_edit_form_admin($email_template);
    $valid = $form->validate();
    if ($valid) {
      $new_name = $form->edit_email_template->inputs["name"]->value;
      if ($new_name != $email_template->name &&
          ORM::factory("bp_email_template")
          ->where("name", "=", $new_name)
          ->where("id","!=", $email_template->id)
          ->find()
          ->loaded()) {
        $form->edit_email_template->inputs["name"]->add_error("in_use", 1);
        $valid = false;
      } 
			else {
        $email_template->name = $new_name;
      }
    }

    if ($valid) {
      //$email_template->name = $form->edit_email_template->name->value;
			$email_template->name = $form->edit_email_template->inputs["name"]->value;
/*      $email_template->description = $form->edit_email_template->description->value;
      $email_template->use_header = $form->edit_email_template->use_header->checked;
      $email_template->use_footer = $form->edit_email_template->use_footer->checked;
      $email_template->subject = $form->edit_email_template->subject->value;
      $email_template->text = $form->edit_email_template->text->value;
*/
			$email_template->email_text = $form->edit_email_template->email_text->value;
			$email_template->email_html = $form->edit_email_template->email_html->value;
			$email_template->save();

      message::success(t("Changed Email template %email_template_name",
          array("email_template_name" => html::clean($email_template->name))));
      print json::reply(array("result" => "success"));
    } 
		else {
      print $form;
    }
  }

  public function edit_email_template_form($id) {
    $email_template = ORM::factory("bp_email_template", $id);
    if (!$email_template->loaded()) {
      kohana::show_404();
    }

    $form = bp_email_template::get_edit_form_admin($email_template);

    print $form;
  }

}