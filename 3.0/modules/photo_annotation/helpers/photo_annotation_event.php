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
class photoannotation_event_Core {
  static function module_change($changes) {
    // See if the Tags module is installed,
    //   tell the user to install it if it isn't.
    if (!module::is_active("tag") || in_array("tag", $changes->deactivate)) {
      site_status::warning(
        t("The Photo Annotation module requires the Tags module.  " .
          "<a href=\"%url\">Activate the Tags module now</a>",
          array("url" => url::site("admin/modules"))),
        "photoannotation_needs_tag");
    } else {
      site_status::clear("photoannotation_needs_tag");
    }
    if (module::is_active("tagfaces") || in_array("tagfaces", $changes->activate)) {
      site_status::warning(
        t("The Photo Annotation module cannot be used together with the TagFaces module.  " .
          "<a href=\"%url\">Dectivate the TagFaces module now</a>",
          array("url" => url::site("admin/modules"))),
        "photoannotation_incompatibility_tagfaces");
    } else {
      site_status::clear("photoannotation_incompatibility_tagfaces");
    }
  }

  static function site_menu($menu, $theme) {
    // Create a menu option for adding face data.
    if (!$theme->item()) {
      return;
    }
    $item = $theme->item();
    if ($item->is_photo()) {
      if ((access::can("view", $item)) && (access::can("edit", $item))) {
        $menu->get("options_menu")
             ->append(Menu::factory("link")
             ->id("photoannotation")
             ->label(t("Add annotation"))
             ->css_id("g-photoannotation-link")
             ->url("#"));
      }
    }
  }

  static function item_deleted($item) {
    // Check for and delete existing Faces and Notes.
    $existingFaces = ORM::factory("items_face")
                          ->where("item_id", "=", $item->id)
                          ->find_all();
    if (count($existingFaces) > 0) {
      db::build()->delete("items_faces")->where("item_id", "=", $item->id)->execute();
    }

    $existingNotes = ORM::factory("items_note")
                          ->where("item_id", "=", $item->id)
                          ->find_all();
    if (count($existingNotes) > 0) {
      db::build()->delete("items_notes")->where("item_id", "=", $item->id)->execute();
    }

    $existingUsers = ORM::factory("items_user")
                          ->where("item_id", "=", $item->id)
                          ->find_all();
    if (count($existingUsers) > 0) {
      db::build()->delete("items_users")->where("item_id", "=", $item->id)->execute();
    }
  }

  static function user_deleted($old) {
    // Check for and delete existing Annotations linked to that user.
    $existingFaces = ORM::factory("items_user")
                          ->where("user_id", "=", $old->id)
                          ->find_all();
    if (count($existingFaces) > 0) {
      $onuserdelete = module::get_var("photoannotation", "onuserdelete", "0");
      if (module::get_var("photoannotation", "fullname", false)) {
        $new_tag_name = $old->display_name();
      } else {
        $new_tag_name = $old->name;
      }
      switch ($onuserdelete) {
        case "1":
          //convert to tag
          $tag = ORM::factory("tag")->where("name", "=", $new_tag_name)->find();
          if (!$tag->loaded()) {
            $tag->name = $new_tag_name;
            $tag->count = 0;
          }
          foreach ($existingFaces as $existingFace) {
            $item = ORM::factory("item")->where("id", "=", $existingFace->item_id)->find();
            $tag->add($item);
            $tag->count++;
            $tag->save();
            photoannotation::saveface($tag->id, $existingFace->item_id, $existingFace->x1, $existingFace->y1, $existingFace->x2, $existingFace->y2, $existingFace->description);
          }
          break;
        case "2":
          //convert to note
          foreach ($existingFaces as $existingFace) {
            photoannotation::savenote($new_tag_name, $existingFace->item_id, $existingFace->x1, $existingFace->y1, $existingFace->x2, $existingFace->y2, $existingFace->description);
          }
      }
      db::build()->delete("items_users")->where("user_id", "=", $old->id)->execute();
    }
    // Delete notification settings
    $notification_settings = ORM::factory("photoannotation_notification")
                          ->where("user_id", "=", $old->id)
                          ->find();
    if ($notification_settings->loaded()) {
      $notification_settings->delete();
    }
  }

