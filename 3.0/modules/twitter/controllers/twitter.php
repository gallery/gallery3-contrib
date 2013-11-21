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
   * Verify credentials and redirect based on response from Twitter.
   */
  public function callback() {
    require_once(MODPATH . "twitter/vendor/twitteroauth/twitteroauth.php");

    $consumer_key = module::get_var("twitter", "consumer_key");
    $consumer_secret = module::get_var("twitter", "consumer_secret");
    $oauth_token = Session::instance()->get("twitter_oauth_token");
    $oauth_token_secret = Session::instance()->get("twitter_oauth_token_secret");
    $item_url = Session::instance()->get("twitter_item_redirect");

    // If the oauth_token is old redirect to the connect page
    if (isset($_REQUEST['oauth_token']) && $oauth_token !== $_REQUEST['oauth_token']) {
      Session::instance()->set("twitter_oauth_status", "old_token");
      $this->_clear_session();
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
      $this->_save_user($access_token);
      $item = ORM::factory("item", $item_id);
      url::redirect(url::abs_site($item_url));
    } else {
      log::error("content", "Twitter", "Unable to retrieve user access token: " . $connection->http_code);
      $this->_clear_session();
      url::redirect(url::site("twitter/redirect"));
    }
  }

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
   * Redirect user to Twitter authorization page.
   */
  public function redirect() {
    require_once(MODPATH . "twitter/vendor/twitteroauth/twitteroauth.php");

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
      url::redirect($url);
    } else {
      // Show notification if something went wrong
      message::success(t("Could not connect to Twitter. Refresh the page or try again later."));
      url::redirect(urldecode($_GET['item_url']));
    }
  }

  /**
   * Reset a user's Twitter OAuth access token
   * @param int   $user_id 
   */
  public function reset($user_id) {
    if (identity::active_user()->id == $user_id) {
      $u = ORM::factory("twitter_user")->where("user_id", "=", $user_id)->find();
      if ($u->loaded()) {
        $u->oauth_token = "";
        $u->oauth_token_secret = "";
        $u->twitter_user_id = "";
        $u->screen_name = "";
        $u->save();
        message::success(t("Your Twitter access token has been reset."));
        Session::instance()->set("twitter_item_redirect", 
                url::abs_site("user_profile/show/{$user_id}"));
        url::redirect("twitter/redirect");
      }
    }
  }
  
  /**
   * Post a status update to Twitter
   * @param int      $item_id
   */
  public function tweet($item_id) {
    access::verify_csrf();

    $item = ORM::factory("item", $item_id);
    $form = twitter::get_tweet_form($item);

    if ($form->validate()) {
      $item_url = url::abs_site($item->relative_url_cache);
      $user = $this->_get_twitter_user(identity::active_user()->id);
      $consumer_key = module::get_var("twitter", "consumer_key");
      $consumer_secret = module::get_var("twitter", "consumer_secret");

      require_once(MODPATH . "twitter/vendor/twitteroauth/twitteroauth.php");

      $connection = new TwitterOAuth(
              $consumer_key,
              $consumer_secret,
              $user->oauth_token,
              $user->oauth_token_secret);

      $message = $form->twitter_message->tweet->value;
      $attach_image = $form->twitter_message->attach_image->value;
      if ($attach_image == 1) {
	      $filename = APPPATH . "../var/resizes/" . $item->relative_path_cache;
	      $handle = fopen($filename, "rb");
	      $image = fread($handle, filesize($filename));
	      fclose($handle);
	
	      $response = $connection->upload('statuses/update_with_media', array('media[]' => "{$image};type=image/jpeg;filename={$filename}", 'status' => $message));
      }
      else {
      	      $response = $connection->post('statuses/update', array('status' => $message));
      } 

      if (200 == $connection->http_code) {
        message::success(t("Tweet sent!"));
        json::reply(array("result" => "success", "location" => $item->url()));
      } else {
        message::error(t("Unable to send, your Tweet has been saved. Please try again later: %http_code, %response_error", array("http_code" => $connection->http_code, "response_error" => $response->error)));
        log::error("content", "Twitter", t("Unable to send tweet: %http_code",
                array("http_code" => $connection->http_code)));
        json::reply(array("result" => "success", "location" => $item->url()));
      }
      $tweet->item_id = $item_id;
      (!empty($response->id)) ? $tweet->twitter_id = $response->id : $tweet->twitter_id = NULL;
      $tweet->tweet = $message;
      $tweet->id = $form->twitter_message->tweet_id->value;
      $this->_save_tweet($tweet);
      
    } else {
      json::reply(array("result" => "error", "html" => (string)$form));
    }
  }

  /**
   * Clear Twitter module session variables
   */
  private function _clear_session() {
    Session::instance()->delete("twitter_oauth_token");
    Session::instance()->delete("twitter_oauth_token_secret");
    Session::instance()->delete("twitter_access_token");
  }

  /**
   * Get Twitter credentials for the current user.
   * @param  int      $user_id
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
   * Save new tweets
   * @param object   $tweet
   */
  private function _save_tweet($tweet) {
    if (!empty($tweet->item_id) && !empty($tweet->tweet)) {
      if ($tweet->id > 0) {
        $t = ORM::factory("twitter_tweet")->where("id", "=", $tweet->id)->find();
      } else {
        $t = ORM::factory("twitter_tweet");
      }
      $t->item_id = $tweet->item_id;
      $t->twitter_id = $tweet->twitter_id;
      $t->tweet = $tweet->tweet;
      $t->sent = (!empty($tweet->twitter_id)) ? time() : NULL;
      $t->user_id = identity::active_user()->id;
      $t->save();
    }
  }

  /**
   * Save or update the current user's Twitter credentials.
   * @param array     $access_token
   */
  private function _save_user($access_token) {
    $u = ORM::factory("twitter_user")
            ->where("user_id", "=", identity::active_user()->id)
            ->find();
    if (!$u->loaded()) {
      $u = ORM::factory("twitter_user");
    }
    $u->oauth_token = $access_token["oauth_token"];
    $u->oauth_token_secret = $access_token["oauth_token_secret"];
    $u->twitter_user_id = $access_token["user_id"];
    $u->screen_name = $access_token["screen_name"];
    $u->user_id = identity::active_user()->id;
    $u->save();
    message::success(t("Success! You may now share Gallery items on Twitter."));
  }

}
