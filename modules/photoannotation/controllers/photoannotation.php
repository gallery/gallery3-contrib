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
  public function save($item_data) {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    //Get form data
    $id = $_POST["id"];   //Not yet needed since we are only creating new tagfaces will be needed when editing of existing ones is implemented
    $str_y1 = $_POST["top"];
    $str_x1 = $_POST["left"];
    $str_y2 = $_POST["height"] + $str_y1;  //Annotation uses area size, tagfaces uses positions
    $str_x2 = $_POST["width"] + $str_x1;  //Annotation uses area size, tagfaces uses positions
    $str_face_title = $_POST["text"];
    $tag_data = $_POST["tagsList"];
    $str_face_description = $_POST["desc"];
    $redir_uri = $_POST["currenturl"];
    // Decide if we are saving a face or a note.
    if ($tag_data == -1) {
      if ($str_face_title == "") {
        message::error(t("Please select a Tag or specify a Title."));
        url::redirect($redir_uri);
        return;
      }
      //Save note
      $newnote = ORM::factory("items_note");
      $newnote->item_id = $item_data;
      $newnote->x1 = $str_x1;
      $newnote->y1 = $str_y1;
      $newnote->x2 = $str_x2;
      $newnote->y2 = $str_y2;
      $newnote->title = $str_face_title;
      $newnote->description = $str_face_description;
      $newnote->save();
    } else {
      // Check to see if the tag already has a face associated with it.
      $existingFace = ORM::factory("items_face")
                           ->where("tag_id", "=", $tag_data)
                           ->where("item_id", "=", $item_data)
                           ->find_all();

      if (count($existingFace) == 0) {
        // Save the new face to the database.
        $newface = ORM::factory("items_face");
        $newface->tag_id = $tag_data;
        $newface->item_id = $item_data;
        $newface->x1 = $str_x1;
        $newface->y1 = $str_y1;
        $newface->x2 = $str_x2;
        $newface->y2 = $str_y2;
        $newface->description = $str_face_description;
        $newface->save();
      } else {
        // Update the coordinates of an existing face.
        $updatedFace = ORM::factory("items_face", $existingFace[0]->id);
        $updatedFace->x1 = $str_x1;
        $updatedFace->y1 = $str_y1;
        $updatedFace->x2 = $str_x2;
        $updatedFace->y2 = $str_y2;
        $updatedFace->description = $str_face_description;
        $updatedFace->save();
      }
    }
    message::success(t("Annotation saved."));
    url::redirect($redir_uri);
    return;
  }
  
  public function delete() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    //Get form data
    $noteid = $_POST["noteid"];
    $notetype = $_POST["notetype"];
    $redir_uri = $_POST["currenturl"];
    
    if ($noteid == "" || $notetype == "") {
      message::error(t("Please select a tag or note to delete."));
      url::redirect($redir_uri);
      return;
    }
    
    if ($notetype == "face") {
      db::build()->delete("items_faces")->where("id", "=", $noteid)->execute();    
      message::success(t("Annotation deleted."));
    } elseif ($notetype == "note") {
      db::build()->delete("items_notes")->where("id", "=", $noteid)->execute();    
      message::success(t("Annotation deleted."));
    } else {
      message::error(t("Please select a tag or note to delete."));
    }
    url::redirect($redir_uri);
  }
}
