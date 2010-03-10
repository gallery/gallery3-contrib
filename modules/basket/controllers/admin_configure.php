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

class Admin_Configure_Controller extends Controller
{
  /**
   * the index page of the user homes admin
   */
  public function index()
  {
    $form = basket::get_configure_form();
    if (request::method() == "post") {
      // @todo move the "save" part of this into a separate controller function
      access::verify_csrf();

      if ($form->validate()) {

        basket::extractForm($form);
        message::success(t("Basket Module Configured!"));
        //url::redirect("admin/recaptcha");
      }
    }
    else
    {
      basket::populateForm($form);
    }

    $view = new Admin_View("admin.html");
    $view->content = new View("admin_configure.html");



    $view->content->form = $form;
    //$view->content->products = ORM::factory("product")->order_by("name")->find_all();

    print $view;
  }
}
