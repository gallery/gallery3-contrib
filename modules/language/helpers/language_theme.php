<?php defined("SYSPATH") or die("No direct script access.");/**
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
class language_theme {
  static function head($theme) {
    $session = Session::instance();
   if (count(locales::installed())) {
      // Needed by the languages block
      $theme->script("jquery.cookie.js");
    }

    if ($session->get("l10n_mode", false)) {
      $theme->css("l10n_client.css");
      $theme->script("jquery.cookie.js");
      $theme->script("l10n_client.js");
    }

    return "";
  }

  static function admin_head($theme) {
    $session = Session::instance();
    if ($session->get("l10n_mode", false)) {
      $theme->css("l10n_client.css");
      $theme->script("jquery.cookie.js");
      $theme->script("l10n_client.js");
    }
  }

  static function page_bottom($theme) {
    $session = Session::instance();
    if ($session->get("l10n_mode", false)) {
      return L10n_Client_Controller::l10n_form();
    }
  }

  static function admin_page_bottom($theme) {
    $session = Session::instance();
    if ($session->get("l10n_mode", false)) {
      return L10n_Client_Controller::l10n_form();
    }
  }

  static function body_attributes() {
    if (locales::is_rtl()) {
      return 'class="rtl"';
    }
  }
}
