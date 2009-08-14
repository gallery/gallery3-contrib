<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class tagfaces_Controller extends Controller {
  public function drawfaces($id) {
    // Generate the page that allows the user to draw boxes over a photo.
    
    // Make sure user has access to view and edit the photo.
    $item = ORM::factory("item", $id);
    access::required("view", $item);
    access::required("edit", $item);

    // Create the page.
    $template = new Theme_View("page.html", "drawfaces");
    $template->set_global("item_id", $id);
    $template->set_global("page_title", t("Draw Faces"));
    $template->set_global("page_type", "photoface");
    $template->content = new View("drawfaces.html");
    $template->content->title = t("Tag Faces");
    $template->content->form = $this->_get_faces_form($id);
    $template->content->delete_form = $this->_get_delfaces_form($id);

    // Display the page.
    print $template;
  }

  public function delface() {
    // Delete the specified face data from the photo.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();
    
    // Convert submitted data to local variables.
    $tag_data = Input::instance()->post("facesList");
    $item_data = Input::instance()->post("item_id");
    
    // If the user didn't select a tag, display and error and abort.
    if (count($tag_data) == 0) {
      message::error(t("Please select a tag."));
      url::redirect("tagfaces/drawfaces/$item_data");
      return;
    }

    // Delete the face(s) from the database.
    foreach ($tag_data as $one_tag) {
      ORM::factory("items_face")
        ->where("id", $one_tag)
        ->delete_all();
    }
    
    // Display a success message.
    if (count($tag_data) == 1) {
      message::success(t("One face deleted."));
    } else {
      message::success(count($tag_data) . t(" faces deleted."));
    }    
    url::redirect("tagfaces/drawfaces/$item_data");
  }
  
  public function saveface() {
    // Save the face coordinates to the specified tag.
    
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Convert submitted data to local variables.
    $tag_data = Input::instance()->post("tagsList");
    $item_data = Input::instance()->post("item_id");
    $str_x1 = Input::instance()->post("x");
    $str_y1 = Input::instance()->post("y");
    $str_x2 = Input::instance()->post("x2");
    $str_y2 = Input::instance()->post("y2");

    // If the user didn't select a tag, display and error and abort.
    if (count($tag_data) == 0) {
      message::error(t("Please select a tag."));
      url::redirect("tagfaces/drawfaces/$item_data");
      return;
    }

    // If the user didn't select a face, display an error and abort.
    if (($str_x1 == "") || ($str_x2 == "") || ($str_y1 == "") || ($str_y2 == "")) {
      message::error(t("Please select a face."));
      url::redirect("tagfaces/drawfaces/$item_data");
      return;
    }

    // Check to see if the tag already has a face associated with it.
    $existingFace = ORM::factory("items_face")
      ->where("tag_id", $tag_data[0])
      ->where("item_id", $item_data)
      ->find_all();

    if (count($existingFace) == 0) {
      // Save the new face to the database.
      $newface = ORM::factory("items_face");
      $newface->tag_id = $tag_data[0];
      $newface->item_id = $item_data;
      $newface->x1 = $str_x1;
      $newface->y1 = $str_y1;
      $newface->x2 = $str_x2;
      $newface->y2 = $str_y2;
      $newface->save();
    } else {
      // Update the coordinates of an existing face.
      $updatedFace = ORM::factory("items_face", $existingFace[0]->id);
      $updatedFace->x1 = $str_x1;
      $updatedFace->y1 = $str_y1;
      $updatedFace->x2 = $str_x2;
      $updatedFace->y2 = $str_y2;
      $updatedFace->save();
    }

    // Redirect back to the main screen and display a "success" message.
    message::success(t("Face saved."));
    url::redirect("tagfaces/drawfaces/$item_data");
  }

  private function _get_faces_form($id) {
    // Generate the form that allows the user to select a tag to
    //   save the face too.  Also displays the coordinates of the face
    //   and the "Save face" button.
    
    // Make a new Form.
    $form = new Forge("tagfaces/saveface", "", "post",
                      array("id" => "gTagFacesForm"));

    // Create an array of all the tags for the current item.
    $all_tags = ORM::factory("tag")
      ->join("items_tags", "tags.id", "items_tags.tag_id")
      ->where("items_tags.item_id", $id)
      ->find_all();

    // Generate an array of tags to use as checkboxes.
    $array_tags = "";
    foreach ($all_tags as $oneTag) {
      $array_tags[$oneTag->id] = array($oneTag->name, false);
    }

    // Make a checklist of tags on the form.
    $tags_group = $form->group("FaceTag")->label(t("Select a tag:"));
    $tags_group->checklist("tagsList")
               ->options($array_tags)
               ->label(t("Select one of the tags below to associate with the face:"));
    
    // Generate input boxes to hold the coordinates of the face.
    $coordinates_group = $form->group("FaceCoordinates")
                              ->label(t("Coordinates:"));
    $coordinates_group->input("x")
                      ->label(t("X1"));
    $coordinates_group->input("y")
                      ->label(t("Y1"));
    $coordinates_group->input("x2")
                      ->label(t("X2"));
    $coordinates_group->input("y2")
                      ->label(t("Y2"));

    // Add the id# of the photo and a save button to the form.
    $form->hidden("item_id")->value($id);
    $form->submit("SaveFace")->value(t("Save face"));

    // Return the newly generated form.
    return $form;
  }

  private function _get_delfaces_form($id) {
    // Generate a form to allow the user to remove face data
    //   from a photo.
    
    // Make a new Form.
    $form = new Forge("tagfaces/delface", "", "post",
                      array("id" => "gTagDelFacesForm"));

    // Create an array of all the tags that already have faces.
    $existing_faces = ORM::factory("items_face")
      ->where("item_id", $id)
      ->find_all();

    // turn the $existing_faces array into an array that can be used
    //   for a checklist.
    $array_faces = "";
    foreach ($existing_faces as $oneFace) {
      $array_faces[$oneFace->id] = array(ORM::factory("tag", 
                                     $oneFace->tag_id)->name, false);
    }

    // Add a checklist to the form.
    $tags_group = $form->group("ExistingFaces")
                       ->label(t("Tags with faces:"));
    $tags_group->checklist("facesList")
               ->options($array_faces)
               ->label(t("Select the tag(s) that correspond(s) to the face(s) you wish to delete:"));
    
    // Add the id# of the photo and a delete button to the form.
    $form->hidden("item_id")->value($id);
    $form->submit("DeleteFace")->value(t("Delete face(s)"));

    // Return the newly generated form.
    return $form;
  }
}
