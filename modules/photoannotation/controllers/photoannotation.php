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
    $user_id = $_POST["userlist"];
    $description = $_POST["desc"];
    $redir_uri = url::abs_site("{$item->type}s/{$item->id}");
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
      $tag_data = -1;
    }
    //Save annotation
    if ($annotate_id == "new") {   //This is a new annotation
      if ($user_id > -1) {              //Save user
        $this->_saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
      } elseif ($tag_data > -1) {         //Conversion user -> face
        $this->_saveface($tag_data, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
      } elseif ($item_title != "") {   //Conversion user -> note
        $this->_savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
      } else {                            //Somethings wrong
        message::error(t("Please select a User or Tag or specify a Title."));
        url::redirect($redir_uri);
        return;
      }
    } else {    //This is an update to an existing annotation
      switch ($notetype) {
        case "user":   //the original annotation is a user
          $updateduser = ORM::factory("items_user")    //load the existing user
                            ->where("id", "=", $annotate_id)
                            ->find();
          if ($user_id > -1) {              //Conversion user -> user
            $this->_saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id);
          } elseif ($tag_data > -1) {         //Conversion user -> face
            $this->_saveface($tag_data, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updateduser->delete();   //delete old user
          } elseif ($item_title != "") {   //Conversion user -> note
            $this->_savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updateduser->delete();   //delete old user
          } else {                            //Somethings wrong
            message::error(t("Please select a User or Tag or specify a Title."));
            url::redirect($redir_uri);
            return;
          }
          break;
        case "face":   //the original annotation is a face
          $updatedface = ORM::factory("items_face")    //load the existing user
                            ->where("id", "=", $annotate_id)
                            ->find();
          if ($user_id > -1) {              //Conversion face -> user
            $this->_saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updatedface->delete();   //delete old face
          } elseif ($tag_data > -1) {         //Conversion face -> face
            $this->_saveface($tag_data, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id);
          } elseif ($item_title != "") {   //Conversion face -> note
            $this->_savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updatedface->delete();   //delete old face
          } else {                            //Somethings wrong
            message::error(t("Please select a User or Tag or specify a Title."));
            url::redirect($redir_uri);
            return;
          }
          break;
        case "note":   //the original annotation is a note
          $updatednote = ORM::factory("items_note")    //load the existing user
                            ->where("id", "=", $annotate_id)
                            ->find();
          if ($user_id > -1) {              //Conversion note -> user
            $this->_saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updatednote->delete();   //delete old note
          } elseif ($tag_data > -1) {         //Conversion note -> face
            $this->_saveface($tag_data, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description);
            $updatednote->delete();   //delete old note
          } elseif ($item_title != "") {   //Conversion note -> note
            $this->_savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id);
          } else {                            //Somethings wrong
            message::error(t("Please select a User or Tag or specify a Title."));
            url::redirect($redir_uri);
            return;
          }
          break;
        default:
          message::error(t("Please select a User or Tag or specify a Title."));
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

  private function _saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id = "") {
    if ($annotate_id == "") {
      $item_user = ORM::factory("items_user");
    } else {
      $item_user = ORM::factory("items_user")
                      ->where("id", "=", $annotate_id)
                      ->find();
    }
    $item_user->user_id = $user_id;
    $item_user->item_id = $item_id;
    $item_user->x1 = $str_x1;
    $item_user->y1 = $str_y1;
    $item_user->x2 = $str_x2;
    $item_user->y2 = $str_y2;
    $item_user->description = $description;
    $item_user->save();
  }
  
  private function _saveface($tag_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id = "") {
    if ($annotate_id == "") {
      $item_face = ORM::factory("items_face");
    } else {
      $item_face = ORM::factory("items_face")
                      ->where("id", "=", $annotate_id)
                      ->find();
    }
    $item_face->tag_id = $tag_id;
    $item_face->item_id = $item_id;
    $item_face->x1 = $str_x1;
    $item_face->y1 = $str_y1;
    $item_face->x2 = $str_x2;
    $item_face->y2 = $str_y2;
    $item_face->description = $description;
    $item_face->save();
  }

  private function _savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id = "") {
    if ($annotate_id == "") {
      $item_note = ORM::factory("items_note");
    } else {
      $item_note = ORM::factory("items_note")
                      ->where("id", "=", $annotate_id)
                      ->find();
    }
    $item_note->item_id = $item_id;
    $item_note->x1 = $str_x1;
    $item_note->y1 = $str_y1;
    $item_note->x2 = $str_x2;
    $item_note->y2 = $str_y2;
    $item_note->title = $item_title;
    $item_note->description = $description;
    $item_note->save();
  }
}