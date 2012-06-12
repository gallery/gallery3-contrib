<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
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
class latestupdates_Controller extends Controller {
  public function user_profiles($str_display_type, $user_id) {
    // Make sure user_id is valid.
    $current_user = ORM::factory("user", $user_id);
    if (!$current_user->loaded()) {
      throw new Kohana_404_Exception();
    }

    // Grab the first 10 items for the specified display time.
    //   Default to "popular" if display type is invalid.
    $template = new View("latestupdates_user_profile_carousel.html");
    if ($str_display_type == "recent") {
      $template->items = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("owner_id", "=", $user_id)
        ->order_by("created", "DESC")
        ->find_all(10);
      $template->str_view_more_title = t("View all recent uploads");
    } elseif ($str_display_type == "albums") {
      $template->items = ORM::factory("item")
        ->viewable()
        ->where("type", "=", "album")
        ->where("owner_id", "=", $user_id)
        ->order_by("created", "DESC")
        ->find_all(10);
      $template->str_view_more_title = t("View all recent albums");
    } else {
      $template->items = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("owner_id", "=", $user_id)
        ->order_by("view_count", "DESC")
        ->find_all(10);
      $template->str_view_more_title = t("View more popular uploads");
    }
    $template->str_view_more_url = url::site("latestupdates/users/{$str_display_type}/{$user_id}");

    // Display the page.
    print $template;

    item::set_display_context_callback("latestupdates_Controller::get_display_context",
                                       $str_display_type, $user_id);
    return ;
  }

  public function users($str_display_type, $user_id) {
    // Generate a dynamic page with items uploaded by a specific user ($user_id).

    // Make sure user_id is valid.
    $current_user = ORM::factory("user", $user_id);
    if (!$current_user->loaded()) {
      throw new Kohana_404_Exception();
    }

    // Figure out how many items to display on each page.
    $page_size = module::get_var("gallery", "page_size", 9);

    // Figure out which page # the visitor is on and
    //	don't allow the visitor to go below page 1.
    $page = Input::instance()->get("page", 1);
    if ($page < 1) {
      url::redirect("latestupdates/users/{$str_display_type}/{$user_id}");
    }

    // If this page was reached from a breadcrumb, figure out what page to load from the show id.
    $show = Input::instance()->get("show");
    if ($show) {
      $child = ORM::factory("item", $show);
      $index = latestupdates_Controller::_get_position($child, $str_display_type, $user_id);
        if ($index) {
          $page = ceil($index / $page_size);
          if ($page == 1) {
            url::redirect("latestupdates/users/{$str_display_type}/{$user_id}");
          } else {
            url::redirect("latestupdates/users/{$str_display_type}/{$user_id}?page=$page");
          }
        }
      }

    // First item to display.
    $offset = ($page - 1) * $page_size;

    // Determine the total number of items,
    //	for page numbering purposes.
    $count = 0;
    if ($str_display_type == "albums") {
      $count = ORM::factory("item")
        ->viewable()
        ->where("type", "=", "album")
        ->where("owner_id", "=", $user_id)
        ->count_all();
    } else {
      $count = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("owner_id", "=", $user_id)
        ->count_all();
    }

    // Figure out what the highest page number is.
    $max_pages = ceil($count / $page_size);

    // Don't let the visitor go past the last page.
    if ($max_pages && $page > $max_pages) {
      url::redirect("latestupdates/users/{$str_display_type}/{$user_id}?page=$max_pages");
    }

    // Figure out which items to display on this page.
    $children = "";
    $str_page_title = "";
    if ($str_display_type == "recent") {
      $children = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("owner_id", "=", $user_id)
        ->order_by("created", "DESC")
        ->find_all($page_size, $offset);
      $str_page_title = "Recent Uploads";
    } elseif ($str_display_type == "albums") {
      $children = ORM::factory("item")
        ->viewable()
        ->where("type", "=", "album")
        ->where("owner_id", "=", $user_id)
        ->order_by("created", "DESC")
        ->find_all($page_size, $offset);
      $str_page_title = "Recent Albums";
    } else {
      $children = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("owner_id", "=", $user_id)
        ->order_by("view_count", "DESC")
        ->find_all($page_size, $offset);
      $str_page_title = "Most Viewed";
    }

    // Set up the previous and next page buttons.
    if ($page > 1) {
      $previous_page = $page - 1;
      $view->previous_page_link = url::site("latestupdates/users/{$str_display_type}/{$user_id}?page={$previous_page}");
    }
    if ($page < $max_pages) {
      $next_page = $page + 1;
      $view->next_page_link = url::site("latestupdates/users/{$str_display_type}/{$user_id}?page={$next_page}");
    }

    // Set up and display the actual page.
    $root = item::root();
    $template = new Theme_View("page.html", "collection", "LatestUpdates");
	$template->page_title = t("Gallery :: Latest Updates");
    $template->set_global(
      array("page" => $page,
            "max_pages" => $max_pages,
            "page_size" => $page_size,
            "children" => $children,
            "breadcrumbs" => array(
              Breadcrumb::instance($root->title, $root->url())->set_first(),
              Breadcrumb::instance(t("User profile: %name", array("name" => $current_user->display_name())), 
                                   url::site("user_profile/show/{$user_id}")),
              Breadcrumb::instance($str_page_title,
                                   url::site("latestupdates/users/{$str_display_type}/{$user_id}"))->set_last()),
            "children_count" => $count));
    $template->content = new View("dynamic.html");
    $template->content->title = t($str_page_title);

    print $template;

    item::set_display_context_callback("latestupdates_Controller::get_display_context",
                                       $str_display_type, $user_id);

  }

