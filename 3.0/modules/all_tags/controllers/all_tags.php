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
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
* General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA 02110-1301, USA.
*/
class All_Tags_Controller extends Controller {
  public function index() {
	
    $template = new Theme_View("page.html", "other", "All Tags");
    $template->css("all_tags.css");
    $template->page_title = t("Gallery :: All Tags");
    $template->content = new View("all_tags.html");

    $filter = Input::instance()->get("filter");
    $template->content->filter = $filter;
    $query = ORM::factory("tag");
    if ($filter) {
      $query->like("name", $filter);
    }
    $template->content->tags = $query->order_by("name", "ASC")->find_all();

    print $template;
  }
}

/*
  public function index() {
    $filter = Input::instance()->get("filter");

    $view = new Admin_View("admin.html");
    $view->page_title = t("Manage tags");
    $view->content = new View("admin_tags.html");
    $view->content->filter = $filter;

    $query = ORM::factory("tag");
    if ($filter) {
      $query->like("name", $filter);
    }
    $view->content->tags = $query->order_by("name", "ASC")->find_all();
    print $view;
  }
  */
