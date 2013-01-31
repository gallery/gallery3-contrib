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
class latestupdates_Controller extends Controller {
  public function user_profiles($str_display_type, $user_id) {
    // Make sure user_id is valid, throw a 404 error if its not.
    $current_user = ORM::factory("user", $user_id);
    if (!$current_user->loaded()) {
      throw new Kohana_404_Exception();
    }

    // Grab the first 10 items for the specified display type.
    //   Default to "popular" if display type is invalid.
    $template = new View("latestupdates_user_profile_carousel.html");
    $template->items = latestupdates_Controller::items($str_display_type, $user_id, 10);

    // Figure out the text for the "View more" link.
    if ($str_display_type == "recent") {
      $template->str_view_more_title = t("View all recent uploads");
    } elseif ($str_display_type == "albums") {
      $template->str_view_more_title = t("View all recent albums");
    } else {
      $template->str_view_more_title = t("View more popular uploads");
    }

    // Set up a "View more" url.
    $template->str_view_more_url = url::site("latestupdates/users/{$str_display_type}/{$user_id}");

    // Display the page.
    print $template;

    // Make item links in the carousel load as virtual albums for the view type instead of the regular album.
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
    $count = latestupdates_Controller::items_count($str_display_type, $user_id);

    // Figure out what the highest page number is.
    $max_pages = ceil($count / $page_size);

    // Don't let the visitor go past the last page.
    if ($max_pages && $page > $max_pages) {
      url::redirect("latestupdates/users/{$str_display_type}/{$user_id}?page=$max_pages");
    }

    // Figure out which items to display on this page.
    $children = latestupdates_Controller::items($str_display_type, $user_id, $page_size, $offset);

    // Figure out the page title.
    $str_page_title = "";
    if ($str_display_type == "recent") {
      $str_page_title = t("Recent Uploads");
    } elseif ($str_display_type == "albums") {
      $str_page_title = t("Recent Albums");
    } else {
      $str_page_title = t("Most Viewed");
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
    $template->content->title = $str_page_title;

    // Display the page.
    print $template;

    // Set up the callback so links within the photo page will lead to photos within the virtual album
    //   instead of the actual album.
    item::set_display_context_callback("latestupdates_Controller::get_display_context",
                                       $str_display_type, $user_id);
  }

  public function albums($id) {
    // Figure out how many items to display on each page.
    $page_size = module::get_var("gallery", "page_size", 9);

    // Load the parent album.
    $item = ORM::factory("item", $id);

    // Figure out which page # the visitor is on and
    //	don't allow the visitor to go below page 1.
    $page = Input::instance()->get("page", 1);
    if ($page < 1) {
      url::redirect("latestupdates/albums/{$item->id}");
    }

    // If this page was reached from a breadcrumb, figure out what page to load from the show id.
    $show = Input::instance()->get("show");
    if ($show) {
      $child = ORM::factory("item", $show);
      $index = latestupdates_Controller::_get_position($child, "descendants", $item->id);
      if ($index) {
        $page = ceil($index / $page_size);
        if ($page == 1) {
          url::redirect("latestupdates/albums/{$item->id}");
        } else {
          url::redirect("latestupdates/albums/{$item->id}?page=$page");
        }
      }
    }

    // First item to display.
    $offset = ($page - 1) * $page_size;

    // Determine the total number of items,
    //	for page numbering purposes.
    $count = latestupdates_Controller::items_count("descendants", $item->id);

    // Figure out what the highest page number is.
    $max_pages = ceil($count / $page_size);

    // Don't let the visitor go past the last page.
    if ($max_pages && $page > $max_pages) {
      url::redirect("latestupdates/albums/{$item->id}?page=$max_pages");
    }

    // Figure out which items to display on this page.
    $children = latestupdates_Controller::items("descendants", $item->id, $page_size, $offset);

    // Set up the previous and next page buttons.
    if ($page > 1) {
      $previous_page = $page - 1;
      $view->previous_page_link = url::site("latestupdates/albums/{$item->id}?page={$previous_page}");
    }
    if ($page < $max_pages) {
      $next_page = $page + 1;
      $view->next_page_link = url::site("latestupdates/albums/{$item->id}?page={$next_page}");
    }

    // Set up breadcrumbs.
    $breadcrumbs = array();
    $counter = 0;
    $breadcrumbs[] = Breadcrumb::instance(t("Recent Uploads"), url::site("latestupdates/albums/{$item->id}"))->set_last();
    $parent_item = $item;
    while ($parent_item->id != 1) {
      $breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url());
      $parent_item = ORM::factory("item", $parent_item->parent_id);
    }
    $breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url())->set_first();
    $breadcrumbs = array_reverse($breadcrumbs, true);

