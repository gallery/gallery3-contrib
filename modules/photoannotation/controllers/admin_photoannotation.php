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
class Admin_Photoannotation_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }
  
  public function converter() {
    print $this->_get_converter_view();
  }
  
  public function tagsmaintanance($delete) {
    print $this->_get_tagsmaintanance_view($delete);
  }

  public function converthandler() {
    access::verify_csrf();
    $form = $this->_get_converter_form();
    if ($form->validate()) {
      //Load the source tag
      $sourcetag = ORM::factory("tag", $form->sourcetag->value);
      if (!$sourcetag->loaded()) {
        message::error(t("The specified tag could not be found"));
        url::redirect("admin/photoannotation/converter");
      }
      //Load the target user
      $targetuser = ORM::factory("user", $form->targetuser->value);
      if (!$targetuser->loaded()) {
        message::error(t("The specified user could not be found"));
        url::redirect("admin/photoannotation/converter");
      }
      //Load all existing tag annotations
      $tag_annotations = ORM::factory("items_face")->where("tag_id", "=", $sourcetag->id)->find_all();
      
      foreach ($tag_annotations as $tag_annotation) {
        //Check if there are already user annotations of the target user on photo
        $user_annotations = ORM::factory("items_user")
                              ->where("item_id", "=", $tag_annotation->item_id)
                              ->where("user_id", "=", $targetuser->id)
                              ->find_all();
        if (count($user_annotations) > 1) {
          //If there are more than one existing annotations, delete all and create a new one
          foreach ($user_annotations as $user_annotation) {
            $user_annotation->delete();
          }
          $target_annotation = ORM::factory("items_user");
        } elseif (count($user_annotations) == 1) {
          //If there is only one existing annotation, load it and update it
          $target_annotation = ORM::factory("items_user", $user_annotations[0]->id);
        } else {
          //If there are no existing annotations create one
          $target_annotation = ORM::factory("items_user");
        }
        //Save values from tag annotation to user annotation and save it
        $target_annotation->user_id = $targetuser->id;
        $target_annotation->item_id = $tag_annotation->item_id;
        $target_annotation->x1 = $tag_annotation->x1;
        $target_annotation->y1 = $tag_annotation->y1;
        $target_annotation->x2 = $tag_annotation->x2;
        $target_annotation->y2 = $tag_annotation->y2;
        $target_annotation->description = $tag_annotation->description;
        $target_annotation->save();
        //Delete the old annotation
        $tag_annotation->delete();
      }
      message::success(t("%count tag annotations (%tagname) have been converted to user annotations (%username)", array("count" => count($tag_annotations), "tagname" => $sourcetag->name, "username" => $targetuser->display_name())));
      url::redirect("admin/photoannotation/converter");
    }
    print $this->_get_converter_view($form);
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var(
        "photoannotation", "noborder", $form->hoverphoto->noborder->value, true);
      module::set_var(
        "photoannotation", "bordercolor", $form->hoverphoto->bordercolor->value);
      module::set_var(
        "photoannotation", "noclickablehover", $form->hoverclickable->noclickablehover->value, true);
      module::set_var(
        "photoannotation", "clickablehovercolor", $form->hoverclickable->clickablehovercolor->value);
      module::set_var(
        "photoannotation", "nohover", $form->hovernoclickable->nohover->value, true);
      module::set_var(
        "photoannotation", "hovercolor", $form->hovernoclickable->hovercolor->value);
      module::set_var(
        "photoannotation", "showusers", $form->legendsettings->showusers->value, true);
      module::set_var(
        "photoannotation", "showfaces", $form->legendsettings->showfaces->value, true);
      module::set_var(
        "photoannotation", "shownotes", $form->legendsettings->shownotes->value, true);
      module::set_var(
        "photoannotation", "fullname", $form->legendsettings->fullname->value, true);
      module::set_var(
        "photoannotation", "nonotifications", $form->notifications->nonotifications->value, true);
      module::set_var(
        "photoannotation", "notificationoptout", $form->notifications->notificationoptout->value, true);
      module::set_var(
        "photoannotation", "onuserdelete", $form->onuserdelete->onuserdelete->value);
      message::success(t("Your settings have been saved."));
      url::redirect("admin/photoannotation");
    }
    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->page_title = t("Photo annotation");
    $v->content = new View("admin_photoannotation.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_converter_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->page_title = t("Photo annotation converter");
    $v->content = new View("admin_photoannotation_converter.html");
    $v->content->form = empty($form) ? $this->_get_converter_form() : $form;
    return $v;
  }

  private function _get_tagsmaintanance_view($delete = false) {
    $tag_orpanes_count = 0;
    $user_orphanes_count = 0;
    $item_orphanes_count = 0;
    $tag_orpanes_deleted = 0;
    $user_orphanes_deleted = 0;
    $item_orphanes_deleted = 0;
    //check all tag annotations
    $tag_annotations = ORM::factory("items_face")->find_all();
    foreach ($tag_annotations as $tag_annotation) {
      $check_tag = ORM::factory("tag")->where("id", "=", $tag_annotation->tag_id)->find();
      if (!$check_tag->loaded()) {
        if ($delete) {
          $tag_annotation->delete();
          $tag_orpanes_deleted++;
        } else {
          $tag_orpanes_count++;
        }
      } else {
        $check_item = ORM::factory("item")->where("id", "=", $tag_annotation->item_id)->find();
        if (!$check_item->loaded()) {
          if ($delete) {
            $tag_annotation->delete();
            $item_orpanes_deleted++;
          } else {
            $item_orpanes_count++;
          }
        }
      }
    }
    //check all user annotations
    $user_annotations = ORM::factory("items_user")->find_all();
    foreach ($user_annotations as $user_annotation) {
      $check_user = ORM::factory("user")->where("id", "=", $user_annotation->user_id)->find();
      if (!$check_user->loaded()) {
        if ($delete) {
          $user_annotation->delete();
          $user_orpanes_deleted++;
        } else {
          $user_orphanes_count++;
        }
      } else {
        $check_item = ORM::factory("item")->where("id", "=", $user_annotation->item_id)->find();
        if (!$check_item->loaded()) {
          if ($delete) {
            $user_annotation->delete();
            $item_orpanes_deleted++;
          } else {
            $item_orpanes_count++;
          }
        }
      }
    }
    
    //check all user annotations
    $note_annotations = ORM::factory("items_note")->find_all();
    foreach ($note_annotations as $note_annotation) {
      $check_item = ORM::factory("item")->where("id", "=", $note_annotation->item_id)->find();
      if (!$check_item->loaded()) {
        if ($delete) {
          $note_annotation->delete();
          $item_orpanes_deleted++;
        } else {
          $item_orpanes_count++;
        }
      }
    }
    $v = new Admin_View("admin.html");
    $v->page_title = t("Photo annotation tags maintanance");
    $v->content = new View("admin_photoannotation_tagsmaintanance.html");
    $v->content->tag_orpanes_count = $tag_orpanes_count;
    $v->content->user_orphanes_count = $user_orphanes_count;
    $v->content->item_orphanes_count = $item_orphanes_count;
    $v->content->tag_orpanes_deleted =  $tag_orpanes_deleted;
    $v->content->user_orphanes_deleted = $user_orphanes_deleted;
    $v->content->item_orphanes_deleted = $item_orphanes_deleted;
    $v->content->dodeletion = $delete;
    return $v;
  }
  
  private function _get_converter_form() {
    //get all tags
    $tags = ORM::factory("tag")->order_by("name", "ASC")->find_all();
    foreach ($tags as $tag) {
      $tag_array[$tag->id] = $tag->name;
    }
    //get all users
    $users = ORM::factory("user")->order_by("name", "ASC")->find_all();
    foreach ($users as $user) {
      $user_array[$user->id] = $user->display_name();
    }
    $form = new Forge("admin/photoannotation/converthandler", "", "post", array("id" => "g-admin-form"));
    $form->dropdown("sourcetag")->label(t("Select tag"))
      ->options($tag_array);	
    $form->dropdown("targetuser")->label(t("Select user"))
      ->options($user_array);	
    $form->submit("submit")->value(t("Convert"));
    return $form;
  }

  private function _get_form() {
    $form = new Forge("admin/photoannotation/handler", "", "post", array("id" => "g-admin-form"));
    $group = $form->group("hoverphoto")->label(t("Hovering over the photo"));
    $group->checkbox("noborder")->label(t("Don't show borders."))
      ->checked(module::get_var("photoannotation", "noborder", false));	
    $group->input("bordercolor")->label(t('Border color'))
      ->value(module::get_var("photoannotation", "bordercolor", "000000"))
      ->rules("valid_alpha_numeric|length[6]");
    $group = $form->group("hoverclickable")->label(t("Hovering over a clickable annotation"));
    $group->checkbox("noclickablehover")->label(t("Don't show borders."))
      ->checked(module::get_var("photoannotation", "noclickablehover", false));	
    $group->input("clickablehovercolor")->label(t('Border color'))
      ->value(module::get_var("photoannotation", "clickablehovercolor", "00AD00"))
      ->rules("valid_alpha_numeric|length[6]");
    $group = $form->group("hovernoclickable")->label(t("Hovering over a non-clickable annotation"));
    $group->checkbox("nohover")->label(t("Don't show borders."))
      ->checked(module::get_var("photoannotation", "nohover", false));	
    $group->input("hovercolor")->label(t('Border color'))
      ->value(module::get_var("photoannotation", "hovercolor", "990000"))
      ->rules("valid_alpha_numeric|length[6]");
    $group = $form->group("legendsettings")->label(t("Legend settings"));
    $group->checkbox("showusers")->label(t("Show face annotation below photo."))
      ->checked(module::get_var("photoannotation", "showusers", false));	
    $group->checkbox("showfaces")->label(t("Show face annotation below photo."))
      ->checked(module::get_var("photoannotation", "showfaces", false));	
    $group->checkbox("shownotes")->label(t("Show note annotations below photo."))
      ->checked(module::get_var("photoannotation", "shownotes", false));	
    $group->checkbox("fullname")->label(t("Show full name of a user instead of the username on annotations (username will be dispayed for users without a full name)."))
      ->checked(module::get_var("photoannotation", "fullname", false));	
    $group = $form->group("notifications")->label(t("Notification settings"));
    $group->checkbox("nonotifications")->label(t("Disable user notifications."))
      ->checked(module::get_var("photoannotation", "nonotifications", false));	
    $group->checkbox("notificationoptout")->label(t("Notify users by default (only applies to new users and user who have not saved their profile after installing this module)."))
      ->checked(module::get_var("photoannotation", "notificationoptout", false));	
    $group = $form->group("onuserdelete")->label(t("Auto conversion settings"));
    $group->dropdown("onuserdelete")->label(t("When deleting a user do the following with all annotations associated with this user"))
      ->options(array("0" => t("Delete annotation"), "1" => t("Convert to tag annotation"), "2" =>  t("Convert to note annotation")))
      ->selected(module::get_var("photoannotation", "onuserdelete", "0"));	
    $form->submit("submit")->value(t("Save"));
    return $form;
  }
}
