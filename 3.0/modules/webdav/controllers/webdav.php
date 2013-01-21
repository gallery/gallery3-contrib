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
require_once(MODPATH . "webdav/vendor/Sabre/autoload.php");

class WebDAV_Controller extends Controller {
  const ALLOW_PRIVATE_GALLERY = true;
  public function gallery() {
    $root = new Gallery3_DAV_Album("");
    $tree = new Gallery3_DAV_Tree($root);

    // Skip the lock plugin for now, we don't want Finder to get write support for the time being.
    // $lock_backend = new Sabre_DAV_Locks_Backend_FS(TMPPATH . "sabredav");
    // $lock = new Sabre_DAV_Locks_Plugin($lock_backend);
    $filter = new Sabre_DAV_TemporaryFileFilterPlugin(TMPPATH . "sabredav");

    $server = new Sabre_DAV_Server($tree);
    $server->setBaseUri(url::site("webdav/gallery"));
    // $server->addPlugin($lock);
    $server->addPlugin($filter);
    $server->addPlugin(new Sabre_DAV_Browser_GuessContentType());

    if ($this->_authenticate()) {
      $server->exec();
    }
  }

  private function _authenticate() {
    $auth = new Sabre_HTTP_BasicAuth();
    $auth->setRealm(item::root()->title);
    $authResult = $auth->getUserPass();
    list($username, $password) = $authResult;

    if (!$username || !$password) {
      $auth->requireLogin();
      return false;
    }

    $user = identity::lookup_user_by_name($username);
    if (empty($user) || !identity::is_correct_password($user, $password)) {
      $auth->requireLogin();
      return false;
    }

    identity::set_active_user($user);
    return true;
  }
}

