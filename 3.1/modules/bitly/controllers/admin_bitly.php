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
class Admin_Bitly_Controller extends Admin_Controller {

  /**
   * bit.ly module's settings
   * @todo Create/get and display the shortened value for this Gallery's root album (home page)
   */
  public function index() {
    $form = bitly::get_configure_form();
    $valid_config = true;

    if (request::method() == "post") {
      access::verify_csrf();
      if ($form->validate()) {
        $current_login = module::get_var("bitly", "login");
        $current_key = module::get_var("bitly", "api_key");
        $current_domain = module::get_var("bitly", "domain");
        $new_login = $form->configure_bitly->login->value;
        $new_key = $form->configure_bitly->api_key->value;
        $new_domain = $form->configure_bitly->domain->value;
        
        module::set_var("bitly", "login", $new_login);
        module::set_var("bitly", "api_key", $new_key);
        module::set_var("bitly", "domain", $new_domain);

        if (!bitly::check_config()) {
          url::redirect("admin/bitly");
        } else {
          if ($current_login && !$new_login) {
            message::success(t("Your bit.ly login has been cleared."));
          } else if ($current_login && $new_login && $current_login != $new_login) {
            message::success(t("Your bit.ly login has been changed."));
          } else if (!$current_login && $new_login) {
            message::success(t("Your bit.ly login has been saved."));
          }
          if ($current_key && !$new_key) {
            message::success(t("Your bit.ly API key has been cleared."));
          } else if ($current_key && $new_key && $current_key != $new_key) {
            message::success(t("Your bit.ly API key has been changed."));
          } else if (!$current_key && $new_key) {
            message::success(t("Your bit.ly API key has been saved."));
          }
          if ($current_domain && $new_domain && $current_domain != $new_domain) {
            message::success(t("Your preferrend bit.ly domain has been changed."));
          } else if (!$current_domain && $new_domain) {
            message::success(t("Your preferred bit.ly domain has been set."));
          }
          log::success("bitly", t("bit.ly login changed to %new_login",
                                    array("new_login" => $new_login)));
          log::success("bitly", t("bit.ly API key changed to %new_key",
                                    array("new_key" => $new_key)));
          
          (!$new_login || !$new_key) ? $valid_config = false : $valid_config = true;
        }
      }
    }

    $view = new Admin_View("admin.html");
    $view->page_title = t("bit.ly url shortner");
    $view->content = new View("admin_bitly.html");
    $view->content->login = $form->configure_bitly->login->value;
    $view->content->api_key = $form->configure_bitly->api_key->value;
    $view->content->domain = $form->configure_bitly->domain->value;
    $view->content->valid_config = $valid_config;
    $view->content->form = $form;

    if ($valid_config) {
      $link = ORM::factory("bitly_link")->where("item_id", "=", 1)->find();
      if ($link->loaded()) {
        $view->content->g3_url = "http://" . module::get_var("bitly", "domain") . "/$link->hash";
      } else {
        $view->content->g3_url = bitly::shorten_url(1);
      }
    }
    print $view;
  }

}