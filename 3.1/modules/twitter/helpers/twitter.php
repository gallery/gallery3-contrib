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
class twitter_Core {
  
  static $test_mode = TEST_MODE;

  static $character_count = 140;

  //public static $url = "http://twitter.com/home/?status=";

  /**
   *
   * @return Forge
   * @todo Set global Twitter account
   * @todo Default tweet message
   */
  static function get_configure_form() {
    $form = new Forge("admin/twitter", "", "post", array("id" => "g-configure-twitter-form"));

    $group_oauth = $form->group("twitter_oauth")->label(t("OAuth Settings"));
    $group_oauth->input("consumer_key")
          ->label(t("Consumer key"))
          ->value(module::get_var("twitter", "consumer_key"));
    $group_oauth->input("consumer_secret")
          ->label(t("Consumer secret"))
          ->value(module::get_var("twitter", "consumer_secret"));

    $group_tweet = $form->group("twitter_message")->label(t("Default Tweet"));
    $group_tweet->input("default_tweet")
          ->label("Default Tweet")
          ->value(module::get_var("twitter", "default_tweet"));
    // @todo Add reset default tweet button

    if (module::is_active("bitly")) {
      $group_url = $form->group("urls")->label(t("Shorten URLs"));
      $group_url->checkbox("shorten_urls")
            ->label(t("Shorten URLs automatically with bit.ly"))
            ->checked(module::get_var("twitter", "shorten_urls"));
    }

    $form->submit("")->value(t("Save"));
    return $form;
  }

  /**
   *
   * @param <type> $item
   * @return Forge 
   */
  static function get_tweet_form($item) {
    $long_url = url::abs_site($item->relative_url_cache);
    $default_tweet = module::get_var("twitter", "default_tweet");
    $tweet = preg_replace("/%type/", $item->type, $default_tweet);
    $tweet = preg_replace("/%title/", $item->title, $tweet);
    $tweet = preg_replace("/%description/", $item->description, $tweet);
    // If bit.ly module's enabled, get the item's URL and shorten it
    // @todo Refactor bit.ly module so that it doesn't output a status message when called by other modules
    if (module::is_active("bitly") && module::get_var("twitter", "shorten_urls")) {
      $url = bitly::shorten_url($item->id);
    } else {
      $url = url::abs_site($item->relative_url_cache);
    }
    $tweet = preg_replace("/%url/", $url, $tweet);
    $form = new Forge("twitter/tweet", "", "post", array("id" => "g-twitter-form"));
    $group = $form->group("twitter_message")->label(t("Compose Tweet"));
    $group->textarea("tweet")
          ->value($tweet)
          ->rules("required")
          ->error_messages("required", t("Your tweet cannot be empty!"))
          ->id("g-tweet");
    $group->hidden("item_id")->value($item->id);
    $form->submit("")->value(t("Tweet"));
    return $form;
  }

  /**
   * Has this Gallery been registered at dev.twitter.com/app?
   * @return boolean
   */
  static function is_registered() {
    $consumer_key = module::get_var("twitter", "consumer_key");
    $consumer_secret = module::get_var("twitter", "consumer_secret");
    if (empty($consumer_key) || empty($consumer_secret)) {
      site_status::warning(
        t("Twitter module requires attention! Set the <a href=\"%url\">consumer key and secret</a>.",
          array("url" => html::mark_clean(url::site("admin/twitter")))),
        "twitter_config");
      return false;
    } else {
      site_status::clear("twitter_config");
      return true;
    }
  }

  /**
   * Reset the standard Tweet to the module default
   * @return string
   */
  static function reset_default_tweet() {
    $default_tweet = t("Check out this %type, '%title': %description %url");
    module::set_var("twitter", "default_tweet", $default_tweet);
    return $default_tweet;
  }

}