  static function get_display_context($item, $str_display_type, $user_id) {
    $current_user = ORM::factory("user", $user_id);

    $str_page_title = "";
    if ($str_display_type == "recent") {
      $str_page_title = "Recent Uploads";
    } elseif ($str_display_type == "albums") {
      $str_page_title = "Recent Albums";
    } else {
      $str_page_title = "Most Viewed";
    }

    $position = latestupdates_Controller::_get_position($item, $str_display_type, $user_id);
    if ($position > 1) {
      list ($previous_item, $ignore, $next_item) =
        latestupdates_Controller::items($str_display_type, $user_id, 3, $position - 2);
    } else {
      $previous_item = null;
      list ($next_item) = latestupdates_Controller::items($str_display_type, $user_id, 1, $position);
    }

    $count = 0;
    if ($str_display_type == "albums") {
      $count = ORM::factory("item")
        ->viewable()
        ->where("type", "=", "album")
        ->where("owner_id", "=", $user_id)
        ->count_all();
    } else {
      $count = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("owner_id", "=", $user_id)
        ->count_all();
    }

    $root = item::root();
    return array("position" => $position,
                 "previous_item" => $previous_item,
                 "next_item" => $next_item,
                 "sibling_count" => $count,
                 "breadcrumbs" => array(
                   Breadcrumb::instance($root->title, $root->url())->set_first(),
                   Breadcrumb::instance(t("User profile: %name", array("name" => $current_user->display_name())), 
                                        url::site("user_profile/show/{$user_id}")),
                   Breadcrumb::instance($str_page_title,
                                        url::site("latestupdates/users/{$str_display_type}/{$user_id}?show={$item->id}")),
                   Breadcrumb::instance($item->title, $item->url())->set_last())
                );
  }

  static function items($str_display_type, $user_id, $limit=null, $offset=null) {
    $str_where = array();
    $str_orderby_field = "";
    if ($str_display_type == "recent") {
      $str_where = array(array("type", "!=", "album"));
      $str_orderby_field = "created";
    } elseif ($str_display_type == "albums") {
      $str_where = array(array("type", "=", "album"));
      $str_orderby_field = "created";
    } else {
      $str_where = array(array("type", "!=", "album"));
      $str_orderby_field = "view_count";
    }

    return ORM::factory("item")
      ->viewable()
      ->merge_where($str_where)
      ->where("owner_id", "=", $user_id)
      ->order_by($str_orderby_field, "DESC")
      ->find_all($limit, $offset);
  }

