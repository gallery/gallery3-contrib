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

class Twitter_Controller extends Controller {

  /**
   * Display Twitter status dialog.
   * @param int       $item_id
   */
  public function dialog($item_id) {
    $item = ORM::factory("item", $item_id);
    $form = twitter::get_tweet_form($item);

    // Ensure user has permission
    access::required("view", $item);

    $user_id = identity::active_user()->id;
    $token_is_set = $this->_is_token_set($user_id);

    $v = new View("twitter_dialog.html");
    $v->is_registered = twitter::is_registered();
    $v->user_token_set = $token_is_set;

    if ($token_is_set) {
      $v->type = $item->type;
      $v->title = $item->title;
      $v->description = $item->description;
      $v->form = $form;
      $v->character_count = twitter::$character_count;
    } else {
      $item_url = urlencode(url::abs_site($item->relative_url_cache));
      $v->user_id = $user_id;
      $v->twitter_auth_url = url::site("twitter/redirect?item_url=$item_url");
    }
    
    print $v;
  }

  /**
   * Check if current user's Twitter credentials have been stored locally.
   * @param  int      $user_id
   * @return boolean
   */
  private function _is_token_set($user_id) {
    $twitter_user = $this->_get_twitter_user($user_id);
    if (!empty($twitter_user->oauth_token) && !empty($twitter_user->oauth_token_secret)) {
      return true;
    }
    return false;
  }

  /**
   * Get Twitter credentials for the current user.
   * @param int       $user_id
   * @return mixed    object|false
   */
  private function _get_twitter_user($user_id) {
    $twitter_user = ORM::factory("twitter_user")->where("user_id", "=", $user_id)->find();
    if ($twitter_user->loaded()) {
      return $twitter_user;
    }
    return false;
  }

  /**
   * Verify credentials and redirect based on response from Twitter.
   */
  public function callback() {
    require_once(MODPATH . "twitter/lib/twitteroauth.php");

    $consumer_key = module::get_var("twitter", "consumer_key");
    $consumer_secret = module::get_var("twitter", "consumer_secret");
    $oauth_token = Session::instance()->get("twitter_oauth_token");
    $oauth_token_secret = Session::instance()->get("twitter_oauth_token_secret");
    $item_url = Session::instance()->get("twitter_item_redirect");

    // If the oauth_token is old redirect to the connect page
    if (isset($_REQUEST['oauth_token']) && $oauth_token !== $_REQUEST['oauth_token']) {
      Session::instance()->set("twitter_oauth_status", "old_token");
      $this->clear_twitter_session();
      url::redirect(url::site("twitter/redirect"));
    }

    // Create TwitteroAuth object with app key/secret and token key/secret from default phase
    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

    // Request access tokens from twitter
    $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

    // Save the access tokens
    Session::instance()->set("twitter_access_token", $access_token);

    // Remove no longer needed request tokens
    Session::instance()->delete("twitter_oauth_token");
    Session::instance()->delete("twitter_oauth_token_secret");

    // If HTTP response is 200 continue otherwise send to connect page to retry
    if (200 == $connection->http_code) {
      // The user has been verified and the access tokens can be saved for future use
      $this->save_twitter_user($access_token);
      // Redirect to the tweet form
      $item = ORM::factory("item", $item_id);
      url::redirect(url::abs_site($item_url));
    } else {
      // @todo Log HTTP status for application log and/or error message
      $this->clear_twitter_session();
      url::redirect(url::site("twitter/redirect"));
    }
  }

  /**
   * Save or update the current user's Twitter credentials.
   * @param array     $access_token
   * @todo Ensure only one record per twitter_screen_name
   */
  function save_twitter_user($access_token) {
    $twitter_user = ORM::factory("twitter_user");
    $twitter_user->oauth_token = $access_token["oauth_token"];
    $twitter_user->oauth_token_secret = $access_token["oauth_token_secret"];
    $twitter_user->twitter_user_id = $access_token["user_id"];
    $twitter_user->screen_name = $access_token["screen_name"];
    $twitter_user->user_id = identity::active_user()->id;
    $twitter_user->save();

    message::success(t("Twitter access tokens saved!"));
  }

