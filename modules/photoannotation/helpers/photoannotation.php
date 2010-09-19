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
class photoannotation_Core {
  static function search_user($q, $page_size, $offset) {
    $db = Database::instance();
    $q = trim($q, "*");
    $q = $db->escape($q) ."*";
    if ($q == "*") {
      $users = ORM::factory("user");
      $count = $users->count_all();
      $data = $users->order_by("name", "ASC")->find_all($page_size, $offset);
      return array($count, $data);
    } else {
      $query =
        "SELECT SQL_CALC_FOUND_ROWS {users}.*, " .
        "  MATCH({users}.`name`) AGAINST ('$q' IN BOOLEAN MODE) AS `score` " .
        "FROM {users} " .
        "WHERE MATCH({users}.`name`, {users}.`full_name`) AGAINST ('$q' IN BOOLEAN MODE) " .
        "ORDER BY `score` DESC " .
        "LIMIT $page_size OFFSET $offset";
      $data = $db->query($query);
      $count = $db->query("SELECT FOUND_ROWS() as c")->current()->c;
      return array($count, new ORM_Iterator(ORM::factory("user"), $data));
    }
  }

  static function get_user_search_form($form_id) {
    $form = new Forge("photoannotation/showuser", "", "post", array("id" => $form_id, "class" => "g-short-form"));
    $label = t("Search for a person");

    $group = $form->group("showuser")->label("Search for a person");
    $group->input("name")->label($label)->id("name");
    $group->submit("")->value(t("Search"));
    return $form;
  }

  public static function getuser($user_string) {
    $user_parts = explode("(", $user_string);
    $user_part = rtrim(ltrim(end($user_parts)), ")");
    $user = ORM::factory("user")->where("name", "=", $user_part)->find();
    $user_firstpart = trim(implode(array_slice($user_parts, 0, count($user_parts)-1)));
    if (!$user->loaded() || strcasecmp($user_firstpart, $user->display_name()) <> 0) {
      $result->found = false;
      $result->isguest = false;
      $result->user = "";
      return $result;
    }
    if (identity::guest()->id == $user->id) {
      $result->found = true;
      $result->isguest = true;
      $result->user = "";
      return $result;
    }
    $result->found = true;
    $result->isguest = false;
    $result->user = $user;
    return $result;
  }

