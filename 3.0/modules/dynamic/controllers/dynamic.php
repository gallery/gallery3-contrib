<?php defined("SYSPATH") or die("No direct script access.");/**
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
    if ($page < 1) {
      url::redirect(url::merge(array("page" => 1)));
    } else if ($page > $max_pages) {
      url::redirect(url::merge(array("page" => $max_pages)));
    }

    $root = item::root();
    $template = new Theme_View("page.html", "collection", "dynamic");
    $template->set_global(
      array("page" => $page,
            "max_pages" => $max_pages,
            "page_size" => $page_size,
            "children" => dynamic::items($album_defn->key_field, $page_size, $offset),
            "breadcrumbs" => array(
              Breadcrumb::instance($root->title, $root->url())->set_first(),
              Breadcrumb::instance($album_defn->title,
                                   url::site("dynamic/$album"))->set_last()),
            "children_count" => $children_count));
    $template->content = new View("dynamic.html");
    $template->content->title = t($album_defn->title);

    print $template;

    item::set_display_context_callback("Dynamic_Controller::get_display_context",
                                       $album_defn, $album);
  }

  static function get_display_context($item, $album_defn, $path) {
    $where = array(array("type", "!=", "album"));

    $position = dynamic::get_position($album_defn, $item, $where);
    if ($position > 1) {
      list ($previous_item, $ignore, $next_item) =
        dynamic::items($album_defn->key_field, 3, $position - 2);
    } else {
      $previous_item = null;
      list ($next_item) = dynamic::items($album_defn->key_field, 1, $position);
    }

    $root = item::root();
    return array("position" => $position,
                 "previous_item" => $previous_item,
                 "next_item" => $next_item,
                 "sibling_count" => dynamic::get_display_count($album_defn),
                 "siblings_callback" => array("dynamic::items", array($album_defn->key_field)),
                 "breadcrumbs" => array(
                   Breadcrumb::instance($root->title, $root->url())->set_first(),
                   Breadcrumb::instance($album_defn->title,
                                        url::site("dynamic/$path?show={$item->id}")),
                   Breadcrumb::instance($item->title, $item->url())->set_last()));
  }
}
