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
class photoannotation_Controller extends Controller {
  public function showuser() {
    if (identity::active_user()->guest && !module::get_var("photoannotation", "allowguestsearch", false)) {
      message::error(t("You have to log in to perform a people search."));
      url::redirect(url::site());
      return;
    }
    $form = photoannotation::get_user_search_form("g-user-cloud-form");
    $user_id = Input::instance()->get("name", "");
    if ($user_id == "") {
      $user_id = Input::instance()->post("name", "");
    }
    $getuser = photoannotation::getuser($user_id);
    if ($getuser->found) {
      url::redirect(user_profile::url($getuser->user->id));
      return;
    }
    $page_size = module::get_var("gallery", "page_size", 9);
    $page = Input::instance()->get("page", 1);
    $offset = ($page - 1) * $page_size;

    // Make sure that the page references a valid offset
    if ($page < 1) {
      $page = 1;
    }
    list ($count, $result) = photoannotation::search_user($user_id, $page_size, $offset);
    $max_pages = max(ceil($count / $page_size), 1);
    if ($page > 1) {
      $previous_page_url = url::site("photoannotation/showuser?name=". $user_id ."&amp;page=". ($page - 1));
    }
    if ($page < $max_pages) {
      $next_page_url = url::site("photoannotation/showuser?name=". $user_id ."&amp;page=". ($page + 1));
    }
    if ($user_id == "") {
      $user_id = "*";
    }
    $template = new Theme_View("page.html", "other", "usersearch");
    $template->set_global("position", $page);
    $template->set_global("total", $max_pages);
    $template->content = new View("photoannotation_user_search.html");      
    $template->content->search_form = photoannotation::get_user_search_form(g-user-search-form); 
    $template->content->users = $result;
    $template->content->q = $user_id;
    $template->content->count = $count;
    $template->content->paginator = new View("paginator.html");      
    $template->content->paginator->previous_page_url = $previous_page_url;
    $template->content->paginator->next_page_url = $next_page_url;      
    print $template;
  }