  /**
   * Redirect user to Twitter authorization page.
   */
  function redirect() {
    require_once(MODPATH . "twitter/lib/twitteroauth.php");
    
    $consumer_key = module::get_var("twitter", "consumer_key");
    $consumer_secret = module::get_var("twitter", "consumer_secret");
    $oauth_callback = url::abs_site("twitter/callback");

    // We'll want this after Twitter kicks back to our callback
    if (!empty($_GET['item_url'])) {
      Session::instance()->set("twitter_item_redirect", $_GET['item_url']);
    }
    
    // Build TwitterOAuth object with client credentials
    $connection = new TwitterOAuth($consumer_key, $consumer_secret);

    // Get temporary credentials.
    $request_token = $connection->getRequestToken($oauth_callback);
    
    // Save temporary credentials to session.
    Session::instance()->set("twitter_oauth_token", $request_token['oauth_token']);
    Session::instance()->set("twitter_oauth_token_secret", $request_token['oauth_token_secret']);

    // If last connection failed don't display authorization link
    if (200 == $connection->http_code) {
      // Build authorize URL and redirect user to Twitter
      $url = $connection->getAuthorizeURL($request_token["oauth_token"]);
      url::redirect(url::site($url));
    } else {
      // Show notification if something went wrong
      message::success(t("Could not connect to Twitter. Refresh the page or try again later."));
      url::redirect(url::site($url));
    }
  }

  /**
   * Save tweets sent and those not sent because of Twitter API issues
   * @param integer  $item_id
   * @param string   $tweet  The tweet sent, or the tweet that couldn't be sent
   * @param boolean  $status   1 for success and 0 for not sent
   */
  public function save($item_id, $tweet, $status) {
    if (!empty($item_id) && !empty($tweet) && !empty($status)) {
      $t = ORM::factory("twitter_tweet");
      $t->created = time();
      $t->item_id = $item_id;
      $t->tweet = $tweet;
      $t->status = $status;
      $t->user_id = identity::active_user()->id;
      $t->save();
    }
  }

  /**
   * Post a status update to Twitter
   * @param string    $message
   */
  public function tweet($item_id) {
    access::verify_csrf();
    
    $item = ORM::factory("item", $item_id);
    $form = twitter::get_tweet_form($item);
    
    if ($form->validate()) {
      $item_url = url::abs_site($item->relative_url_cache);
      $twitter_user = $this->_get_twitter_user(identity::active_user()->id);
      $consumer_key = module::get_var("twitter", "consumer_key");
      $consumer_secret = module::get_var("twitter", "consumer_secret");

      require_once(MODPATH . "twitter/lib/twitteroauth.php");
      
      $connection = new TwitterOAuth(
              $consumer_key,
              $consumer_secret,
              $twitter_user->oauth_token,
              $twitter_user->oauth_token_secret);
      
      $message = $form->twitter_message->tweet->value;
      $connection->post('statuses/update', array('status' => $message));

      if (200 == $connection->http_code) {
        message::success(t("Tweet sent!"));
        //url::redirect(url::abs_site($item->relative_url_cache));
        //json::reply(array("result" => "success"));
        json::reply(array("result" => "success", "location" => $item->url()));
        $status = 1;
      } else {
        message::error(t("Unable to send Tweet. Your message has been saved. Please try again later."));
        json::reply(array("result" => "error", "location" => $item->url()));
        $status = 0;
        // @todo Log Twitter error response
      }
      $this->save($item_id, $message, $status);
      
    } else {
      echo "validation failed";
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }

  /**
   * Clear Twitter module session variables
   */
  function clear_twitter_session() {
    Session::instance()->delete("twitter_oauth_token");
    Session::instance()->delete("twitter_oauth_token_secret");
    Session::instance()->delete("twitter_access_token");
  }
}