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
class Embedded_videos_Controller extends Controller {
  public function create($id) {
    $album = ORM::factory("item", $id);
    access::required("view", $album);
    access::required("add", $album);
    access::verify_csrf();
    $form = embed_videos::get_add_form($album);
    $temp_filename = "";
    
    // TODO: Add admin page options for these.
    $maxwidth = 650;
    $maxheight = 480;

    // Yes, this is a mess.
    $youtubeUrlPattern="youtube";
    $youtubeThumbnailUrl="http://img.youtube.com/vi/";
    $vimeoUrlPattern="vimeo.com";
    // End mess

    batch::start();
    try {
      $valid = $form->validate();
      if ($form->add_embedded_video->inputs['video_url']->value != "") {
        $title = $form->add_embedded_video->inputs['title']->value;
        $description = $form->add_embedded_video->inputs['description']->value;
        $valid_url=false;
        $embedded_video = ORM::factory("embedded_video");
        $item = ORM::factory("item");
        $item->type = "photo";
        $url = $form->add_embedded_video->inputs['video_url']->value;
        if(preg_match("/$youtubeUrlPattern/",$url)) {
          $video_id = 0;
          if (preg_match("/watch\?v=(.*?)(&\S+=\S+)/",$url,$matches)) {
            $video_id = $matches[1];
          } else if (preg_match("/watch\?v=(.*)/",$url,$matches)) {
            $video_id = $matches[1];
          } else if (preg_match("/v\/(.*)/",$url,$matches)) {
            $video_id = $matches[1];
          }
          if ($video_id) {
            $video_id = $matches[1];
            $embedded_video->embed_code = '<iframe class="youtube-player" type="text/html" width="'. $maxwidth .'" height="'. $maxheight .'" src="http://www.youtube.com/embed/' . $video_id . '" frameborder="0"></iframe>';
            $embedded_video->source = "YouTube";
            $content = file_get_contents("http://img.youtube.com/vi/" . $video_id . "/0.jpg");
            $itemname = "youtube_" . $video_id . ".jpg";
            $temp_filename = VARPATH . "tmp/$itemname";
            if ($content) {
              $valid_url = true;
              $sxml = simplexml_load_file("http://gdata.youtube.com/feeds/api/videos/$video_id");
              if ($sxml) {
                if ($title == '') {
                  $title = (string)$sxml->title;
                }
                if ($description == '') {
                  $description = (string)$sxml->content;
                }
              }
            }
          }
        } else if(preg_match("/$vimeoUrlPattern/",$url)) {
          if(preg_match("/$vimeoUrlPattern\/(.*)/",$url,$matches)) {
            $video_id = $matches[1];
            if ($video_id) {
              $sxml = simplexml_load_file("http://vimeo.com/api/v2/video/$video_id.xml");
              if ($sxml) {
                if ($title == '') {
                  $title = (string)$sxml->video->title;
                }
                if ($description == '') {
                  $description = strip_tags((string)$sxml->video->description);
                }
                $embedded_video->source = "Vimeo";
                $content = file_get_contents((string)$sxml->video->thumbnail_large);
                $itemname = "vimeo_" . $video_id . ".jpg";
                $temp_filename = VARPATH . "tmp/$itemname";
                $width = min((int)$sxml->video->width, $maxwidth);
                $height = min((int)$sxml->video->height, $maxheight);
                $embedded_video->embed_code = '<iframe src="http://player.vimeo.com/video/' . $video_id . '" width="'. (string)$width .'" height="'. (string)$height .'" frameborder="0"></iframe>';
                $valid_url = true;
              }
            }
          }
        }
        //$item->validate();
        //$content = file_get_contents("http://img.youtube.com/vi/" . $form->add_embedded_video->inputs['name']->value . "/0.jpg");
        if ($valid_url) {
          $file = fopen($temp_filename, "wb");
          fwrite($file, $content);
          fclose($file);
          gallery_graphics::composite($temp_filename, $temp_filename, array("file" => "modules/embed_videos/images/embed_video_icon.png", "position" => "center", "transparency" => 95));
          $item->set_data_file($temp_filename);
          $item->name = basename($itemname);
          $item->title = $title;
          $item->parent_id = $album->id;
          $item->description = $description;
          $item->slug = $form->add_embedded_video->inputs['slug']->value;
          $path_info = @pathinfo($temp_filename);
          $item->save();
          $embedded_video->item_id = $item->id;
          $embedded_video->validate();
          $embedded_video->save();
          log::success("content", t("Added a embedded video"), html::anchor("embeds/$item->id", t("view video")));
          module::event("add_event_form_completed", $item, $form);
        } else {
          $form->add_embedded_video->inputs['video_url']->add_error('invalid_id', 1);
          $valid = false;
        }
      } else {
        $form->add_embedded_video->inputs['video_url']->add_error('invalid_id', 1);
        $valid = false;
      }
    }
    catch(Exception $e) {
      // Lame error handling for now. Just record the exception and move on
      Kohana_Log::add("error", $e->getMessage() . "\n" . $e->getTraceAsString());
      // Ugh. I hate to use instanceof, But this beats catching the exception separately since
      // we mostly want to treat it the same way as all other exceptions
      if ($e instanceof ORM_Validation_Exception) {
        Kohana_Log::add("error", "Validation errors: " . print_r($e->validation->errors(), 1));
        foreach($e->validation->errors() as $key => $error) {
          $form->add_embed->inputs[$key]->add_error($error, 1);
        }
        $valid = false;
      }
      if (file_exists($temp_filename)) {
        unlink($temp_filename);
      }
    }
    if (file_exists($temp_filename)) {
      unlink($temp_filename);
    }
    batch::stop();
    if ($valid) {
      //print json_encode(array("result" => "success"));
      json::reply(array("result" => "success", "location" => $item->url()));
    } else {
      //json::reply(array("result" => "error", "form" => (string)$form));
      print $form;
    }
  }
  public function form_add($album_id) {
    $album = ORM::factory("item", $album_id);
    access::required("view", $album);
    access::required("add", $album);
    print embed_videos::get_add_form($album);
  }
}
