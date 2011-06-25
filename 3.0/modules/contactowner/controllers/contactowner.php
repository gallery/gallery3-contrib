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
class ContactOwner_Controller extends Controller {
  static function get_email_form($user_id, $item_id=null) {
    // Determine name of the person the message is going to.
    $str_to_name = "";
    if ($user_id == -1) {
      $str_to_name = module::get_var("contactowner", "contact_owner_name");
    } else {
      // Locate the record for the user specified by $user_id,
      //   use this to determine the user's name.
      $userDetails = ORM::factory("user")
        ->where("id", "=", $user_id)
        ->find_all();
      $str_to_name = $userDetails[0]->name;
    }

    // If item_id is set, include a link to the item.
    $email_body = "";
    if (!empty($item_id)) {
      $item = ORM::factory("item", $item_id);
      $email_body = "This message refers to <a href=\"" . url::abs_site("{$item->type}s/{$item->id}") . "\">this page</a>.";
    }

    // Make a new form with a couple of text boxes.
    $form = new Forge("contactowner/sendemail/{$user_id}", "", "post",
                      array("id" => "g-contact-owner-send-form"));
    $sendmail_fields = $form->group("contactOwner");
    $sendmail_fields->input("email_to")
                    ->label(t("To:"))->value($str_to_name)
                    ->id("g-contactowner-to-name");
    $sendmail_fields->input("email_from")
                    ->label(t("From:"))->value(identity::active_user()->email)
                    ->id("g-contactowner-from-email")
                    ->rules('required|valid_email')
                    ->error_messages("required", t("You must enter a valid email address"))
                    ->error_messages("valid_email", t("You must enter a valid email address"))
                    ->error_messages("invalid", t("You must enter a valid email address"));
    $sendmail_fields->input("email_subject")
                    ->label(t("Subject:"))->value("")
                    ->id("g-contactowner-subject")
                    ->rules('required')
                    ->error_messages("required", t("You must enter a subject"));
    $sendmail_fields->textarea("email_body")
                    ->label(t("Message:"))
                    ->value($email_body)
                    ->id("g-contactowner-email-body")
                    ->rules('required')
                    ->error_messages("required", t("You must enter a message"));

    // Add a captcha, if there's an active captcha module.
    module::event("captcha_protect_form", $form);

    // Add a save button to the form.
    $sendmail_fields->submit("SendMessage")->value(t("Send"));

    return $form;
  }

  public function emailowner($item_id) {
    // Display a form that a vistor can use to contact the site owner.

    // If this page is disabled, show a 404 error.
    if (module::get_var("contactowner", "contact_owner_link") != true) {
      throw new Kohana_404_Exception();
    }

    // Set up and display the actual page.
    $view = new View("contactowner_emailform.html");
    $view->sendmail_form = $this->get_email_form("-1", $item_id);

    print $view;
  }

  public function emailid($user_id, $item_id) {
    // Display a form that a vistor can use to contact a registered user.

    // If this page is disabled, show a 404 error.
    if (module::get_var("contactowner", "contact_user_link") != true) {
      throw new Kohana_404_Exception();
    }

    // Set up and display the actual page.
    // Set up and display the actual page.
    $view = new View("contactowner_emailform.html");
    $view->sendmail_form = $this->get_email_form($user_id, $item_id);

    print $view;
  }

  public function sendemail($user_id) {
    // Validate the form, then send the actual email.

    // If this page is disabled, show a 404 error.
    if (($user_id == "-1") && (module::get_var("contactowner", "contact_owner_link") != true)) {
      throw new Kohana_404_Exception();
    } elseif (($user_id >= 0) && (module::get_var("contactowner", "contact_user_link") != true)) {
      throw new Kohana_404_Exception();
    }

    // Make sure the form submission was valid.
    $form = $this->get_email_form($user_id);
    $valid = $form->validate();
    if ($valid) {
        // Copy the data from the email form into a couple of variables.
        $str_emailsubject = Input::instance()->post("email_subject");
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
        if ($user_id == -1) {
          // If the email id is "-1" send the message to a pre-determined
          //   owner email address.
          $str_emailto = module::get_var("contactowner", "contact_owner_email");
        } else {
          // or else grab the email from the user table.
        $userDetails = ORM::factory("user")
          ->where("id", "=", $user_id)
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

      message::info(t("Your Message Has Been Sent."));
      json::reply(array("result" => "success"));

    } else {
      // Set up and display the actual page.
      json::reply(array("result" => "error", "html" => (string) $form));
    }
  }
}
