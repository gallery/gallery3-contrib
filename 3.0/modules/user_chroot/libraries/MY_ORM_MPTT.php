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

class ORM_MPTT extends ORM_MPTT_Core {
  /**
   * Copied from modules/gallery/libraries/ORM_MPTT.php, not sure of the reason...
   */
  private $model_name = null;
  function __construct($id=null) {
    parent::__construct($id);
    $this->model_name = inflector::singular($this->table_name);
  }

  /**
   * Return the parent of this node
   *
   * @return ORM
   */
  function parent() {
    if( user_chroot::album() && user_chroot::album()->id == $this->id ) {
      return null;
    } else {
      return parent::parent();
    }
  }

  /**
   * Return all the parents of this node, in order from root to this node's immediate parent.
   *
   * @return array ORM
   */
  function parents() {
    $select = $this
      ->where('left_ptr', '<=', $this->left_ptr)
      ->where('right_ptr', '>=', $this->right_ptr)
      ->where('id', '<>', $this->id)
      ->order_by('left_ptr', 'ASC');

    if( user_chroot::album() ) {
      $select->where('left_ptr', '>=', user_chroot::album()->left_ptr);
      $select->where('right_ptr', '<=', user_chroot::album()->right_ptr);
    }

    return $select->find_all();
  }
}