    // Set up and display the actual page.
    $root = item::root();
    $template = new Theme_View("page.html", "collection", "LatestUpdates");
    $template->page_title = t("Gallery :: Latest Updates");
    $template->set_global(
      array("page" => $page,
            "max_pages" => $max_pages,
            "page_size" => $page_size,
            "children" => $children,
            "breadcrumbs" => $breadcrumbs,
            "children_count" => $count));
    $template->content = new View("dynamic.html");
    $template->content->title = t("Recent Uploads");

    // Display the page.
    print $template;

    // Set up the callback so links within the photo page will lead to photos within the virtual album
    //   instead of the actual album.
    item::set_display_context_callback("latestupdates_Controller::get_display_context",
                                       "descendants", $item->id);
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

    // If this page was reached from a breadcrumb, figure out what page to load from the show id.
    $show = Input::instance()->get("show");
    if ($show) {
      $child = ORM::factory("item", $show);
      $index = latestupdates_Controller::_get_position($child, "recent", 0);
      if ($index) {
        $page = ceil($index / $page_size);
        if ($page == 1) {
          url::redirect("latestupdates/updates");
        } else {
          url::redirect("latestupdates/updates?page=$page");
        }
      }
    }

    // First item to display.
    $offset = ($page - 1) * $page_size;

    // Determine the total number of items,
    //	for page numbering purposes.
    $count = latestupdates_Controller::items_count("recent", 0);

    // Figure out what the highest page number is.
    $max_pages = ceil($count / $page_size);

    // Don't let the visitor go past the last page.
    if ($max_pages && $page > $max_pages) {
      url::redirect("latestupdates/updates?page=$max_pages");
    }

    // Figure out which items to display on this page.
    $items = latestupdates_Controller::items("recent", 0, $page_size, $offset);

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
    $root = item::root();
    $template = new Theme_View("page.html", "collection", "LatestUpdates");
	$template->page_title = t("Gallery :: Latest Updates");
    $template->set_global(
      array("page" => $page,
            "max_pages" => $max_pages,
            "page_size" => $page_size,
            "children" => $items,
            "breadcrumbs" => array(
              Breadcrumb::instance($root->title, $root->url())->set_first(),
              Breadcrumb::instance(t("Recent Uploads"),
                                   url::site("latestupdates/updates"))->set_last()),
            "children_count" => $count));
    $template->content = new View("dynamic.html");
    $template->content->title = t("Recent Uploads");

    // Display the page.
    print $template;

