<?php defined("SYSPATH") or die("No direct script access.");
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
class Admin_Session_Explorer_Controller extends Admin_Controller {
  public function index() {
    list($uas, $ips, $sample_size) = $this->get_uas_and_ips();

    $view = new Admin_View("admin.html");
    $view->page_title = t("Session explorer");
    $view->content = new View("admin_session_explorer.html");
    $view->content->uas = $uas;
    $view->content->ips = $ips;
    $view->content->sample_size = $sample_size;
    print $view;
  }

  private function get_uas_and_ips() {
    $uas = array();
    $ips = array();
    $sample_size = 0;

    $d = new Session_Database_Driver();
    foreach (db::build()
             ->select("session_id")
             ->from("sessions")
             ->execute() as $r) {
      $data = $this->unserialize_session($d->read($r->session_id));
      $ua = $data["user_agent"];
      $ip = $data["ip_address"];
      if (!isset($uas[$ua])) {
        $uas[$ua] = 0;
      }
      if (!isset($ips[$ip])) {
        $ips[$ip] = 0;
      }
      $uas[$ua]++;
      $ips[$ip]++;

      // Limit the sample size once we've found N user agents
      if (++$sample_size == 5000) {
        break;
      }
    }
    arsort($uas);
    arsort($ips);

    // Top N only
    array_splice($uas, 15);
    array_splice($ips, 15);

    return array($uas, $ips, $sample_size);
  }

  // Adapted from
  // http://us3.php.net/manual/en/function.session-decode.php#101687
  // by jason at joeymail dot net
  function unserialize_session($data) {
    if (strlen($data) == 0) {
      return array();
    }

    // match all the session keys and offsets
    preg_match_all('/(^|;|\})([a-zA-Z0-9_]+)\|/i', $data, $matches_array, PREG_OFFSET_CAPTURE);

    $return_array = array();

    $last_offset = null;
    $current_key = '';
    foreach ($matches_array[2] as $value) {
      $offset = $value[1];
      if(!is_null($last_offset)) {
        $value_text = substr($data, $last_offset, $offset - $last_offset);
        $return_array[$current_key] = unserialize($value_text);
      }
      $current_key = $value[0];
      $last_offset = $offset + strlen($current_key) + 1;
    }

    $value_text = substr($data, $last_offset);
    try {
      $return_array[$current_key] = unserialize($value_text);
    } catch (ErrorException $e) {
      // Dunno why unserialize fails.  If it fails enough, it'll show up in the aggregate
      // counts and we can deal with it.
      return array("user_agent" => "[unserialize fail]", "ip_address" => "[unserialize fail]");
    }
    return $return_array;
  }
}