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
class tag_albums_Controller extends Controller {
  public function filter($id, $filter) {
    // Display the index page, but only show albums for 
    //  tags whose name begins with $filter.
    $this->index($id, $filter);
  }

  public function index($id, $filter) {
    // Load a page containing sub-albums for each tag in the gallery.

    // Check to see if the user has overridden default behavior, and act accordingly.
    if ((module::get_var("tag_albums", "tag_index_scope", "false")) || ($id == "")) {
      $tag_album_index_type = module::get_var("tag_albums", "tag_index", "default");
      if (($tag_album_index_type == "tagcloudpage") && (module::is_active("tag_cloud_page"))) {
        $redirect_url = "tag_cloud_page/";
        if ($id) {
          $redirect_url .= "?album={$id}";
        }
        url::redirect($redirect_url);
        return;
      } elseif (($tag_album_index_type == "alltags") && (module::is_active("all_tags"))) {
        $redirect_url = "all_tags/";
        if ($id) {
          $redirect_url .= "?album={$id}";
        }
        url::redirect($redirect_url);
        return;
      }
    }

    // If an ID was specified, make sure it's valid.
    $album_tags = ORM::factory("tags_album_id")
      ->where("id", "=", $id)
      ->find_all();
    if (count($album_tags) == 0) {
      $id = 0;
    }

    // Inherit permissions, title and description from the album that linked to this page,
    //  if available, if not use the root album and some default values.
    $album = "";
    $page_title = module::get_var("tag_albums", "tag_page_title", "All Tags");
    $page_description = "";
    $str_page_url = "";
    if ($id == 0) {
      $album = ORM::factory("item", 1);
      access::required("view", $album);
      $str_page_url = "tag_albums/";
    } else {
      $album = ORM::factory("item", $album_tags[0]->album_id);
      access::required("view", $album);
      $page_title = $album->title;
      $page_description = $album->description;
      $str_page_url = "tag_albums/album/" . $id . "/" . urlencode($album->title);
    }

    // Figure out sort order from module preferences.
    $sort_page_field = module::get_var("tag_albums", "tag_sort_by", "name");
    $sort_page_direction = module::get_var("tag_albums", "tag_sort_direction", "ASC");

    // Figure out how many items to display on each page.
    $page_size = module::get_var("gallery", "page_size", 9);

    // If this page was reached from a breadcrumb, figure out what page to load from the show id.
    $show = Input::instance()->get("show");
    if ($show) {
      $child = ORM::factory("tag", $show);
      $comp = "";
      if (!strcasecmp($sort_page_direction, "DESC")) {
        $comp = ">";
      } else {
        $comp = "<";
      }
      $index = ORM::factory("tag")
              ->where($sort_page_field, $comp, $child->$sort_page_field)
              ->order_by("tags." . $sort_page_field, $sort_page_direction)
              ->count_all();
      $tag_model = ORM::factory("tag")
                  ->where($sort_page_field, "=", $child->$sort_page_field)
                  ->order_by("tags." . $sort_page_field, $sort_page_direction)
                  ->find_all();
      foreach ($tag_model as $one_tag) {
        $index++;
        if ($one_tag->id == $show) {
          break;
        }
      }
      if ($index) {
        $page = ceil($index / $page_size);
        if ($page == 1) {
          url::redirect("$str_page_url");
        } else {
          url::redirect("$str_page_url?page=$page");
        }
      }
    }

    // Figure out which page # the visitor is on and
    //	don't allow the visitor to go below page 1.
    $page = Input::instance()->get("page", 1);
    if ($page < 1) {
      url::redirect($str_page_url);
    }

    // First item to display.
    $offset = ($page - 1) * $page_size;

    // Determine the total number of items,
    //	for page numbering purposes.
    $all_tags_count_model = ORM::factory("tag");
    if ($filter != "") {
      if ($filter == "NUM") {
        $all_tags_count_model->open();
        $all_tags_count_model->where("tags.name", "LIKE", "0%");
        $counter = 1;
        while ($counter < 10) {
          $all_tags_count_model->or_where("tags.name", "LIKE", ($counter++) . "%");
        }
        $all_tags_count_model->close();
      } else {
        $all_tags_count_model->where("tags.name", "LIKE", $filter . "%");
      }
    }
    $all_tags_count = $all_tags_count_model->count_all();

    // Figure out what the highest page number is.
    $max_pages = ceil($all_tags_count / $page_size);

    // Don't let the visitor go past the last page.
    if ($max_pages && $page > $max_pages) {
      url::redirect("$str_page_url?page=$max_pages");
    }

    // Figure out which items to display on this page.
    $display_tags_model = ORM::factory("tag");
    if ($filter != "") {
      if ($filter == "NUM") {
        $display_tags_model->open();
        $display_tags_model->where("tags.name", "LIKE", "0%");
        $counter = 1;
        while ($counter < 10) {
          $display_tags_model->or_where("tags.name", "LIKE", ($counter++) . "%");
        }
        $display_tags_model->close();
      } else {
        $display_tags_model->where("tags.name", "LIKE", $filter . "%");
      }
    }
    $display_tags_model->order_by("tags." . $sort_page_field, $sort_page_direction);
    $display_tags = $display_tags_model->find_all($page_size, $offset);

    // Set up the previous and next page buttons.
    if ($page > 1) {
      $previous_page = $page - 1;
      $view->previous_page_link = url::site($str_page_url . "?page={$previous_page}");
    }
    if ($page < $max_pages) {
      $next_page = $page + 1;
      $view->next_page_link = url::site($str_page_url . "?page={$next_page}");
    }

    // Generate an arry of "fake" items, one for each tag on the page.
    //   Grab thumbnails from a admin-specified photo, or the most recently 
    //   uploaded item for each tag, if available.
    $children_array = Array();
    foreach ($display_tags as $one_tag) {
      $tag_thumb_url = "";
      $tag_thumb_width = "";
      $tag_thumb_height = "";

      // Check and see if the admin specified a photo to use for this tags thumbnail.
      $record = ORM::factory("tags_album_tag_cover")->where("tag_id", "=", $one_tag->id)->find();
      if ($record->loaded()) {
        $tag_thumb_item = ORM::factory("item", $record->photo_id);
        if ($tag_thumb_item->loaded()) {
          $tag_thumb_url = $tag_thumb_item->thumb_url();
          $tag_thumb_width = $tag_thumb_item->thumb_width;
          $tag_thumb_height = $tag_thumb_item->thumb_height;
        }
      }

      // If no pre-specified thumbnail was found, use the most recently uploaded photo (if available).
      if ($tag_thumb_url == "") {
        $tag_item = ORM::factory("item")
          ->viewable()
          ->join("items_tags", "items.id", "items_tags.item_id")
          ->where("items_tags.tag_id", "=", $one_tag->id)
          ->order_by("items.id", "DESC")
          ->find_all(1, 0);
        if (count($tag_item) > 0) {
          if ($tag_item[0]->has_thumb()) {
            $tag_thumb_url = $tag_item[0]->thumb_url();
            $tag_thumb_width = $tag_item[0]->thumb_width;
            $tag_thumb_height = $tag_item[0]->thumb_height;
          }
        }
      }

      // Create a new object to represent this virtual album, and add it to the array of objects for
      //   this page.
      $child_tag =  new Tag_Albums_Item($one_tag->name, url::site("tag_albums/tag/" . $one_tag->id . "/" . $id . "/" . urlencode($one_tag->name)), "album", 0);
      if ($tag_thumb_url != "") {
        $child_tag->set_thumb($tag_thumb_url, $tag_thumb_width, $tag_thumb_height);
      }
      $children_array[] = $child_tag;
    }
    $children = new Tag_Albums_Children($children_array);

    // Set up breadcrumbs.
    $tag_album_breadcrumbs = Array();
    if ($id > 0) {
      $counter = 0;
      $tag_album_breadcrumbs[] = Breadcrumb::instance($album->title, $album->url())->set_last();
      $parent_item = ORM::factory("item", $album->parent_id);
      while ($parent_item->id != 1) {
        $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url());
        $parent_item = ORM::factory("item", $parent_item->parent_id);
      }
      $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url())->set_first();
      $tag_album_breadcrumbs[1]->url .= "?show=" . $album->id;
      $tag_album_breadcrumbs = array_reverse($tag_album_breadcrumbs, true);
    } else {
      $tag_album_breadcrumbs[] = Breadcrumb::instance(item::root()->title, item::root()->url())->set_first();
      $tag_album_breadcrumbs[] = Breadcrumb::instance($page_title, $str_page_url)->set_last();
    }

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "collection", "Tag Albums");
    $template->set_global(
      array("page" => $page,
            "max_pages" => $max_pages,
            "page_size" => $page_size,
            "children" => $children,
            "breadcrumbs" => $tag_album_breadcrumbs,
            "children_count" => $all_tags_count));
    $template->page_title = $page_title;
    $template->content = new View("dynamic.html");
    $template->content->title = $page_title;
    $template->content->description = $page_description;
    $template->content->filter_text = $this->_get_filter_html($id, $filter);
    print $template;
  }

  public function tag($id, $album_id) {
    // Display a dynamic album containing everything tagged with a specific tag where,
    //  TAG is $id.
    //  Optionally, set the breadcrumbs to make this page look like an album where the 
    //  album is $album_id.

    // Make sure $album_id is valid, clear it out if it isn't.
    $album_tags = ORM::factory("tags_album_id")
      ->where("id", "=", $album_id)
      ->find_all();
    if (count($album_tags) == 0) {
      $album_id = 0;
    }

    // Load the current tag.
    $display_tag = ORM::factory("tag", $id);

    // Figure out sort order from module preferences.
    $sort_page_field = module::get_var("tag_albums", "subalbum_sort_by", "title");
    $sort_page_direction = module::get_var("tag_albums", "subalbum_sort_direction", "ASC");

    // Figure out the URL to this page.
    $str_page_url = "tag_albums/tag/{$id}/{$album_id}/" . urlencode($display_tag->name);

    // Figure out how many items to display on each page.
    $page_size = module::get_var("gallery", "page_size", 9);

    // If this page was reached from a breadcrumb, figure out what page to load from the show id.
    $show = Input::instance()->get("show");
    if ($show) {
      $child = ORM::factory("item", $show);
      $index = $this->_get_position($child->$sort_page_field, $child->id, Array($id), "items." . $sort_page_field, $sort_page_direction, "OR", true);
      if ($index) {
        $page = ceil($index / $page_size);
        if ($page == 1) {
          url::redirect($str_page_url);
        } else {
          url::redirect($str_page_url . "?page=$page");
        }
      }
    }

    // Figure out which page # the visitor is on and
    //	don't allow the visitor to go below page 1.
    $page = Input::instance()->get("page", 1);
    if ($page < 1) {
      url::redirect($str_page_url);
    }

    // First item to display.
    $offset = ($page - 1) * $page_size;

    // Determine the total number of items,
    //	for page numbering purposes.
    $count = $this->_count_records(Array($id), "OR", true);

    // Figure out what the highest page number is.
    $max_pages = ceil($count / $page_size);

    // Don't let the visitor go past the last page.
    if ($max_pages && $page > $max_pages) {
      url::redirect($str_page_url . "/?page=$max_pages");
    }

    // Figure out which items to display on this page.
    $tag_children = $this->_get_records(Array($id), $page_size, $offset, "items." . $sort_page_field, $sort_page_direction, "OR", true); 
	
    // Set up the previous and next page buttons.
    if ($page > 1) {
      $previous_page = $page - 1;
      $view->previous_page_link = url::site($str_page_url . "/?page={$previous_page}");
    }
    if ($page < $max_pages) {
      $next_page = $page + 1;
      $view->next_page_link = url::site($str_page_url . "/?page={$next_page}");
    }

    // Set up breadcrumbs for the page.
    $tag_album_breadcrumbs = Array();
    if ($album_id > 0) {
      $counter = 0;
      $tag_album_breadcrumbs[] = Breadcrumb::instance($display_tag->name, $str_page_url)->set_last();
      $parent_item = ORM::factory("item", $album_tags[0]->album_id);
      if ($album_tags[0]->tags != "*") {
        $parent_item = ORM::factory("item", $parent_item->parent_id);
      }	else {
        $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->name, url::site("tag_albums/album/" . $album_tags[0]->id . "/" . urlencode($parent_item->name)));
        $parent_item = ORM::factory("item", $parent_item->parent_id);
      }
      while ($parent_item->id != 1) {
        $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url());
        $parent_item = ORM::factory("item", $parent_item->parent_id);
      }
      $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url())->set_first();
      $parent_item = ORM::factory("item", $album_tags[0]->album_id);
      $tag_album_breadcrumbs[1]->url .= "?show=" . $id;
      $tag_album_breadcrumbs = array_reverse($tag_album_breadcrumbs, true);
    } else {
      $tag_album_breadcrumbs[] = Breadcrumb::instance(item::root()->title, item::root()->url())->set_first();
      if (module::get_var("tag_albums", "tag_index", "default") == "default") {
        $tag_album_breadcrumbs[] = Breadcrumb::instance(module::get_var("tag_albums", "tag_page_title", "All Tags"), url::site("tag_albums/") . "?show=" . $id);
      } else {
      $tag_album_breadcrumbs[] = Breadcrumb::instance(module::get_var("tag_albums", "tag_page_title", "All Tags"), url::site("tag_albums/"));
      }
      $tag_album_breadcrumbs[] = Breadcrumb::instance($display_tag->name, $str_page_url)->set_last();
    }

    // Set up and display the actual page.
    $template = new Theme_View("page.html", "collection", "Tag Albums");
    $template->set_global(
      array("page" => $page,
            "max_pages" => $max_pages,
            "page_size" => $page_size,
            "children" => $tag_children,
            "breadcrumbs" => $tag_album_breadcrumbs,
            "children_count" => $count));
    $template->page_title = $display_tag->name;
    $template->content = new View("dynamic.html");
    $template->content->title = $display_tag->name;
    $template->content->description = isset($page_description) ? $page_description : "";

    $template->set_global("all_siblings", $this->_get_records(Array($id), $count, 0, "items." . $sort_page_field, $sort_page_direction, "OR", false));
    print $template;

    // Set breadcrumbs on the photo pages to point back to the calendar day view.
    item::set_display_context_callback("tag_albums_Controller::get_display_context", $id, $album_id);
  }

  public function album($id) {
    // Displays a dynamic page containing items that have been 
    //  tagged with one or more tags.

    // Load the specified ID to make sure it exists.
    $album_tags = ORM::factory("tags_album_id")
      ->where("id", "=", $id)
      ->find_all();

    // If it doesn't exist, redirect to the modules root page.
    if (count($album_tags) == 0) {
      url::redirect("tag_albums/");
    }

    // If it does exist, and is set to *, load a list of all tags.
    if ($album_tags[0]->tags == "*") {
      $this->index($id, "");
    } else {
      // Otherwise, populate this page with the specified items.

      // Inherit permissions, title and description from the album that linked to this page.
      $album = ORM::factory("item", $album_tags[0]->album_id);
      access::required("view", $album);
      $page_title = $album->title;
      $page_description = $album->description;

      // URL to this page
      $str_page_url = "tag_albums/album/" . $id . "/" . urlencode($album->name);

      // Determine page sort order.
      $sort_page_field = $album->sort_column;
      $sort_page_direction = $album->sort_order;

      // Determine search type (AND/OR) and generate an array of the tag ids.
      $tag_ids = Array();
      foreach (explode(",", $album_tags[0]->tags) as $tag_name) {
        $tag = ORM::factory("tag")->where("name", "=", trim($tag_name))->find();
        if ($tag->loaded()) {
          $tag_ids[] = $tag->id;
        }
      }
      $album_tags_search_type = $album_tags[0]->search_type;

      // Figure out how many items to display on each page.
      $page_size = module::get_var("gallery", "page_size", 9);

      // If this page was reached from a breadcrumb, figure out what page to load from the show id.
      $show = Input::instance()->get("show");
      if ($show) {
        $child = ORM::factory("item", $show);
        $index = $this->_get_position($child->$sort_page_field, $child->id, $tag_ids, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, true);
        if ($index) {
          $page = ceil($index / $page_size);
          if ($page == 1) {
            url::redirect($str_page_url);
          } else {
            url::redirect($str_page_url . "?page=$page");
          }
        }
      }

      // Figure out how many items are in this "virtual album"
      $count = $this->_count_records($tag_ids, $album_tags_search_type, true);

      // Figure out which page # the visitor is on and
      //   don't allow the visitor to go below page 1.
      $page = Input::instance()->get("page", 1);
      if ($page < 1) {
        url::redirect($str_page_url);
      }

      // First item to display.
      $offset = ($page - 1) * $page_size;

      // Figure out what the highest page number is.
      $max_pages = ceil($count / $page_size);

      // Don't let the visitor go past the last page.
      if ($max_pages && $page > $max_pages) {
        url::redirect($str_page_url . "/?page=$max_pages");
      }

      // Figure out which items to display on this page and store their details in $children.
      $tag_children = $this->_get_records($tag_ids, $page_size, $offset, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, true); 

      // Set up the previous and next page buttons.
      if ($page > 1) {
        $previous_page = $page - 1;
        $view->previous_page_link = url::site($str_page_url . "/?page={$previous_page}");
      }
      if ($page < $max_pages) {
        $next_page = $page + 1;
        $view->next_page_link = url::site($str_page_url . "/?page={$next_page}");
      }

      // Set up breadcrumbs.
      $tag_album_breadcrumbs = array();
      $counter = 0;
      $tag_album_breadcrumbs[] = Breadcrumb::instance($album->title, $album->url())->set_last();
      $parent_item = ORM::factory("item", $album->parent_id);
      while ($parent_item->id != 1) {
        $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url());
        $parent_item = ORM::factory("item", $parent_item->parent_id);
      }
      $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url())->set_first();
      $tag_album_breadcrumbs[1]->url .= "?show=" . $album->id;
	  
      $tag_album_breadcrumbs = array_reverse($tag_album_breadcrumbs, true);

      // Set up and display the actual page.
      $template = new Theme_View("page.html", "collection", "Tag Albums");
      $template->set_global(
        array("page" => $page,
              "max_pages" => $max_pages,
              "page_size" => $page_size,
              "children" => $tag_children,
              "breadcrumbs" => $tag_album_breadcrumbs,
              "children_count" => $count));
      $template->page_title = $page_title;
      $template->content = new View("dynamic.html");
      $template->content->title = $page_title;
      $template->content->description = $page_description;

      $template->set_global("all_siblings", $this->_get_records($tag_ids, $count, 0, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false));
      print $template;

    // Set breadcrumbs on the photo pages to point back to the calendar day view.
    item::set_display_context_callback("tag_albums_Controller::get_display_context", 0, $id);
    }
  }

  public function show($item_id, $tag_id, $album_id) {
    // This function used to be responsible for displaying photos.
    //   As of Gallery 3.0.3, it is no longer needed, now it just
    //   redirects to the photo's primary URL to avoid breaking older links.
    item::set_display_context_callback("tag_albums_Controller::get_display_context", $tag_id, $album_id);
    $item = ORM::factory("item", $item_id);
    url::redirect(url::abs_site("{$item->type}s/{$item->id}"));
  }

  public function make_tag_album_cover($id, $tag_id, $album_id) {
    if (!identity::active_user()->admin) {
      message::error(t("You do not have sufficient privileges to do this"));
      url::redirect("tag_albums/show/" . $id . "/" . $tag_id . "/" . $album_id . "/" . urlencode($item->name));
    }

    $item = ORM::factory("item", $id);

    if (($album_id > 0) && ($tag_id == 0)) {
      // If we are dealing with a dynamic album, set it's thumbnail to this pics.
      // Based on modules/gallery/helpers/item.php
      $album_tags = ORM::factory("tags_album_id")
        ->where("id", "=", $album_id)
        ->find_all();
      if (count($album_tags) > 0) {
        $parent = ORM::factory("item", $album_tags[0]->album_id);
        $parent->album_cover_item_id = $item->id;
        $parent->thumb_dirty = 1;
        graphics::generate($parent);
        $parent->save();

        $grand_parent = $parent->parent();
        if ($grand_parent && access::can("edit", $grand_parent) &&
            $grand_parent->album_cover_item_id == null)  {
          item::make_album_cover($parent);
        }
      }
      message::success(t("Made " . $item->title . " this album's cover"));
      url::redirect("tag_albums/show/" . $id . "/" . $tag_id . "/" . $album_id . "/" . urlencode($item->name));
    } else {
      // If setting a thumbnail for an auto-generated all tags->tag album.
      $record = ORM::factory("tags_album_tag_cover")->where("tag_id", "=", $tag_id)->find();
      if (!$record->loaded()) {
        $record->tag_id = $tag_id;
      }
      $record->photo_id = $id;
      $record->save();
      message::success(t("Made " . $item->title . " this album's cover"));
      url::redirect("tag_albums/show/" . $id . "/" . $tag_id . "/" . $album_id . "/" . urlencode($item->name));
    }
  }

  static function get_display_context($item, $tag_id, $album_id) {
    // Make sure #album_id is valid, clear it out if it isn't.
    // Note:  $dynamic_siblings is used exclusively for Grey Dragon.

    $album_tags = ORM::factory("tags_album_id")
      ->where("id", "=", $album_id)
      ->find_all();
    if (count($album_tags) == 0) {
      $album_id = 0;
    }

    // Load the tag and item, make sure the user has access to the item.
    $display_tag = ORM::factory("tag", $tag_id);

    // Figure out sort order from module preferences.
    $sort_page_field = "";
    $sort_page_direction = "";
    if (($tag_id > 0) || (count($album_tags) == 0)) {
      $sort_page_field = module::get_var("tag_albums", "subalbum_sort_by", "title");
      $sort_page_direction = module::get_var("tag_albums", "subalbum_sort_direction", "ASC");
    } else {
      $parent_album = ORM::factory("item", $album_tags[0]->album_id);
      $sort_page_field = $parent_album->sort_column;
      $sort_page_direction = $parent_album->sort_order;
    }

    // Load the number of items in the parent album, and determine previous and next items.
    $sibling_count = "";
    $tag_children = "";
    $previous_item = "";
    $next_item = "";
    $position = 0;
    $dynamic_siblings = "";
    $siblings_callback_param=array();
    if ($tag_id > 0) {	
      $album_tags_search_type = "";
      $sibling_count = tag_albums_Controller::_count_records(Array($tag_id), "OR", false);
      $position = tag_albums_Controller::_get_position($item->$sort_page_field, $item->id, Array($tag_id), "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
      if ($position > 1) {
        $previous_item_object = tag_albums_Controller::_get_records(Array($tag_id), 1, $position-2, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
        if (count($previous_item_object) > 0) {
          $previous_item = $previous_item_object[0];
        }
      }
      $next_item_object = tag_albums_Controller::_get_records(Array($tag_id), 1, $position, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
      if (count($next_item_object) > 0) {
        $next_item = $next_item_object[0];
      }
      $dynamic_siblings = tag_albums_Controller::_get_records(Array($tag_id), $sibling_count, 0, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
      $siblings_callback_param= array(Array($tag_id), $sibling_count, 0, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
    } else {
      $tag_ids = Array();
      foreach (explode(",", $album_tags[0]->tags) as $tag_name) {
        $tag = ORM::factory("tag")->where("name", "=", trim($tag_name))->find();
        if ($tag->loaded()) {
          $tag_ids[] = $tag->id;
        }
      }
      $album_tags_search_type = $album_tags[0]->search_type;
      $sibling_count = tag_albums_Controller::_count_records($tag_ids, $album_tags_search_type, false);
      $position = tag_albums_Controller::_get_position($item->$sort_page_field, $item->id, $tag_ids, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
      if ($position > 1) {
        $previous_item_object = tag_albums_Controller::_get_records($tag_ids, 1, $position-2, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
        if (count($previous_item_object) > 0) {
          $previous_item = $previous_item_object[0];
        }
      }
      $next_item_object = tag_albums_Controller::_get_records($tag_ids, 1, $position, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
      if (count($next_item_object) > 0) {
        $next_item = $next_item_object[0];
      }
      $dynamic_siblings = tag_albums_Controller::_get_records($tag_ids, $sibling_count, 0, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
      $siblings_callback_param= array($tag_ids, $sibling_count, 0, "items." . $sort_page_field, $sort_page_direction, $album_tags_search_type, false);
    }

    // Set up breadcrumbs
    $tag_album_breadcrumbs = Array();
    if ($album_id > 0) {
      $counter = 0;
      $tag_album_breadcrumbs[] = Breadcrumb::instance($item->title, $item->url())->set_last();
      if ($album_tags[0]->tags == "*") {
        $tag_album_breadcrumbs[] = Breadcrumb::instance($display_tag->name, url::site("tag_albums/tag/" . $display_tag->id . "/" . $album_id . "/" . urlencode($display_tag->name)));
      }
      $parent_item = ORM::factory("item", $album_tags[0]->album_id);
      $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, url::site("tag_albums/album/" . $album_id . "/" . urlencode($parent_item->name)));
      $parent_item = ORM::factory("item", $parent_item->parent_id);
      while ($parent_item->id != 1) {
        $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url());
        $parent_item = ORM::factory("item", $parent_item->parent_id);
      }
      $tag_album_breadcrumbs[] = Breadcrumb::instance($parent_item->title, $parent_item->url())->set_first();
      $tag_album_breadcrumbs[1]->url .= "?show=" . $item->id;
      $tag_album_breadcrumbs = array_reverse($tag_album_breadcrumbs, true);
    } else {
      $tag_album_breadcrumbs[] = Breadcrumb::instance(item::root()->title, item::root()->url())->set_first();
      $tag_album_breadcrumbs[] = Breadcrumb::instance(module::get_var("tag_albums", "tag_page_title", "All Tags"), url::site("tag_albums/"));
      $tag_album_breadcrumbs[] = Breadcrumb::instance($display_tag->name, url::site("tag_albums/tag/" . $display_tag->id . "/" . urlencode($display_tag->name)) . "?show=" . $item->id);
      $tag_album_breadcrumbs[] = Breadcrumb::instance($item->title, $item->url())->set_last();
    }

    return array("position" => $position,
                 "previous_item" => $previous_item,
                 "next_item" => $next_item,
                 "tag_id" => $tag_id,
                 "album_id" => $album_id,
                 "is_tagalbum_page" => true,
                 "dynamic_siblings" => $dynamic_siblings,
                 "sibling_count" => $sibling_count,
                 "siblings_callback" => array("tag_albums_Controller::get_siblings", $siblings_callback_param),
                 "breadcrumbs" => $tag_album_breadcrumbs);
  }

  private function _get_position($item_title, $item_id, $tag_ids, $sort_field, $sort_direction, $search_type, $include_albums) {
    // Determine an item's position within a virtual album.

    // Convert ASC/DESC to < or > characters.
    if (!strcasecmp($sort_direction, "DESC")) {
      $comp = ">";
    } else {
      $comp = "<";
    }

    // Figure out how many items are _before the current item.
    $items_model = ORM::factory("item");
    if ($search_type == "AND") {
      $items_model->select('COUNT("*") AS result_count');
    } else {
      $items_model->select("items.id");
    }
    $items_model->viewable();
    $items_model->join("items_tags", "items.id", "items_tags.item_id");		
    $items_model->open();
    $items_model->where("items_tags.tag_id", "=", $tag_ids[0]);
    $counter = 1;
    while ($counter < count($tag_ids)) {
      $items_model->or_where("items_tags.tag_id", "=", $tag_ids[$counter]);
      $counter++;
    }
    $items_model->close();
    if ($include_albums == false) {
      $items_model->and_where("items.type", "!=", "album");
    }
    $items_model->and_where($sort_field, $comp, $item_title);
    $items_model->order_by($sort_field, $sort_direction);
    $items_model->group_by("items.id");
    if ($search_type == "AND") {
      $items_model->having("result_count", "=", count($tag_ids));
    }
    $position = count($items_model->find_all());

    // In case multiple items have identical sort criteria, query for
    //  everything with the same criteria, and increment the position
    //  one at a time until we find the right item.	
    $items_model = ORM::factory("item");
    if ($search_type == "AND") {
      $items_model->select("items.id");
      $items_model->select('COUNT("*") AS result_count');
    } else {
      $items_model->select("items.id");
    }
    $items_model->viewable();
    $items_model->join("items_tags", "items.id", "items_tags.item_id");		
    $items_model->open();
    $items_model->where("items_tags.tag_id", "=", $tag_ids[0]);
    $counter = 1;
    while ($counter < count($tag_ids)) {
      $items_model->or_where("items_tags.tag_id", "=", $tag_ids[$counter]);
      $counter++;
    }
    $items_model->close();
    if ($include_albums == false) {
      $items_model->and_where("items.type", "!=", "album");
    }
    $items_model->and_where($sort_field, "=", $item_title);
    $items_model->order_by($sort_field, $sort_direction);
    $items_model->group_by("items.id");
    if ($search_type == "AND") {
      $items_model->having("result_count", "=", count($tag_ids));
    }
    $match_items = $items_model->find_all();
    foreach ($match_items as $one_item) {
      $position++;
      if ($one_item->id == $item_id) {
        break;
      }
    }

    return ($position);
  }

  public function get_siblings($tag_ids, $page_size, $offset, $sort_field, $sort_direction, $search_type, $include_albums) {
	  return tag_albums_Controller::_get_records($tag_ids, $page_size, $offset, $sort_field, $sort_direction, $search_type, $include_albums);
  } 

  private function _get_records($tag_ids, $page_size, $offset, $sort_field, $sort_direction, $search_type, $include_albums) {
    // Returns an array of items to be displayed on the current page.

    $items_model = ORM::factory("item");
    if ($search_type == "AND") {
      // For some reason, if I do 'select("*")' the item ids all have values that are 1000+
      //   higher then they should be.  So instead, I'm manually selecting each column that I need.
      $items_model->select("items.id");
      $items_model->select("items.name");
      $items_model->select("items.title");
      $items_model->select("items.view_count");
      $items_model->select("items.owner_id");
      $items_model->select("items.rand_key");
      $items_model->select("items.type");
      $items_model->select("items.thumb_width");
      $items_model->select("items.thumb_height");
      $items_model->select("items.left_ptr");
      $items_model->select("items.right_ptr");
      $items_model->select("items.relative_path_cache");
      $items_model->select("items.relative_url_cache");
      $items_model->select('COUNT("*") AS result_count');
    }
    $items_model->viewable();
    $items_model->join("items_tags", "items.id", "items_tags.item_id");		
    $items_model->open();
    $items_model->where("items_tags.tag_id", "=", $tag_ids[0]);
    $counter = 1;
    while ($counter < count($tag_ids)) {
      $items_model->or_where("items_tags.tag_id", "=", $tag_ids[$counter]);
      $counter++;
    }
    $items_model->close();
    if ($include_albums == false) {
      $items_model->and_where("items.type", "!=", "album");
    }
    $items_model->order_by($sort_field, $sort_direction);
    $items_model->group_by("items.id");
    if ($search_type == "AND") {
      $items_model->having("result_count", "=", count($tag_ids));
    }

    return $items_model->find_all($page_size, $offset);
  }

  private function _count_records($tag_ids, $search_type, $include_albums) {
    // Count the number of viewable items for the designated tag(s)
    //  and return that number.

    if (count($tag_ids) == 0) {
      // If no tags were specified, return 0.
      return 0;

    } elseif (count($tag_ids) == 1) {
      // if one tag was specified, we can use count_all to get the number.
      $count = ORM::factory("item")
               ->viewable()
               ->join("items_tags", "items.id", "items_tags.item_id")
               ->where("items_tags.tag_id", "=", $tag_ids[0]);
      if ($include_albums == false) {
        $count->and_where("items.type", "!=", "album");
      }
      return $count->count_all();

    } else {
      // If multiple tags were specified, count_all won't work,
      //   so we'll have to do count(find_all) instead.
      $items_model = ORM::factory("item");
      if ($search_type == "AND") {
        $items_model->select('COUNT("*") AS result_count');
      } else {
        $items_model->select('items.id');
      }
      $items_model->viewable();
      $items_model->join("items_tags", "items.id", "items_tags.item_id");		
      $items_model->where("items_tags.tag_id", "=", $tag_ids[0]);
      $counter = 1;
      while ($counter < count($tag_ids)) {
        $items_model->or_where("items_tags.tag_id", "=", $tag_ids[$counter]);
        $counter++;
      }
      if ($include_albums == false) {
        $items_model->and_where("items.type", "!=", "album");
      }
      $items_model->group_by("items.id");
      if ($search_type == "AND") {
        $items_model->having("result_count", "=", count($tag_ids));
      }

      return count($items_model->find_all());
    }
  }

  private function _get_filter_html($album_id, $str_filter) {
    // Generate HTML to display filter links on the index page.

    // Make sure $album_id is set.
    if ($album_id == "") {
      $album_id = 0;
    }

    // Generate the links.
    $str_html = "Filter: ";
    if ($str_filter != "") {
      if ($album_id > 0) {
        $album_tags = ORM::factory("tags_album_id")
          ->where("id", "=", $album_id)
          ->find_all();
        $album = ORM::factory("item", $album_tags[0]->album_id);
        $str_html .= "<a href=\"" . url::site("tag_albums/album/" . $album_id . "/" . urlencode($album->name)) . "\">(All)</a> ";
      } else {
        $str_html .= "<a href=\"" . url::site("tag_albums/") . "\">(All)</a> ";
      }
    }
    if ($str_filter == "NUM") {
      $str_html .= "# ";
    } else {
      $str_html .= "<a href=\"" . url::site("tag_albums/filter/" . $album_id . "/NUM") . "\">#</a> ";
    }
    foreach(range('A','Z') as $letter) {
      if ($letter == $str_filter) {
        $str_html .= $letter . " ";
      } else {
        $str_html .= "<a href=\"" . url::site("tag_albums/filter/" . $album_id . "/" . $letter) . "\">";
        $str_html .= $letter . "</a> ";
      }
    }

    // Return the HTML.
    return $str_html;
  }
}
