<?php defined('SYSPATH') OR die('No direct access allowed.');
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
// This class does not depend on any Kohana services so that it can be used in non-Kohana
// applications.
class G3Remote {
  protected static $_instance;

  private $_gallery3_site;
  private $_access_token;

  public static function instance($site=null, $access_token=null) {
    if (!isset(G3Remote::$_instance)) {
      G3Remote::$_instance = new G3Remote($site, $access_token);
    }

    return G3Remote::$_instance;
  }

  /**
   * Constructs a new G3Remote object
   *
   * @param   array  Database config array
   * @return  G3Remote
   */
  protected function __construct($site, $access_token) {
    // Store the config locally
    $this->_gallery3_site = $site;
    $this->_access_token = $access_token;
  }

  public function get_access_token($user, $password) {
    $request = "{$this->_gallery3_site}/access_key";
    list ($response_status, $response_headers, $response_body) =
      G3Remote::_get($request, array("user" => $user, "password" => $password));
    if (G3Remote::_success($response_status)) {
      $response = json_decode($response_body);
      if ($response->status == "OK") {
        $this->_access_token = $response->token;
      } else {
        throw new Exception("Remote host failure: {$response->message}");
      }
    } else {
      throw new Exception("Remote host failure: $response_status");
    }
    return $this->_access_token;
  }

  public function get_resource($path, $params=array()) {
    return $this->_do_request("get", $path, $params);
  }

  public function delete_resource($path) {
    return $this->_do_request("delete", $path);
  }

  public function update_resource($path, $params) {
    return $this->_do_request("put", $path, $params);
  }

  public function add_resource($path, $params) {
    return $this->_do_request("post", $path, $params);
  }

  private function _do_request($method, $path, $params=array()) {
    $request_path = "{$this->_gallery3_site}/$path";
    $headers = array();
    if ($method == "put" || $method == "delete") {
      $headers["X_GALLERY_REQUEST_METHOD"] = $method;
      $method = "post";
    }
    if (!empty($this->_access_token)) {
      $headers["X_GALLERY_REQUEST_KEY"] = $this->_access_token;
    }

    list ($response_status, $response_headers, $response_body) =
      $method == "get" ? G3Remote::_get($request_path, $params, $headers) :
                         G3Remote::_post($request_path, $params, $headers);

    if (G3Remote::_success($response_status)) {
      $response = json_decode($response_body);
      switch ($response->status) {
      case "OK":
      case "VALIDATE_ERROR":
        return $response;
      default:
        throw new Exception("Remote host failure: {$response->message}");
      }
    } else {
      throw new Exception("Remote host failure: $response_status");
    }
  }

  private static function _post($url, $post_data_array, $extra_headers=array()) {
    $boundary = str_repeat("-", 9) . md5(microtime());
    $boundary_length = strlen($boundary);

    $extra_headers['Content-Type'] = "multipart/form-data; boundary=$boundary";
    $length = 0;
    $fields = array();
    foreach ($post_data_array as $field => $value) {
      $fields[$field] = array();
      if (is_string($value)) {
        $fields[$field]["disposition"] = "Content-Disposition: form-data; name=\"$field\"\r\n\r\n";
        $fields[$field]["type"] = "";
        $fields[$field]["value"] = "$value\r\n";
      } else {
        $fields[$field]["disposition"] =
          "Content-Disposition: form-data; name=\"$field\"; filename=\"{$value->name}\"\r\n";
        $fields[$field]["type"] = "Content-Type: {$value->type}\r\n\r\n";
        $fields[$field]["value"] = "\r\n";
        $fields[$field]["local_file"] = $value->tmp_name;
        $length += $value->size;
      }
      $length += strlen($fields[$field]["disposition"]) + strlen($fields[$field]["value"]) +
        strlen($fields[$field]["type"]) + $boundary_length + 4;
    }
    $length += $boundary_length + 6;  // boundary terminator and last crlf
    $extra_headers['Content-Length'] = $length;

    $socket = G3Remote::_open_socket($url, 'POST', $extra_headers);

    $sent_length = 0;
    foreach ($fields as $field => $value) {
      $sent_length += fwrite($socket, "--$boundary\r\n");
      $sent_length += fwrite($socket, $value["disposition"]);
      if (!empty($value["type"])) {
        $sent_length += fwrite($socket, $value["type"]);
        $file = fopen($value["local_file"], "rb");
        while (!feof($file)) {
          $buffer = fread($file, 8192);
          $sent_length += fwrite($socket, $buffer);
          fflush($socket);
        }
      }
      $sent_length += fwrite($socket, $value["value"]);
      fflush($socket);
    }

    $sent_length += fwrite($socket, "--$boundary--\r\n");
    fflush($socket);

    /* Read the web page into a buffer */
    return G3Remote::_get_response($socket);
  }

