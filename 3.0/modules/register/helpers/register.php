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
class register_Core {
  private static $_states;

  static function format_registration_state($state) {
    if (empty(self::$_states)) {
      $policy = module::get_var("registration", "policy");
      $email_verification = module::get_var("registration", "email_verification");
      $pending = $policy == "admin_only" || ($policy == "admin_approval" && !$email_verification);
      self::$_states = array(t("Unconfirmed"),
                             $pending ? t("Pending")  : t("Confirmed"),
                             t("Activated"));
    }
    return self::$_states[$state];
  }

  static function check_user_name($user_name) {
    if (identity::lookup_user_by_name($user_name)) {
      return true;
    }
    $user = ORM::factory("pending_user")
      ->where("name", "=", $user_name)
      ->find();
    return $user->loaded();
  }

  static function send_user_created_confirmation($user, $requires_first=false) {
    $message = new View("register_welcome.html");
    $message->user = $user;
    $message->site_url = $requires_first ? url::abs_site("register/first/{$user->hash}") :
                                           url::abs_site("");
    // added Shad Laws, v2
    $message->subject_prefix = module::get_var("registration", "subject_prefix");
    $message->locale = $user->locale; // as stored in pending_users table
    $message->subject = t("Welcome", array("locale" => $message->locale));
    // modified Shad Laws, v2
    self::_sendemail($user->email, $message->subject_prefix.$message->subject, $message);
  }

  static function send_confirmation($user) {
    $message = new View("confirm_registration.html");
    $message->confirm_url = url::abs_site("register/confirm/{$user->hash}");
    $message->user = $user;
    // added Shad Laws, v2
    $message->subject_prefix = module::get_var("registration", "subject_prefix");
    $message->locale = $user->locale; // as stored in pending_users table
    $message->subject = t("User registration confirmation", array("locale" => $message->locale));
    // modified Shad Laws, v2
    self::_sendemail($user->email, $message->subject_prefix.$message->subject, $message);
  }

  // function added Shad Laws, v2
  static function send_admin_notify($user) {
    $message = new View("register_admin_notify.html");
    $message->admin_register_url = url::abs_site("admin/register");
    $message->user = $user;
    $message->subject_prefix = module::get_var("registration", "subject_prefix");
    $message->locale = module::get_var("gallery", "default_locale"); // as Gallery default
    $message->subject = t("New pending user registration", array("locale" => $message->locale));
    self::_sendemail(module::get_var("gallery", "email_reply_to"), $message->subject_prefix.$message->subject, $message);
  }

  static function create_pending_request($form) {
    $email_verification = module::get_var("registration", "email_verification");

    $user = ORM::factory("pending_user");
    $user->name = $form->register_user->inputs["name"]->value;
    $user->full_name = $form->register_user->inputs["full_name"]->value;
    $user->email = $form->register_user->inputs["email"]->value;
    $user->url = $form->register_user->inputs["url"]->value;
    $user->request_date = time();
    // added by Shad Laws, v2
    $user->locale = locales::locale_from_http_request() ? locales::locale_from_http_request() : module::get_var("gallery", "default_locale"); // sets default locale based on browser

    if (!$email_verification) {
      $user->state = 1;
    }
    $user->hash = md5(rand());
    $user->save();
    return $user;
  }

  static function create_new_user($id) {
    $user = ORM::factory("pending_user", $id);

    $password = md5(uniqid(mt_rand(), true));
    $new_user = identity::create_user($user->name, $user->full_name, $password, $user->email);
    $new_user->url = $user->url;
    $new_user->admin = false;
    $new_user->guest = false;
    $new_user->save();

    $group_id = module::get_var("registration", "default_group");
    if ($group_id != null) {
        $default_group = group::lookup($group_id);
        if ($default_group != null) {
            $default_group->add($new_user);
            $default_group->save();
        }
    }

    $user->hash =  md5(uniqid(mt_rand(), true));
    $user->state = 2;
    $user->save();
    self::send_user_created_confirmation($user, $password);

    return $new_user;
  }

  private static function _sendemail($email, $subject, $message) {
    Sendmail::factory()
      ->to($email)
      ->subject($subject)
      ->header("Mime-Version", "1.0")
      // modified by Shad Laws, v2
      ->header("Content-type", "text/html; charset=utf-8")
      ->message($message->render())
      ->send();
  }
}