  private function _get_position($item, $str_display_type, $user_id) {
    $str_where = array();
    $str_orderby_field = "";
    if ($str_display_type == "recent") {
      $str_where = array(array("type", "!=", "album"));
      $str_orderby_field = "created";
    } elseif ($str_display_type == "albums") {
      $str_where = array(array("type", "=", "album"));
      $str_orderby_field = "created";
    } else {
      $str_where = array(array("type", "!=", "album"));
      $str_orderby_field = "view_count";
    }

    $position = ORM::factory("item")
      ->viewable()
      ->where("owner_id", "=", $user_id)
      ->merge_where($str_where)
      ->where($str_orderby_field, ">", $item->$str_orderby_field)
      ->order_by($str_orderby_field, "DESC")
      ->count_all();

    foreach (ORM::factory("item")
             ->viewable()
             ->where("owner_id", "=", $user_id)
             ->merge_where($str_where)
             ->where($str_orderby_field, "=", $item->$str_orderby_field)
             ->order_by($str_orderby_field, "DESC")
             ->find_all() as $row) {
      $position++;
      if ($row->id == $item->id) {
        break;
      }
    }

    return $position;
  }

  public function albums($id) {
    // Figure out how many items to display on each page.
    $page_size = module::get_var("gallery", "page_size", 9);

    // Figure out which page # the visitor is on and
    //	don't allow the visitor to go below page 1.
    $page = Input::instance()->get("page", 1);
    if ($page < 1) {
      url::redirect("latestupdates/albums/{$item->id}");
    }

    // First item to display.
    $offset = ($page - 1) * $page_size;

    $item = ORM::factory("item", $id);

    // Determine the total number of items,
    //	for page numbering purposes.
    $count = $item
      ->viewable()
      ->where("type", "!=", "album")
      ->order_by("created", "DESC")
      ->descendants_count();

    // Figure out what the highest page number is.
    $max_pages = ceil($count / $page_size);

    // Don't let the visitor go past the last page.
    if ($max_pages && $page > $max_pages) {
      url::redirect("latestupdates/albums/{$item->id}?page=$max_pages");
    }

    // Figure out which items to display on this page.
    $children = $item
      ->viewable()
      ->where("type", "!=", "album")
      ->order_by("created", "DESC")
      ->descendants($page_size, $offset);

    // Set up the previous and next page buttons.
    if ($page > 1) {
      $previous_page = $page - 1;
      $view->previous_page_link = url::site("latestupdates/albums/{$item->id}?page={$previous_page}");
    }
    if ($page < $max_pages) {
      $next_page = $page + 1;
      $view->next_page_link = url::site("latestupdates/albums/{$item->id}?page={$next_page}");
    }

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "collection", "LatestUpdates");
    $template->page_title = t("Gallery :: Latest Updates");
    $template->set_global("page", $page);
    $template->set_global("page_size", $page_size);
    $template->set_global("max_pages", $max_pages);
    $template->set_global("children", $children);
    $template->set_global("children_count", $count);
    $template->content = new View("dynamic.html");
    $template->content->title = t("Latest Updates");
    print $template;
  }

  public function updates() {
    // Figure out how many items to display on each page.
    $page_size = module::get_var("gallery", "page_size", 9);

    // Figure out which page # the visitor is on and
    //	don't allow the visitor to go below page 1.
    $page = Input::instance()->get("page", 1);
    if ($page < 1) {
      url::redirect("latestupdates/updates");
    }

    // First item to display.
    $offset = ($page - 1) * $page_size;

    // Determine the total number of items,
    //	for page numbering purposes.
    $count = ORM::factory("item")
      ->viewable()
      ->where("type", "!=", "album")
      ->find_all()
      ->count();

    // Figure out what the highest page number is.
    $max_pages = ceil($count / $page_size);

    // Don't let the visitor go past the last page.
    if ($max_pages && $page > $max_pages) {
      url::redirect("latestupdates/updates?page=$max_pages");
    }

    // Figure out which items to display on this page.
    $items = ORM::factory("item")
      ->viewable()
      ->where("type", "!=", "album")
      ->order_by("created", "DESC")
      ->find_all($page_size, $offset);

    // Set up the previous and next page buttons.
    if ($page > 1) {
      $previous_page = $page - 1;
      $view->previous_page_link = url::site("latestupdates/updates?page={$previous_page}");
    }
    if ($page < $max_pages) {
      $next_page = $page + 1;
      $view->next_page_link = url::site("latestupdates/updates?page={$next_page}");
    }

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "collection", "LatestUpdates");
    $template->page_title = t("Gallery :: Latest Updates");
    $template->set_global("page", $page);
    $template->set_global("page_size", $page_size);
    $template->set_global("max_pages", $max_pages);
    $template->set_global("children", $items);
    $template->set_global("children_count", $count);
    $template->content = new View ("dynamic.html");
    $template->content->title = t("Latest Updates");
    print $template;
  }
}
