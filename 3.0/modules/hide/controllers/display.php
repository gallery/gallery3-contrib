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

class Display_Controller extends Controller {

  /**
   * Hides the given item.
   *
   * @param int $id  the item id
   */
  public function hide($id) {
    $item = model_cache::get("item", $id);
    $msg = t("Hidden <b>%title</b> item", array("title" => html::purify($item->title)));

    $this->_check_hide_permissions($item);
    hide::hide($item);
    message::success($msg);

    json::reply(array("result" => "success", "reload" => 1));
  }

  /**
   * Allows the given item to be displayed again.
   *
   * @param int $id  the item id
   */
  public function show($id) {
    $item = model_cache::get("item", $id);
    $msg = t("Displayed <b>%title</b> item", array("title" => html::purify($item->title)));

    $this->_check_hide_permissions($item);
    hide::show($item);
    message::success($msg);

    json::reply(array("result" => "success", "reload" => 1));
  }

  /**
   * Checks whether the given object can be hidden by the active user.
   *
   * @param Item_Model $item  the item
   */
  private function _check_hide_permissions(Item_Model $item) {
    access::verify_csrf();

    access::required("view", $item);
    access::required("edit", $item);

    if (!hide::can_hide()) {
      access::forbidden();
    }
  }
}