  static function user_created($user) {
    // Write notification settings
    $notify = module::get_var("photoannotation", "notificationoptout", false);
    $notification_settings = ORM::factory("photoannotation_notification");
    $notification_settings->user_id = $user->id;
    $notification_settings->newtag = $notify;
    $notification_settings->comment = $notify;
    $notification_settings->save();
  }
  
  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("photoannotation_menu")
               ->label(t("Photo Annotation"))
               ->url(url::site("admin/photoannotation")));
  }
  
  static function show_user_profile($data) {
    $view = new Theme_View("dynamic.html", "collection", "userprofiles");
    //load thumbs
    $item_users = ORM::factory("items_user")->where("user_id", "=", $data->user->id)->find_all();
    foreach ($item_users as $item_user) {
      $item_thumb = ORM::factory("item")
          ->viewable()
          ->where("type", "!=", "album")
          ->where("id", "=", $item_user->item_id)
          ->find();
      if ($item_thumb->loaded()) {
        $item_thumbs[] = $item_thumb;
      }
    }
    $children_count = count($item_thumbs);
    $page_size = module::get_var("gallery", "page_size", 9);
    $page = (int) Input::instance()->get("page", "1");
    $offset = ($page-1) * $page_size;
    $max_pages = max(ceil($children_count / $page_size), 1);
    
    // Make sure that the page references a valid offset
    if ($page < 1) {
      url::redirect($album->abs_url());
    } else if ($page > $max_pages) {
      url::redirect($album->abs_url("page=$max_pages"));
    }
    if ($page < $max_pages) {
      $next_page_url = url::site("user_profile/show/". $data->user->id ."?page=". ($page + 1));
      $view->set_global("next_page_url", $next_page_url);
      $view->set_global("first_page_url", url::site("user_profile/show/". $data->user->id ."?page=". $max_pages));
    }
    
    if ($page > 1) {
      $view->set_global("previous_page_url", url::site("user_profile/show/". $data->user->id ."?page=". ($page - 1)));
      $view->set_global("first_page_url", url::site("user_profile/show/". $data->user->id ."?page=1"));
    }
    $view->set_global("page", $page);
    $view->set_global("max_pages", $max_pages);
    $view->set_global("page_size", $page_size);
    $view->set_global("children", array_slice($item_thumbs, $offset, $page_size));;
    $view->set_global("children_count", $children_count);
    $view->set_global("total", $max_pages);
    $view->set_global("position", t("Page") ." ". $page);
    if ($children_count > 0) {
      $data->content[] = (object)array("title" => t("Photos"), "view" => $view);
    }
  }

  static function user_edit_form($user, $form) {
    // Allow users to modify notification settings.
    if (!module::get_var("photoannotation", "nonotifications", false)) {
      $notification_settings = photoannotation::get_user_notification_settings($user);
      $user_notification = $form->edit_user->group("edit_notification")->label("Tag notifications");
      $user_notification->checkbox("photoannotation_newtag")->label(t("Notify me when a tag is added to a photo with me"))
           ->checked($notification_settings->newtag);
      $user_notification->checkbox("photoannotation_comment")->label(t("Notify me if someone comments on a photo with me on it"))
           ->checked($notification_settings->comment);
    }
  }

  static function user_edit_form_completed($user, $form) {
    // Save notification settings.
    if (!module::get_var("photoannotation", "nonotifications", false)) {
      $notification_settings = ORM::factory("photoannotation_notification")->where("user_id", "=", $user->id)->find();
      if (!$notification_settings->loaded()) {
        $notification_settings = ORM::factory("photoannotation_notification");
        $notification_settings->user_id = $user->id;
      }
      $notification_settings->newtag = $form->edit_user->edit_notification->photoannotation_newtag->value;
      $notification_settings->comment = $form->edit_user->edit_notification->photoannotation_comment->value;
      $notification_settings->save();
    }
  }

  static function comment_created($comment) {
    //Check if there are any user annotations on the photo and send notification if applicable
    $item_users = ORM::factory("items_user")->where("item_id", "=", $comment->item_id)->find_all();
    if (count($item_users) > 0) {
      foreach ($item_users as $item_user) {
        //Don't send if the commenter is the user to be notified
        if ($comment->author_id != $item_user->user_id && module::is_active("notification")) {
          photoannotation::send_notifications($item_user->user_id, $comment->item_id, "newcomment");
        }
      }
    }
  }

  static function comment_updated($comment) {
    //Check if there are any user annotations on the photo and send notification if applicable
    $item_users = ORM::factory("items_user")->where("item_id", "=", $comment->item_id)->find_all();
    if (count($item_users) > 0) {
      foreach ($item_users as $item_user) {
        //Don't send if the commenter is the user to be notified
        if ($comment->author_id != $item_user->user_id && module::is_active("notification")) {
          photoannotation::send_notifications($item_user->user_id, $comment->item_id, "updatedcomment");
        }
      }
    }
  }
}