  public function save($item_id) {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();
    //Get form data
    $item = ORM::factory("item", $item_id);
    $annotate_id = $_POST["noteid"];  
    $notetype = $_POST["notetype"]; 
    $str_y1 = $_POST["top"];
    $str_x1 = $_POST["left"];
    $str_y2 = $_POST["height"] + $str_y1;  //Annotation uses area size, tagfaces uses positions
    $str_x2 = $_POST["width"] + $str_x1;  //Annotation uses area size, tagfaces uses positions
    $item_title = $_POST["text"];
    $tag_data = $_POST["tagsList"];
    $user_id = "";
    $user_id = $_POST["userlist"];
    $description = $_POST["desc"];
    $redir_uri = url::abs_site("{$item->type}s/{$item->id}");
    //If this is a user then get the id
    if ($user_id != "") {
      $getuser = photoannotation::getuser($user_id);
      if (!$getuser->found) {
        message::error(t("Could not find anyone with the name %user.", array("user" => $user_id)));
        url::redirect($redir_uri);
        return;
      }
      if ($getuser->isguest) {
        message::error(t("You cannot create an annotation for the guest user."));
        url::redirect($redir_uri);
        return;
      }
      $user_id = $getuser->user->id;
    }
    
    //Add tag to item, create tag if not exists
    if ($tag_data != "") {
      $tag = ORM::factory("tag")->where("name", "=", $tag_data)->find();
      if (!$tag->loaded()) {
        $tag->name = $tag_data;
        $tag->count = 0;
      }
      $tag->add($item);
      $tag->count++;
      $tag->save();
      $tag_data = $tag->id;
    } else {
      $tag_data = "";
    }
    //Save annotation
    if ($annotate_id == "new") {   //This is a new annotation
      if ($user_id != "") {              //Save user
        photoannotation::saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
      } elseif ($tag_data != "") {         //Save face
         photoannotation::saveface($tag_data, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
      } elseif ($item_title != "") {   //Save note
        photoannotation::savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
      } else {                            //Something's wrong
        message::error(t("Please select a person or tag or specify a title."));
        url::redirect($redir_uri);
        return;
      }
    } else {    //This is an update to an existing annotation
      switch ($notetype) {
        case "user":   //the original annotation is a user
          $updateduser = ORM::factory("items_user")    //load the existing user
                            ->where("id", "=", $annotate_id)
                            ->find();
          if ($user_id != "") {              //Conversion user -> user
            photoannotation::saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
          } elseif ($tag_data != "") {         //Conversion user -> face
            photoannotation::saveface($tag_data, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updateduser->delete();   //delete old user
          } elseif ($item_title != "") {   //Conversion user -> note
            photoannotation::savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updateduser->delete();   //delete old user
          } else {                            //Somethings wrong
            message::error(t("Please select a person or tag or specify a title."));
            url::redirect($redir_uri);
            return;
          }
          break;
        case "face":   //the original annotation is a face
          $updatedface = ORM::factory("items_face")    //load the existing user
                            ->where("id", "=", $annotate_id)
                            ->find();
          if ($user_id != "") {              //Conversion face -> user
            photoannotation::saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updatedface->delete();   //delete old face
          } elseif ($tag_data != "") {         //Conversion face -> face
            photoannotation::saveface($tag_data, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id);
          } elseif ($item_title != "") {   //Conversion face -> note
            photoannotation::savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updatedface->delete();   //delete old face
          } else {                            //Somethings wrong
            message::error(t("Please select a person or tag or specify a title."));
            url::redirect($redir_uri);
            return;
          }
          break;
        case "note":   //the original annotation is a note
          $updatednote = ORM::factory("items_note")    //load the existing user
                            ->where("id", "=", $annotate_id)
                            ->find();
          if ($user_id != "") {              //Conversion note -> user
            photoannotation::saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updatednote->delete();   //delete old note
          } elseif ($tag_data != "") {         //Conversion note -> face
            photoannotation::saveface($tag_data, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updatednote->delete();   //delete old note
          } elseif ($item_title != "") {   //Conversion note -> note
            photoannotation::savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id);
          } else {                            //Somethings wrong
            message::error(t("Please select a person or tag or specify a title."));
            url::redirect($redir_uri);
            return;
          }
          break;
        default:
          message::error(t("Please select a person or tag or specify a title."));
          url::redirect($redir_uri);
          return;
      }
    }
    message::success(t("Annotation saved."));
    url::redirect($redir_uri);
    return;
  }
  
  public function delete($item_data) {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    //Get form data
    $item = ORM::factory("item", $item_data);
    $noteid = $_POST["noteid"];
    $notetype = $_POST["notetype"];
    $redir_uri = url::abs_site("{$item->type}s/{$item->id}");
    
    if ($noteid == "") {
      message::error(t("Please select a tag or note to delete."));
      url::redirect($redir_uri);
      return;
    }
    switch ($notetype) {
      case "user":
        db::build()->delete("items_users")->where("id", "=", $noteid)->execute();
        break;
      case "face":
        db::build()->delete("items_faces")->where("id", "=", $noteid)->execute();
        break;
      case "note":
        db::build()->delete("items_notes")->where("id", "=", $noteid)->execute();
        break;
      default:
        message::error(t("Please select a tag or note to delete."));
        url::redirect($redir_uri);
        return;
    }
    message::success(t("Annotation deleted."));
    url::redirect($redir_uri);
  }
  
  public function autocomplete() {
    if (!identity::active_user()->guest || module::get_var("photoannotation", "allowguestsearch", false)) {
      $users = array();
      $user_parts = explode(",", Input::instance()->get("q"));
      $limit = Input::instance()->get("limit");
      $user_part = ltrim(end($user_parts));
      $user_list = ORM::factory("user")
        ->where("name", "LIKE", "{$user_part}%")
        ->or_where("full_name", "LIKE", "{$user_part}%")
        ->order_by("full_name", "ASC")
        ->limit($limit)
        ->find_all();
      foreach ($user_list as $user) {
        if ($user->name != "guest") {
          $users[] = $user->display_name() ." (". $user->name .")";
        }
      }
      print implode("\n", $users);
    }
  }
}
