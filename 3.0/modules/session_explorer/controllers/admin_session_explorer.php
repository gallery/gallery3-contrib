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
class Admin_Session_Explorer_Controller extends Admin_Controller {
  public function index() {
    list($uas, $ips) = $this->get_uas_and_ips();

    $view = new Admin_View("admin.html");
    $view->page_title = t("Akismet spam filtering");
    $view->content = new View("admin_session_explorer.html");
    $view->content->uas = $uas;
    $view->content->ips = $ips;
    print $view;
  }

  private function get_uas_and_ips() {
    $uas = array();
    $ips = array();
    $d = new Session_Database_Driver();
    foreach (db::build()
             ->select("session_id")
             ->from("sessions")
             ->execute() as $r) {
      $data = explode("|", $d->read($r->session_id));
      $ua = unserialize($data[4]);
      $ip = unserialize($data[5]);
      if (!isset($uas[$ua])) {
        $uas[$ua] = 0;
      }
      if (!isset($ips[$ip])) {
        $ips[$ip] = 0;
      }
      $uas[$ua]++;
      $ips[$ip]++;
    }
    arsort($uas);
    arsort($ips);

    // Top 20 only
    array_splice($uas, 20);
    array_splice($ips, 20);

    return array($uas, $ips);
  }
}