  public static function saveuser($user_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description) {
    //Since we are associating a user we will remove any old annotation of this user on this photo
    $item_old_users = ORM::factory("items_user")
                    ->where("user_id", "=", $user_id)
                    ->where("item_id", "=", $item_id)
                    ->find_all();
    if (count($item_old_users) > 1) {
      foreach ($item_old_users as $item_old_user) {
        $item_old_user->delete();
      }
      $item_user = ORM::factory("items_user");
    } elseif (count($item_old_users) == 1) {
      $item_user = ORM::factory("items_user", $item_old_users[0]->id);
    } else {
      $item_user = ORM::factory("items_user");
      photoannotation::send_notifications($user_id, $item_id, "newtag");
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
  
  public static function saveface($tag_id, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id = "") {
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

  public static function savenote($item_title, $item_id, $str_x1, $str_y1, $str_x2, $str_y2, $description, $annotate_id = "") {
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

  public static function send_notifications($recipient_id, $item_id, $mailtype) {
    //Load the item
    $item = ORM::factory("item")->where("id", "=", $item_id)->find();
    if (!$item->loaded()) {
      return false;
    }
    //Load the user
    $recipient = ORM::factory("user")->where("id", "=", $recipient_id)->find();
    if (!$recipient->loaded()) {
      return false;
    }
    //Only send mail if the notifications are switched on globally
    if (!module::get_var("photoannotation", "nonotifications", false)) {
      //Check if the use has a valid e-mail
      if (!valid::email($recipient->email)) {
        return false;
      }
      //Get the users settings
      $notification_settings = self::get_user_notification_settings($recipient);
      //Check which type of mail to send
      switch ($mailtype) {
        case "newtag":
          //Only send if user has this option enabled
          if ($notification_settings->newtag) {
            //Get subject and body and send the mail
            $subject = module::get_var("photoannotation", "newtagsubject", "Someone tagged a photo of you");
            $body = module::get_var("photoannotation", "newtagbody", "Hello %name, please visit %url to view the photo.");
            $body = str_ireplace(array("%url", "%name"), array($item->abs_url(), $recipient->display_name()), $body);
            return self::_send_mail($recipient->email, $subject, $body);
          }
          break;
        case "newcomment":
          //Only send if user has this option enabled
          if ($notification_settings->comment) {
            //Don't send if the notification module is active and the user is watching this item
            if (module::is_active("notification")) {
              if (notification::is_watching($item, $recipient)) {
                return false;
              }
            }
            //Get subject and body and send the mail
            $subject = module::get_var("photoannotation", "newcommentsubject", "Someone added a comment to photo of you");
            $body = module::get_var("photoannotation", "newcommentbody", "Hello %name, please visit %url to read the comment.");
            $body = str_ireplace(array("%url", "%name"), array($item->abs_url(), $recipient->display_name()), $body);
            return self::_send_mail($recipient->email, $subject, $body);
          }
          break;
        case "updatecomment":
          //Only send if user has this option enabled
          if ($notification_settings->comment) {
            //Don't send if the notification module is active and the user is watching this item
            if (module::is_active("notification")) {
              if (notification::is_watching($item, $recipient)) {
                return false;
              }
            }
            //Get subject and body and send the mail
            $subject = module::get_var("photoannotation", "updatedcommentsubject", "Someone updated a comment to photo of you");
            $body = module::get_var("photoannotation", "updatedcommentbody", "Hello %name, please visit %url to read the comment.");
            $body = str_ireplace(array("%url", "%name"), array($item->abs_url(), $recipient->display_name()), $body);
            return self::_send_mail($recipient->email, $subject, $body);
          }
      }
    }
    return false;
  }
  
  private static function _send_mail($mailto, $subject, $message) {
    //Send the notification mail
    $message = nl2br($message);
    return Sendmail::factory()
      ->to($mailto)
      ->subject($subject)
      ->header("Mime-Version", "1.0")
      ->header("Content-type", "text/html; charset=utf-8")
      ->message($message)
      ->send();
  }
  
  public static function get_user_notification_settings($user) {
    //Try loading the notification settings of user
    $notification_settings = ORM::factory("photoannotation_notification")->where("user_id", "=", $user->id)->find();
    if (!$notification_settings->loaded()) {
      //If the user did not save his settings use the website default
      $notify = module::get_var("photoannotation", "notificationoptout", false);
      $notification_settings = ORM::factory("photoannotation_notification");
      $notification_settings->user_id = $user->id;
      $notification_settings->newtag = $notify;
      $notification_settings->comment = $notify;
      $notification_settings->save();
    }
    return $notification_settings;
  }
  
  static function cloud($count) {
    $users = ORM::factory("user")->order_by("name", "ASC")->find_all();
    if ($users) {
      $cloud = new View("photoannotation_cloud.html");
      $fullname = module::get_var("photoannotation", "fullname", false);
      foreach ($users as $user) {
        $annotations = ORM::factory("items_user")->where("user_id", "=", $user->id)->count_all();
        if ($annotations > 0) {
          if ($annotations > $maxcount) {
            $maxcount = $annotations;
          }
          if ($fullname) {
            $user_array[$user->name]->name = $user->display_name();
          } else {
            $user_array[$user->name]->name = $user->name;
          }
          $user_array[$user->name]->size = $annotations;
          $user_array[$user->name]->url = user_profile::url($user->id);
        }
      }
      if (isset($user_array)) {
        $cloud->users = array_slice($user_array, 0, $count);
        $cloud->max_count = $maxcount;
        return $cloud;
      } else {
        return "";
      }
    }
  }

  static function comment_count($user_id) {
    if (module::is_active("comment")) {
      return ORM::factory("comment")->where("author_id", "=", $user_id)->count_all();
    } else {
      return false;
    }
  }
  
  static function annotation_count($user_id) {
    return ORM::factory("items_user")->where("user_id", "=", $user_id)->count_all();
  }
}
