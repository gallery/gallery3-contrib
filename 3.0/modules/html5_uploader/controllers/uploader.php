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
class Uploader_Controller extends Controller {
  public function index($id) {
    $album = ORM::factory("item", $id);
    access::required("view", $album);
    access::required("add", $album);
    if (!$album->is_album()) {
      $album = $album->parent();
    }

    print $this->_get_add_form($album);
  }

  public function add($id) {
    $album = ORM::factory("item", $id);
    access::required("view", $album);
    access::required("add", $album);
    access::verify_csrf();

    $form = $this->_get_add_form($album);
    if ($form->validate()) {
      batch::start();

      $count = 0;
      $added_a_movie = false;
      $added_a_photo = false;

      $files_list=$_FILES['files'];
      foreach (array_keys($files_list['name']) as $index) {
        try {
          $temp_filename = $files_list['tmp_name'][$index];
          $item = ORM::factory("item");
          $item->name = basename($files_list['name'][$index]);
          $item->title = item::convert_filename_to_title($item->name);
          $item->parent_id = $album->id;
          $item->set_data_file($temp_filename);

          $path_info = @pathinfo($item->name);
          if (array_key_exists("extension", $path_info) &&
              in_array(strtolower($path_info["extension"]), array("flv", "mp4", "m4v"))) {
            $item->type = "movie";
            $item->save();
            $added_a_movie = true;
            log::success("content", t("Added a movie"),
                         html::anchor("movies/$item->id", t("view movie")));
          } else {
            $item->type = "photo";
            $item->save();
            $added_a_photo = true;
            log::success("content", t("Added a photo"),
                         html::anchor("photos/$item->id", t("view photo")));
          }
          $count++;
          module::event("add_photos_form_completed", $item, $form);
        } catch (Exception $e) {
          // Lame error handling for now.  Just record the exception and move on
          Kohana_Log::add("error", $e->getMessage() . "\n" . $e->getTraceAsString());

          // Ugh.  I hate to use instanceof, But this beats catching the exception separately since
          // we mostly want to treat it the same way as all other exceptions
          if ($e instanceof ORM_Validation_Exception) {
            Kohana_Log::add("error", "Validation errors: " . print_r($e->validation->errors(), 1));
          }
        }

        if (file_exists($temp_filename)) {
          unlink($temp_filename);
        }
      }
      batch::stop();
      if ($count) {
        if ($added_a_photo && $added_a_movie) {
          message::success(t("Added %count photos and movies", array("count" => $count)));
        } else if ($added_a_photo) {
          message::success(t2("Added one photo", "Added %count photos", $count));
        } else {
          message::success(t2("Added one movie", "Added %count movies", $count));
        }
      }
      json::reply(array("result" => "success"));
    } else {
      json::reply(array("result" => "error", "html" => (string) $form));
    }

    // Override the application/json mime type.  The dialog based HTML uploader uses an iframe to
    // buffer the reply, and on some browsers (Firefox 3.6) it does not know what to do with the
    // JSON that it gets back so it puts up a dialog asking the user what to do with it.  So force
    // the encoding type back to HTML for the iframe.
    // See: http://jquery.malsup.com/form/#file-upload
    header("Content-Type: text/html; charset=" . Kohana::CHARSET);
  }

  private function _get_add_form($album) {
    $form = new Forge("uploader/add/{$album->id}", "", "post", array("id" => "g-add-photos-form"));
    $group = $form->group("add_photos")
      ->label(t("Add photos to %album_title", array("album_title" => html::purify($album->title))));
    $group->input("files[]")->type("file")->multiple();

    $form->input("FOO")->type("hidden")->label(sprintf("You may upload several files at once. Uploading pictures may take some time - please be patient. Max. upload size of all pictures: %.0f MB.", ini_get("upload_max_filesize")));

    module::event("add_photos_form", $album, $form);

    $group = $form->group("buttons")->label("");
    $group->submit("")->value(t("Upload"));

    return $form;
  }
}
