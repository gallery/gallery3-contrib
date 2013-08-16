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
class Admin_Bitly_Controller extends Admin_Controller {

  /**
   * bit.ly module's settings
   * @todo Create/get and display the shortened value for this Gallery's root album (home page)
   */
  public function index() {
    $form = bitly::get_configure_form();
    $login = module::get_var("bitly", "login");
    $api_key = module::get_var("bitly", "api_key");
    $domain = module::get_var("bitly", "domain");
    $valid_config = false;
    
    if (request::method() == "post") {
      access::verify_csrf();
      if ($form->validate()) {
        $new_login = $form->configure_bitly->login->value;
        $new_key = $form->configure_bitly->api_key->value;
        $new_domain = $form->configure_bitly->domain->value;
        
        module::set_var("bitly", "login", $new_login);
        module::set_var("bitly", "api_key", $new_key);
        module::set_var("bitly", "domain", $new_domain);

        if (!bitly::check_config()) {
          url::redirect("admin/bitly");
        } else {
          if ($login && !$new_login) {
            message::success(t("Your bit.ly login has been cleared."));
          } else if ($login && $new_login && $login != $new_login) {
            message::success(t("Your bit.ly login has been changed."));
          } else if (!$login && $new_login) {
            message::success(t("Your bit.ly login has been saved."));
          }
          if ($api_key && !$new_key) {
            message::success(t("Your bit.ly API key has been cleared."));
          } else if ($api_key && $new_key && $api_key != $new_key) {
            message::success(t("Your bit.ly API key has been changed."));
          } else if (!$api_key && $new_key) {
            message::success(t("Your bit.ly API key has been saved."));
          }
          if ($domain && $new_domain && $domain != $new_domain) {
            message::success(t("Your preferrend bit.ly domain has been changed."));
          } else if (!$domain && $new_domain) {
            message::success(t("Your preferred bit.ly domain has been saved."));
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
    $view->content->form = $form;

    $link = ORM::factory("bitly_link")->where("item_id", "=", 1)->find();

    if ($link->loaded()) {
      $view->content->g3_url = bitly::url($link->hash);
    } else if ($valid_config && !empty($login) && !empty($api_key) && !empty($domain)) {
      $view->content->g3_url = bitly::shorten_url(1);
    }

    print $view;
  }

}