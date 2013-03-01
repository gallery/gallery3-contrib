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

class GalleryRemoteReply_Core {
  private $values = array();
  private $nl = "\n";
  /**
   * Constructor.
   * @param int $status a Gallery Remote status code
   */
  public static function factory($status='') {
    $reply = new GalleryRemoteReply();
    $reply->set('status', $status);
    $reply->set('status_text', '');
    return $reply;
  }

  public function clear() {
    $this->values = array();
  }

  /**
   * Set a property on this reply
   * @chainable
   */
  public function set($key, $value) {
    $this->values[$key] = $value;
    return $this;
  }
  
  public function send($status='') {
    if($status!='') $this->set('status', $status);
    //ksort($this->values);

    echo '#__GR2PROTO__'.$this->nl;
    foreach($this->values as $key => $value) {
      echo $key.'='.$value.$this->nl;
    }
  }
}