  private static function _get($url, $_data_array=array(), $extra_headers=array()) {
    $_data_raw = self::_encode_data($_data_array, $extra_headers);

    $handle = G3Remote::_open_socket("{$url}?$_data_raw", "GET", $extra_headers);

    /* Read the web page into a buffer */
    return G3Remote::_get_response($handle);
  }

  private static function _success($response_status) {
    return preg_match("/^HTTP\/\d+\.\d+\s2\d{2}(\s|$)/", trim($response_status));
  }

  /**
   * Encode the data.  For each key/value pair, urlencode both the key and the value and then
   * concatenate together.  As per the specification, each key/value pair is separated with an
   * ampersand (&)
   * @param array $data_array the key/value data
   * @param array $extra_headers extra headers to pass to the server
   * @return string the encoded post data
   */
  private static function _encode_data($_data_array, &$extra_headers) {
    $_data_raw = '';
    foreach ($_data_array as $key => $value) {
      if (!empty($_data_raw)) {
        $_data_raw .= '&';
      }
      $_data_raw .= urlencode($key) . '=' . urlencode($value);
    }

    return $_data_raw;
  }

  /**
   * Open the socket to server
   */
  static function _open_socket($url, $method='GET', $headers=array()) {
    /* Convert illegal characters */
    $url = str_replace(' ', '%20', $url);

    $url_components = self::_parse_url_for_fsockopen($url);
    $handle = fsockopen(
      $url_components['fsockhost'], $url_components['port'], $errno, $errstr, 5);
    if (empty($handle)) {
      return array(null, null, null);
    }

    $header_lines = array('Host: ' . $url_components['host']);
    foreach ($headers as $key => $value) {
      $header_lines[] = $key . ': ' . $value;
    }

    $success = fwrite($handle, sprintf("%s %s HTTP/1.0\r\n%s\r\n\r\n",
                                       $method,
                                       $url_components['uri'],
                                       implode("\r\n", $header_lines)));
    fflush($handle);

    return $handle;
  }

  /**
   * Read the http response
   */
  static function _get_response($handle) {
    /*
     * Read the status line.  fgets stops after newlines.  The first line is the protocol
     * version followed by a numeric status code and its associated textual phrase.
     */
    $response_status = trim(fgets($handle, 4096));
    if (empty($response_status)) {
      // 'Empty http response code, maybe timeout'
      return array(null, null, null);
    }

    /* Read the headers */
    $response_headers = array();
    while (!feof($handle)) {
      $line = trim(fgets($handle, 4096));
      if (empty($line)) {
        break;
      }

      /* Normalize the line endings */
      $line = str_replace("\r", '', $line);

      list ($key, $value) = explode(':', $line, 2);
      if (isset($response_headers[$key])) {
        if (!is_array($response_headers[$key])) {
          $response_headers[$key] = array($response_headers[$key]);
        }
        $response_headers[$key][] = trim($value);
      } else {
        $response_headers[$key] = trim($value);
      }
    }

    /* Read the body */
    $response_body = '';
    while (!feof($handle)) {
      $response_body .= fread($handle, 4096);
    }
    fclose($handle);

    return array($response_status, $response_headers, $response_body);
  }

  /**
   * Prepare for fsockopen call.
   * @param string $url
   * @return array url components
   * @access private
   */
  private static function _parse_url_for_fsockopen($url) {
    $url_components = parse_url($url);
    if (strtolower($url_components['scheme']) == 'https') {
      $url_components['fsockhost'] = 'ssl://' . $url_components['host'];
      $default_port = 443;
    } else {
      $url_components['fsockhost'] = $url_components['host'];
      $default_port = 80;
    }
    if (empty($url_components['port'])) {
      $url_components['port'] = $default_port;
    }
    if (empty($url_components['path'])) {
      $url_components['path'] = '/';
    }
    $uri = $url_components['path']
      . (empty($url_components['query']) ? '' : '?' . $url_components['query']);
    /* Unescape ampersands, since if the url comes from form input it will be escaped */
    $url_components['uri'] = str_replace('&amp;', '&', $uri);

    return $url_components;
  }
}
