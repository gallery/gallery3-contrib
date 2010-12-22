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
class bitly_Core {
  public static $test_mode = TEST_MODE;

  public static $api_host = "api.bit.ly";

  public static $api_version = "v3";

  public static $api_methods = array(
      'expand'    => 'expand',
      'shorten'   => 'shorten',
      'validate'  => 'validate',
      'clicks'    => 'clicks',
      'referrers' => 'referrers',
      'countries' => 'countries',
      'clicks_by_minute' => 'clicks_by_minute',
      'clicks_by_day' => 'clicks_by_day',
      'lookup' => 'lookup',
      'info' => 'info',
  );

  static function get_configure_form() {
    $form = new Forge("admin/bitly", "", "post", array("id" => "g-configure-bitly-form"));
    $group = $form->group("configure_bitly")->label(t("Configure bit.ly"));
    $group->input("login")
          ->label(t("Login"))
          ->value(module::get_var("bitly", "login"))
          ->rules("required")
          ->error_messages("required", t("You must enter a login"));
    $group->input("api_key")
          ->label(t("API Key"))
          ->value(module::get_var("bitly", "api_key"))
          ->rules("required")
          ->error_messages("required", t("You must enter an API key"));
    $group->dropdown("domain")
          ->label(t("Preferred Domain"))
          ->options(array("bit.ly" => "bit.ly", "j.mp" => "j.mp"))
          ->selected(module::get_var("bitly", "domain"));
    $group->submit("")->value(t("Save"));
    return $form;
  }

  /**
   * Check a login and an API Key against bit.ly to make sure they're valid
   * @param  string   $login   the login
   * @param  string   $api_key the API key
   * @return boolean
   */
  static function validate_config($login, $api_key) {
    if (!empty($login) && !empty($api_key)) {
      $parameters = array(
        'login' => $login,
        'apiKey' => $api_key,
        'x_login' => $login,
        'x_apiKey' => $api_key
        );
      $request = self::_build_http_request('validate', $parameters);
      $response = self::_http_post($request, "api.bit.ly");
      $json_decoded = json_decode($response->body[0]);
      if (!$json_decoded->data->valid) {
        if ("INVALID_LOGIN" == $json_decoded->status_txt) {
          message::error(t("Your bit.ly login is incorrect"));
        } else if ("INVALID_APIKEY" == $json_decoded->status_txt) {
          message::error(t("Your bit.ly API Key is incorrect."));
        }
        return false;
      } else {
        return true;
      }
    }
  }

  /**
   * Check whether the module's configured correctly
   * @return boolean
   */
  static function check_config() {
    $login = module::get_var("bitly", "login");
    $api_key = module::get_var("bitly", "api_key");
    if (empty($login) || empty($api_key)) {
      site_status::warning(
        t("bit.ly is not quite ready!  Please provide a <a href=\"%url\">login and API Key</a>",
          array("url" => html::mark_clean(url::site("admin/bitly")))),
        "bitly_config");

    } else if (!self::validate_config($login, $api_key)) {
      site_status::warning(
        t("bit.ly is not properly configured!  URLs will not be shortened until its <a href=\"%url\">configuration</a> is updated.",
          array("url" => html::mark_clean(url::site("admin/bitly")))),
        "bitly_config");
    } else {
      site_status::clear("bitly_config");
      return true;
    }
    return false;
  }

  /**
   *
   * @param <type> $type
   * @param <type> $parameters
   * @return string
   */
  private static function _build_http_request($type, $parameters) {
    $http_request = '';
    if (!empty($type) && count($parameters)) {
      foreach($parameters as $k => $v) {
        $query_string[] = "$k=" . urlencode($v);
      }
      $path = "/" . self::$api_version . "/$type?" . implode('&', $query_string);
      $module_version = module::get_version("bitly");

      $http_request  = "GET $path HTTP/1.0\r\n";
      $http_request .= "Host: " . self::$api_host . "\r\n";
      $http_request .= "User-Agent: Gallery/3 | bitly/" . module::get_version("bitly") . "\r\n";
      $http_request .= "\r\n";
      $http_request .= $path;
    }
    return $http_request;
  }

