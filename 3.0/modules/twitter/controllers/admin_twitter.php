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
class Admin_Twitter_Controller extends Admin_Controller {

  /**
   * bit.ly module's settings
   * @todo Show default tweet value after resetting it!
   */
  public function index() {
    $form = twitter::get_configure_form();
    if (request::method() == "post") {
      access::verify_csrf();
      if ($form->validate()) {
        $consumer_key = $form->twitter_oauth->consumer_key->value;
        $consumer_secret = $form->twitter_oauth->consumer_secret->value;
        $reset_tweet = $form->twitter_message->reset_tweet->value;
        if ($reset_tweet) {
          $default_tweet = twitter::reset_default_tweet();
        } else {
          $default_tweet = $form->twitter_message->default_tweet->value;
        }
        $shorten_urls = $form->urls->shorten_urls->value;
        
        module::set_var("twitter", "consumer_key", $consumer_key);
        module::set_var("twitter", "consumer_secret", $consumer_secret);
        module::set_var("twitter", "default_tweet", $default_tweet);
        module::set_var("twitter", "shorten_urls", $shorten_urls);
        
        message::success("Twitter settings saved");
        url::redirect("admin/twitter");
      }
    }
    $is_registered = twitter::is_registered();
    
    $v = new Admin_View("admin.html");
    $v->page_title = t("Twitter");
    $v->content = new View("admin_twitter.html");
    $v->content->form = $form;
    $v->content->is_registered = $is_registered;
    
    print $v;
  }

}