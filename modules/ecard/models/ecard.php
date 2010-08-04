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
class ecard_Model extends ORM {
  function item() {
    return ORM::factory("item", $this->item_id);
  }

  function author() {
    return identity::lookup_user($this->author_id);
  }

  function author_name() {
    $author = $this->author();
    if ($author->guest) {
      return $this->from_name;
    } else {
      return $author->display_name();
    }
  }

  function author_email() {
    $author = $this->author();
    if ($author->guest) {
      return $this->from_email;
    } else {
      return $author->email;
    }
  }

  /**
   * Add some custom per-instance rules.
   */
  public function validate(Validation $array=null) {
    // validate() is recursive, only modify the rules on the outermost call.
    if (!$array) {
      $this->rules = array(
        "from_name"  => array("callbacks" => array(array($this, "valid_author"))),
        "from_email" => array("callbacks" => array(array($this, "valid_email"))),
        "to_name"   => array("rules"     => array("required")),
        "item_id"     => array("callbacks" => array(array($this, "valid_item"))),
        "to_email"       => array("rules"     => array("required")),
        "message"        => array("rules"     => array("required")),
      );
    }

    parent::validate($array);
  }

  /**
   * @see ORM::save()
   */
  public function save() {
    return $this;
  }

  /**
   * Add a set of restrictions to any following queries to restrict access only to items
   * viewable by the active user.
   * @chainable
   */
  public function viewable() {
    $this->join("items", "items.id", "ecards.item_id");
    return item::viewable($this);
  }

  /**
   * Make sure we have an appropriate author id set, or a guest name.
   */
  public function valid_author(Validation $v, $field) {
    if (empty($this->author_id)) {
      $v->add_error("author_id", "required");
    } else if ($this->author_id == identity::guest()->id && empty($this->from_name)) {
      $v->add_error("from_name", "required");
    }
  }

  /**
   * Make sure that the email address is legal.
   */
  public function valid_email(Validation $v, $field) {
    if ($this->author_id == identity::guest()->id) {
      if (empty($v->from_email)) {
        $v->add_error("from_email", "required");
      } else if (!valid::email($v->from_email)) {
        $v->add_error("from_email", "invalid");
      }
    }
  }

  /**
   * Make sure we have a valid associated item id.
   */
  public function valid_item(Validation $v, $field) {
    if (db::build()
        ->from("items")
        ->where("id", "=", $this->item_id)
        ->count_records() != 1) {
      $v->add_error("item_id", "invalid");
    }
  }

  /**
   * Make sure that the state is legal.
   */
  static function valid_state($value) {
    return in_array($value, array("published", "unpublished", "spam", "deleted"));
  }
}