  /**
   * Send an http POST request
   * @param  string  $http_request
   * @param  string  $host
   * @return object
   */
  private static function _http_post($http_request) {
    $response = '';
    //Kohana_Log::add("debug", "Send request\n" . print_r($http_request, 1));
    if (false !== ($fs = @fsockopen(self::$api_host, 80, $errno, $errstr, 5))) {
      fwrite($fs, $http_request);
      while ( !feof($fs) ) {
        $response .= fgets($fs, 1160); // One TCP-IP packet
      }
      fclose($fs);
      list($headers, $body) = explode("\r\n\r\n", $response);
      $headers = explode("\r\n", $headers);
      $body = explode("\r\n", $body);
      $response = new ArrayObject(
        array("headers" => $headers, "body" => $body), ArrayObject::ARRAY_AS_PROPS);
    } else {
      throw new Exception("@todo CONNECTION TO URL SHORTENING SERVICE FAILED");
    }
    Kohana_Log::add("debug", "Received response\n" . print_r($response, 1));

    return $response;
  }

  static function build_link($path='/') {
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
    return url::site($path, $protocol);
  }

  /**
   * Shorten a Gallery URL
   * @param  string $long_url
   * @param  string $format
   * @return string
   */
  static function shorten_url($long_url, $format='json') {
    $short_url = '';
    $parameters = array(
      "login" => module::get_var("bitly", "login"),
      'apiKey' => module::get_var("bitly", "api_key"),
      'longUrl' => $long_url,
      'domain' => module::get_var("bitly", "domain"),
      'format' => $format,
      );
    $request = self::_build_http_request('shorten', $parameters);    
    $response = self::_http_post($request, self::$api_host);
    $json_decoded = json_decode($response->body[0]);
    if ('OK' == $json_decoded->status_txt) {
      // Save the original item id, the hash, and possibly the global hash, to the database
      $short_url = $json_decoded->data->url;
      $hash = $json_decoded->data->hash;
      $global_hash = $json_decoded->data->global_hash;
      $new_hash = $json_decoded->data->new_hash;
      message::success("The $long_url has been shortened to $short_url");
    } else {
      message::error("Unable to shorten the url");
      // @todo log the error
    }
    return $short_url;
  }

  /**
   *  returns expanded url
   * {
        "status_code": 200,
        "data": {
          "expand": [
            {
              "short_url": "http://tcrn.ch/a4MSUH",
              "global_hash": "bWw49z",
              "long_url": "http://www.techcrunch.com/2010/01/29/windows-mobile-foursquare/",
              "user_hash": "a4MSUH"
            },
            {
              "short_url": "http://bit.ly/1YKMfY",
              "global_hash": "1YKMfY",
              "long_url": "http://betaworks.com/",
              "user_hash": "1YKMfY"
            },
            {
              "long_url": "http://www.scotster.com/qf/?1152",
              "global_hash": "lLWr",
              "hash": "j3",
              "user_hash": "j3"
            },
            {
              "hash": "a35.",
              "error": "NOT_FOUND"
            }
          ]
        },
        "status_txt": "OK"
      }
   */
  /**
   *
   * @param <type> $short_url
   * @param <type> $hash
   * @param <type> $format
   * @return <type> 
   */
  static function expand_url($short_url, $hash=null, $format='json') {
    $parameters = array(
      "login" => module::get_var("bitly", "login"),
      'apiKey' => module::get_var("bitly", "api_key"),
      'shortUrl' => $short_url,
      'hash' => $hash,
      );
    $request = self::_build_http_request('expand', $parameters);
    $response = self::_http_post($http_request, self::$api_host);
    return $response;
  }
  
}
