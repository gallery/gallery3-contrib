<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class bp_email_template_Core {

  static function create($name, $email_text, $email_html) {
    $mail_templ = ORM::factory("bp_email_template")->where("name", "=", $name)->find();
    if ($mail_templ->loaded()) {
      throw new Exception("Email template already EXISTS $name");
    }

    $mail_templ->name = $name;
    $mail_templ->email_text = $email_text;
    $mail_templ->email_html = $email_html;

    $mail_templ->save();
    return $mail_templ;
  }
	
	static function get_add_form_admin() {
    $form = new Forge("admin/email_templates/add_email_template", "", "post", array("id" => "gAddEmailTemplateForm"));
    $group = $form->group("add_email_template")->label(t("Add Email template"));
    $group->input("name")->label(t("Name"))->id("gEmailTemplateName")
      ->error_messages("in_use", t("There is already an email_template with that name"));
    $group->input("name")->label(t("Name"))->id("gName");
/*    $group->input("description")->label(t("Description"))->id("gDescription");
    $group->checkbox("use_header")->label(t("Use header"))->id("gUseHeader");
    $group->checkbox("use_footer")->label(t("Use footer"))->id("gUseFooter");
    $group->input("subject")->label(t("Subject"))->id("gSubject");
    $group->textarea("text")->label(t("Email template"))->id("gText");
*/
    $group->textarea("email_text")->label(t("Email template (Text only)"))->id("gEmailText");
    $group->textarea("email_html")->label(t("Email template (Html)"))->id("gEmailHtml");
    $group->submit("")->value(t("Add Email template"));
    $email_template = ORM::factory("bp_email_template");
    return $form;
  }

  static function get_edit_form_admin($email_template) {

    $form = new Forge("admin/email_templates/edit_email_template/$email_template->id", "", "post",
        array("id" => "gEditEmailTemplateForm"));
    $group = $form->group("edit_email_template")->label(t("Edit Email template"));
    $group->input("name")->label(t("Name"))->id("gEmailTemplateName")->value($email_template->name);
    //$group->inputs["name"]->error_messages(
    //  "in_use", t("There is already an Email template with that name"));
/*    $group->input("description")->label(t("Description"))->id("gDescription")->value($email_template->description);
    $group->checkbox("use_header")->label(t("Use header"))->id("gUseFooter")->checked($email_template->use_header);
    $group->checkbox("use_footer")->label(t("Use footer"))->id("gUseFooter")->checked($email_template->use_footer);
    $group->input("subject")->label(t("Subject"))->id("gSubject")->value($email_template->subject);
    $group->textarea("text")->label(t("Email template"))->id("gText")->value($email_template->text);
*/
    $group->textarea("email_text")->label(t("Email template (Text only)"))->id("gEmailText")->value($email_template->email_text);
    $group->textarea("email_html")->label(t("Email template (Html)"))->id("gEmailHtml")->value($email_template->email_html);
    $group->submit("")->value(t("Modify Email template"));
    return $form;
  }

  static function get_delete_form_admin($email_template) {
    $form = new Forge("admin/email_templates/delete_email_template/$email_template->id", "", "post",
                      array("id" => "gDeleteEmailTemplateForm"));
    $group = $form->group("delete_email_template")->label(
      t("Are you sure you want to delete Email template %name?", array("name" => $email_template->name)));
    $group->submit("")->value(t("Delete Email template %name", array("name" => $email_template->name)));
    return $form;
  }

}
