<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2011 Chad Parry
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
class Admin_RawPhoto_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  private function _get_view($errors = array(), $icc_path = null) {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_rawphoto.html");
    $view->content->dcraw = rawphoto_graphics::detect_dcraw();
    $toolkit_names = array("imagemagick" => "ImageMagick",
                           "graphicsmagick" => "GraphicsMagick");
    $toolkit_id = module::get_var("gallery", "graphics_toolkit");
    $view->content->toolkit_name = array_key_exists($toolkit_id, $toolkit_names) ?
                                   $toolkit_names[$toolkit_id] : "none";
    $view->content->icc_path = isset($icc_path) ?
                               $icc_path : module::get_var("rawphoto", "icc_path");
    $view->content->errors = $errors;
    return $view;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    $post = new Validation($_POST);
    $post->add_callbacks("IccPath", array($this, "_validate_icc_path"));
    $icc_path = Input::instance()->post("IccPath");
    if ($post->validate()) {
      module::set_var("rawphoto", "icc_path", $icc_path);
      message::success(t("Your preferences have been saved."));
    } else {
      message::error(t("Your preferences are not valid."));
    }

    print $this->_get_view($post->errors(), $icc_path);
  }

  public function _validate_icc_path(Validation $post, $field) {
    if (!empty($post->$field) && !@is_file($post->$field)) {
      $post->add_error($field, t("No ICC profile exists at the location <code>%icc_path</code>",
                                 array("icc_path" => $post->$field)));
    }
  }
}
