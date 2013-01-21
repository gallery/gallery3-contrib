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
class Pages_Controller extends Controller {
  public function show($page_name) {
    // Display the page specified by $page_name, or a 404 error if it doesn't exist.

    // Run a database search to look up the page.
    $existing_page = ORM::factory("static_page")
                     ->where("name", "=", $page_name)
                     ->find_all();

    // If it doesn't exist, display a 404 error.
    if (count($existing_page) == 0) {
      throw new Kohana_404_Exception();
    }

    // Set up breadcrumbs.
    $breadcrumbs = array();
    $root = item::root();
    $breadcrumbs[] = Breadcrumb::instance($root->title, $root->url())->set_first();
    $breadcrumbs[] = Breadcrumb::instance(t($existing_page[0]->title), url::site("pages/show/{$page_name}"))->set_last();

    // Display the page.
    $template = new Theme_View("page.html", "other", "Pages");
    $template->set_global(array("breadcrumbs" => $breadcrumbs));
    $template->page_title = t("Gallery :: ") . t($existing_page[0]->title);
    $template->content = new View("pages_display.html");
    $template->content->title = $existing_page[0]->title;
    $template->content->body = $existing_page[0]->html_code;
    print $template;
  }
}
