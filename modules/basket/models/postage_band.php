<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class Postage_Band_Model extends ORM {
  protected $has_many = array("products");

  /**
   * Specify our rules here so that we have access to the instance of this model.
   */
  public function validate($array=null) {
    if (!$array) {
      $this->rules = array(
        "name"      => array("rules" => array("required", "length[1,32]"),
                             "callbacks" => array(array($this, "valid_name"))),
        "flat_rate" => array("rules" => array("required", "decimal")),
        "per_item"  => array("rules" => array("required")));

    }

    parent::validate($array);
  }

  /**
   * Validate the item name.  It can't conflict with other names, can't contain slashes or
   * trailing periods.
   */
  public function valid_name(Validation $v, $field) {
    $postage_band = ORM::factory("postage_band")->where("name", "=", $this->name)->find();
    if ($postage_band->loaded() && $postage_band->id != $this->id) {
      $v->add_error("name", "in_use");
    }
  }
}
