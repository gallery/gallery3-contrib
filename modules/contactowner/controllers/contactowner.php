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
class ContactOwner_Controller extends Controller {
  public function emailowner() {
    // Display a form that a vistor can use to contact the site owner.

    // If this page is disabled, show a 404 error.
    if (module::get_var("contactowner", "contact_owner_link") != true) {
      throw new Kohana_404_Exception();
    }

    // Make a new form with a couple of text boxes.
    $form = new Forge("contactowner/sendemail", "", "post",
                      array("id" => "g-contact-owner-send-form"));
    $sendmail_fields = $form->group("contactOwner");
    $sendmail_fields->input("email_to")->label(t("To:"))->value(module::get_var("contactowner", "contact_owner_name"));
    $sendmail_fields->input("email_from")->label(t("From:"))->value(identity::active_user()->email);

    $sendmail_fields->input("email_subject")->label(t("Subject:"))->value("");
    $sendmail_fields->textarea("email_body")->label(t("Message:"))->value("");
    $sendmail_fields->hidden("email_to_id")->value("-1");

    // Add a save button to the form.
    $sendmail_fields->submit("SendMessage")->value(t("Send"));

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "other", "Contact");
    $template->content = new View("contactowner_emailform.html");
    $template->content->sendmail_form = $form;
    print $template;
  }

  public function emailid($user_id) {
    // Display a form that a vistor can use to contact a registered user.

    // If this page is disabled, show a 404 error.
    if (module::get_var("contactowner", "contact_user_link") != true) {
      throw new Kohana_404_Exception();
    }

    // Locate the record for the user specified by $user_id,
    //   use this to determine the user's name.
    $userDetails = ORM::factory("user")
      ->where("id", "=", $user_id)
      ->find_all();

    // Make a new form with a couple of text boxes.
    $form = new Forge("contactowner/sendemail", "", "post",
                      array("id" => "g-contact-owner-send-form"));
    $sendmail_fields = $form->group("contactOwner");
    $sendmail_fields->input("email_to")->label(t("To:"))->value($userDetails[0]->name);
    $sendmail_fields->input("email_from")->label(t("From:"))->value(identity::active_user()->email);
    $sendmail_fields->input("email_subject")->label(t("Subject:"))->value("");
    $sendmail_fields->textarea("email_body")->label(t("Message:"))->value("");
    $sendmail_fields->hidden("email_to_id")->value($user_id);

    // Add a save button to the form.
    $sendmail_fields->submit("SendMessage")->value(t("Send"));

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "other", "Contact");
    $template->content = new View("contactowner_emailform.html");
    $template->content->sendmail_form = $form;
    print $template;
  }

  public function sendemail() {
    // Process the data from the form into an email,
    //   then send the email.

    // Make sure the form was submitted.
    if ($_POST) {
      // Set up some rules to validate the form against.
      $post = new Validation($_POST);
      $post->add_rules('email_from', 'required', 'valid::email');
      $post->add_rules('email_subject', 'required');
      $post->add_rules('email_body', 'required');
  
      // If the form was filled out properly then...
      if ($post->validate()) {
        // Copy the data from the email form into a couple of variables.
        $str_emailsubject = Input::instance()->post("email_subject");
        $str_emailtoid = Input::instance()->post("email_to_id");
        $str_emailfrom = Input::instance()->post("email_from");
        $str_emailbody = Input::instance()->post("email_body");

        // Add in some <br> tags to the message body where ever there are line breaks.
        $str_emailbody = str_replace("\n", "\n<br/>", $str_emailbody);

        // Gallery's Sendmail library doesn't allow for custom from addresses,
        //   so add the from email to the beginning of the message body instead.
        //   Also add in the admin-defined message header.
        $str_emailbody = module::get_var("contactowner", "contact_owner_header") . "<br/>\r\n" . "Message Sent From " . $str_emailfrom . "<br/>\r\n<br/>\r\n" . $str_emailbody;

        // Figure out where the email is going to.
        $str_emailto = "";
        if ($str_emailtoid == -1) {
          // If the email id is "-1" send the message to a pre-determined
          //   owner email address.
          $str_emailto = module::get_var("contactowner", "contact_owner_email");
        } else {
          // or else grab the email from the user table.
        $userDetails = ORM::factory("user")
          ->where("id", "=", $str_emailtoid)
          ->find_all();
          $str_emailto = $userDetails[0]->email;
        }

        // Send the email message.
        Sendmail::factory()
          ->to($str_emailto)
          ->subject($str_emailsubject)
          ->header("Mime-Version", "1.0")
          ->header("Content-type", "text/html; charset=utf-8")
          ->message($str_emailbody)
          ->send();

        // Display a message telling the visitor that their email has been sent.
        $template = new Theme_View("page.html", "other", "Contact");
        $template->content = new View("contactowner_emailform.html");
        $template->content->sendmail_form = t("Your Message Has Been Sent.");
        print $template;
	  } else {
        // Display a message telling the visitor that their email has been not been sent,
        //   along with the reason(s) why.
        $template = new Theme_View("page.html", "other", "Contact");
        $template->content = new View("contactowner_emailform.html");
        $template->content->sendmail_form = t("Your Message Has Not Been Sent.");
        $template->content->sendmail_form = $template->content->sendmail_form . "<br/><br/>" . t("Reason(s):") . "<br/>";
        foreach($post->errors('form_error_messages') as $error) {
          $template->content->sendmail_form = $template->content->sendmail_form .  " - " . t($error) . "<br/>";
        }
        print $template;
      }
    }
  }
}