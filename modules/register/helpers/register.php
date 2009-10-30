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
class register_Core {
  static function send_user_created_confirmation($user, $password) {
    $message = new View("register_welcome.html");
    $message->user = $user;
    $message->password = $password;
    self::_sendemail($user->email, t("Your userid has been created"), $message);
  }

  static function send_confirmation($user) {
    $message = new View("confirm_registration.html");
    $message->confirm_url = url::abs_site("register/confirm/{$user->hash}");
    $message->user = $user;
    self::_sendemail($user->email, t("User registration confirmation"), $message);
  }

  static function create_pending_request($form) {
    $email_verification = module::get_var("registration", "email_verification");

    $user = ORM::factory("pending_user");
    $user->name = $form->register_user->inputs["name"]->value;
    $user->full_name = $form->register_user->inputs["full_name"]->value;
    $user->email = $form->register_user->inputs["email"]->value;
    $user->url = $form->register_user->inputs["url"]->value;
    if (!$email_verification) {
      $user->confirmed = true;
    }
    $user->hash = md5(rand());
    $user->save();
    return $user;
  }

  private static function _sendemail($email, $subject, $message) {
    Sendmail::factory()
      ->to($email)
      ->subject($subject)
      ->header("Mime-Version", "1.0")
      ->header("Content-type", "text/html; charset=iso-8859-1")
      ->message($message->render())
      ->send();
  }
}