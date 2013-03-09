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
class Admin_Pdf_Controller extends Admin_Controller {
  public function index() {
    // print screen from new form
    $form = $this->_get_admin_form();
    $this->_print_screen($form);
  }

  public function save() {
    access::verify_csrf();
    $form = $this->_get_admin_form();
    if ($form->validate()) {
      module::set_var("pdf", "make_thumb", ($form->settings->make_thumb->value == 1));
      module::set_var("pdf", "movie_overlay_hide", ($form->settings->movie_overlay_hide->value == 1));
      if ($form->settings->rebuild_thumbs->value == 1) {
        pdf::mark_dirty();
      }
      // All done - redirect with message
      message::success(t("PDF settings updated successfully"));
      url::redirect("admin/pdf");
    }
    // Not valid - print screen from existing form
    $this->_print_screen($form);
  }

  private function _print_screen($form) {
    // get module parameters
    $gs_path = pdf::find_gs();
    $gs_dir = substr($gs_path, 0, strrpos($gs_path, "/"));
    $gs_version = pdf::get_gs_version();
    // make and print view
    $view = new Admin_View("admin.html");
    $view->page_title = t("PDF settings");
    $view->content = new View("admin_pdf.html");
    $view->content->form = $form;
    $view->content->gs_dir = $gs_dir;
    $view->content->gs_version = $gs_version;
    print $view;
  }

  private function _get_admin_form() {
    $form = new Forge("admin/pdf/save", "", "post", array("id" => "g-pdf-admin-form"));
    $group = $form->group("settings")->label(t("PDF settings"));
    $group->checkbox("make_thumb")
      ->label(t("Generate thumbnails (only if Ghostscript is found)"))
      ->checked(module::get_var("pdf", "make_thumb", null));
    $group->checkbox("movie_overlay_hide")
      ->label(t("Hide overlay from \"movie_overlay\" module (tested with Movie Overlay v3)"))
      ->checked(module::get_var("pdf", "movie_overlay_hide", null));
    $group->checkbox("rebuild_thumbs")
      ->label(t("Mark all existing PDFs for rebuild - afterward, go to Maintenace | Rebuild Images"))
      ->checked(false); // always set as false
    $form->submit("")->value(t("Save"));
    return $form;
  }
}