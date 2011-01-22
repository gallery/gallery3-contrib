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
class Gallery_Remote_Controller extends Controller {
  public function index() {
    gallery_remote::check_protocol_version();

    $input = Input::instance();
    // TODO: Validate protocol version here
    switch($input->post("cmd")) {
    case "login":
      print "#__GR2PROTO__\n";
      $uname = $input->post("uname");
      if (empty($uname)) {
        print "status=202\n";
      } else {
        $user = user::lookup_by_name($uname);
        $password = $input->post("password");
        if ($user && user::is_correct_password($user, $password)) {
          print "status=0\n";
          user::login($user);
        } else {
          print "status=201\n";
        }
      }
      print "server_version=2.15\n";
    }
  }
}