    // Set up the callback so links within the photo page will lead to photos within the virtual album
    //   instead of the actual album.
    item::set_display_context_callback("latestupdates_Controller::get_display_context",
                                       "recent", 0);
  }

  static function get_display_context($item, $str_display_type, $user_id) {
    // Set up display elements on the photo page to link to the virtual album.
    //  Valid $str_display_type values are popular, recent, albums and descendants.
    //  $user_id can be set to "0" to search site wide.
    //  For "descendants", $user_id should be the album id #.

    // Figure out page title.
    $str_page_title = "";
    if ($str_display_type == "recent") {
      $str_page_title = t("Recent Uploads");
    } elseif ($str_display_type == "albums") {
      $str_page_title = t("Recent Albums");
    } elseif ($str_display_type == "descendants") {
      $str_page_title = t("Recent Uploads");
    } else {
      $str_page_title = t("Most Viewed");
    }

    // Figure out item position.
    $position = latestupdates_Controller::_get_position($item, $str_display_type, $user_id);

    // Figure out which items are the previous and next items with the virtual album.
    if ($position > 1) {
      list ($previous_item, $ignore, $next_item) =
        latestupdates_Controller::items($str_display_type, $user_id, 3, $position - 2);
    } else {
      $previous_item = null;
      list ($next_item) = latestupdates_Controller::items($str_display_type, $user_id, 1, $position);
    }

    // Figure out total number of items (excluding albums).
    $count = latestupdates_Controller::items_count($str_display_type, $user_id);

    // Set up breadcrumbs.
    $root = item::root();
    $breadcrumbs = array();
    if ($user_id == 0) {
      $breadcrumbs[0] = Breadcrumb::instance($root->title, $root->url())->set_first();
      $breadcrumbs[1] = Breadcrumb::instance($str_page_title,
                             url::site("latestupdates/updates?show={$item->id}"));
      $breadcrumbs[2] = Breadcrumb::instance($item->title, $item->url())->set_last();
    } else {
      if ($str_display_type == "descendants") {
        $counter = 0;
        $breadcrumbs[] = Breadcrumb::instance($item->title, $item->url())->set_last();
        $breadcrumbs[] = Breadcrumb::instance(t("Recent Uploads"), url::site("latestupdates/albums/{$user_id}?show={$item->id}"));
        $parent_item = ORM::factory("item", $user_id);
        while ($parent_item->id != 1) {
          $breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url());
          $parent_item = ORM::factory("item", $parent_item->parent_id);
        }
        $breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url())->set_first();
        $breadcrumbs = array_reverse($breadcrumbs, true);
      } else {
        $current_user = ORM::factory("user", $user_id);
        $breadcrumbs[0] = Breadcrumb::instance($root->title, $root->url())->set_first();
        $breadcrumbs[1] = Breadcrumb::instance(t("User profile: %name", array("name" => $current_user->display_name())),
                                               url::site("user_profile/show/{$user_id}"));
        $breadcrumbs[2] = Breadcrumb::instance($str_page_title,
                               url::site("latestupdates/users/{$str_display_type}/{$user_id}?show={$item->id}"));
        $breadcrumbs[3] = Breadcrumb::instance($item->title, $item->url())->set_last();
      }
    }

    // Return the display elements.
    return array("position" => $position,
                 "previous_item" => $previous_item,
                 "next_item" => $next_item,
                 "sibling_count" => $count,
                 "siblings_callback" => array("latestupdates_Controller::items", array($str_display_type, $user_id)),
                 "breadcrumbs" => $breadcrumbs
                );
  }

  static function items_count($str_display_type, $user_id) {
    // Figure out the total number of items.
    //  Valid $str_display_type values are popular, recent, albums and descendants.
    //  $user_id can be set to "0" to search site wide.
    //  For "descendants", $user_id should be the album id #.

    // If $str_display_type is albums, then we only want albums.
    //   If it's not, then we want everything except albums.
    if ($str_display_type == "albums") {
      // This is only used for user profiles, so we always want
      //   results from a specific user.
      $count = ORM::factory("item")
        ->viewable()
        ->where("type", "=", "album")
        ->where("owner_id", "=", $user_id)
        ->count_all();
    } else {

      // If $user_id is not 0 we only want results from a specific user,
      //   Or else we want results from any user.
      if ($user_id == 0) {
        $count = ORM::factory("item")
          ->viewable()
          ->where("type", "!=", "album")
          ->count_all();
      } else {

        // If type is descendants, then user_id is actually an item id#.
        if ($str_display_type == "descendants") {
          $item = ORM::factory("item", $user_id);
          $count = $item
            ->viewable()
            ->where("type", "!=", "album")
            ->order_by("created", "DESC")
            ->descendants_count();
        } else {
          $count = ORM::factory("item")
            ->viewable()
            ->where("type", "!=", "album")
            ->where("owner_id", "=", $user_id)
            ->count_all();
        }
      }
    }

    return $count;
  }

  static function items($str_display_type, $user_id, $limit=null, $offset=null) {
    // Query the database for a list of items to display in the virtual album.
    //  Valid $str_display_type values are popular, recent, albums and descendants.
    //  $user_id can be set to "0" to search site wide.
    //  For "descendants", $user_id should be the album id #.

    // Figure out search parameters based on $str_display_type.
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

    // Search the database for matching items.

    // Searching for descendants of a parent album is significantly
    //   different from the other query types, so we're doing this one
    //   seperately.
    if ($str_display_type == "descendants") {
      $item = ORM::factory("item", $user_id);
      return $item
        ->viewable()
        ->where("type", "!=", "album")
        ->order_by("created", "DESC")
        ->descendants($limit, $offset);
    }

    //   If $user_id is greater then 0, limit results
    //   to a specific user.
    if ($user_id == 0) {
      return ORM::factory("item")
        ->viewable()
        ->merge_where($str_where)
        ->order_by($str_orderby_field, "DESC")
        ->find_all($limit, $offset);
    } else {
      return ORM::factory("item")
        ->viewable()
        ->merge_where($str_where)
        ->where("owner_id", "=", $user_id)
        ->order_by($str_orderby_field, "DESC")
        ->find_all($limit, $offset);
    }
  }

  private function _get_position($item, $str_display_type, $user_id) {
    // Figure out the item's position within the virtual album.
    //  Valid $str_display_type values are popular, recent, albums and descendants.
    //  $user_id can be set to "0" to search site wide.
    //  For "descendants", $user_id should be the album id #.

    // Figure out search conditions.
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

    // Count the number of records that have a higher orderby_field value then
    //   the item we're looking for.
    $position = 0;
    if ($user_id == 0) {
      $position = ORM::factory("item")
        ->viewable()
        ->merge_where($str_where)
        ->where($str_orderby_field, ">", $item->$str_orderby_field)
        ->order_by($str_orderby_field, "DESC")
        ->count_all();
    } else {
      if ($str_display_type == "descendants") {
        $album_item = ORM::factory("item", $user_id);
        $position = $album_item
          ->viewable()
          ->where("type", "!=", "album")
          ->where("created", ">", $item->created)
          ->order_by("created", "DESC")
          ->descendants_count();
      } else {
        $position = ORM::factory("item")
          ->viewable()
          ->where("owner_id", "=", $user_id)
          ->merge_where($str_where)
          ->where($str_orderby_field, ">", $item->$str_orderby_field)
          ->order_by($str_orderby_field, "DESC")
          ->count_all();
      }
    }

    // Set up a db query for all records with the same orderby field value
    //   as the item we're looking for.
    $items = ORM::factory("item");
    if ($user_id == 0) {
      $items->viewable()
            ->merge_where($str_where)
            ->where($str_orderby_field, "=", $item->$str_orderby_field)
            ->order_by($str_orderby_field, "DESC");
    } else {
      if ($str_display_type == "descendants") {
        $item_album = ORM::factory("item", $user_id);
        $items = $item_album
          ->viewable()
          ->where("type", "!=", "album")
          ->where("created", "=", $item->created)
          ->order_by("created", "DESC");
      } else {
        $items->viewable()
              ->where("owner_id", "=", $user_id)
              ->merge_where($str_where)
              ->where($str_orderby_field, "=", $item->$str_orderby_field)
              ->order_by($str_orderby_field, "DESC");
      }
    }

    // Loop through each remaining match, increasing position by 1 each time
    //   until we find a match.
    if ($str_display_type == "descendants") {
      foreach ($items->descendants() as $row) {
        $position++;
        if ($row->id == $item->id) {
          break;
        }
      }
    } else {
      foreach ($items->find_all() as $row) {
        $position++;
        if ($row->id == $item->id) {
          break;
        }
      }
    }

    // Return the result.
    return $position;
  }
}
