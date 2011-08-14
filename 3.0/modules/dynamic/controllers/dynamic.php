<?php defined("SYSPATH") or die("No direct script access.");/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class Dynamic_Controller extends Controller {
  public function updates() {
    print $this->_show("updates", t("Recent changes"));
  }

  public function popular() {
    print $this->_show("popular", t("Most viewed"));
  }

  private function _show($album) {
    $page_size = module::get_var("gallery", "page_size", 9);

    $album_defn = unserialize(module::get_var("dynamic", $album));

    $input = Input::instance();
    $show = $input->get("show");

    if ($show) {
      $child = ORM::factory("item", $show);
      $index = dynamic::get_position($album_defn, $child);
      if ($index) {
        $page = ceil($index / $page_size);
        url::redirect("dynamic/$album" . ($page == 1 ? "" : "?page=$page"));
      }
    } else {
      $page = (int) $input->get("page", "1");
    }

    $children_count = dynamic::get_display_count($album_defn);

    $offset = ($page - 1) * $page_size;
    $max_pages = max(ceil($children_count / $page_size), 1);

    // Make sure that the page references a valid offset
    if ($page < 1 || ($children_count && $page > ceil($children_count / $page_size))) {
      throw new Kohana_404_Exception();
    }

    Display_Context::factory("dynamic")
      ->set(array("dynamic_type" => $album_defn, "path" => $album))
      ->save();

    $template = new Theme_View("page.html", "collection", "dynamic");
    $template->set_global("page", $page);
    $template->set_global("page_size", $page_size);
    $template->set_global("max_pages", $max_pages);
    $template->set_global("children", dynamic::items($album_defn->key_field, $page_size, $offset));
    $template->set_global("children_count", $children_count);
    $template->content = new View("dynamic.html");
    $template->content->title = t($album_defn->title);

    print $template;
  }

}