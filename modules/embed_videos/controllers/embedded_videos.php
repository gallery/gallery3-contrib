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
class Embedded_video_Controller extends Items_Controller {
    public function show($movie) {
        if (!is_object($movie)) {
            // show() must be public because we route to it in url::parse_url(), so make
            // sure that we're actually receiving an object
            throw new Kohana_404_Exception();
        }
        access::required("view", $movie);
        $where = array(array("type", "!=", "album"));
        $position = $movie->parent()->get_position($movie, $where);
        if ($position > 1) {
            list($previous_item, $ignore, $next_item) = $movie->parent()->children(3, $position - 2, $where);
        } else {
            $previous_item = null;
            list($next_item) = $movie->parent()->viewable()->children(1, $position, $where);
        }
        $template = new Theme_View("page.html", "item", "embed");
        $template->set_global("item", $movie);
        $template->set_global("children", array());
        $template->set_global("children_count", 0);
        $template->set_global("parents", $movie->parents());
        $template->set_global("next_item", $next_item);
        $template->set_global("previous_item", $previous_item);
        $template->set_global("sibling_count", $movie->parent()->viewable()->children_count($where));
        $template->set_global("position", $position);
        $template->content = new View("embed.html");
        $movie->view_count++;
        $movie->save();
        print $template;
    }
    public function update($movie_id) {
        access::verify_csrf();
        $movie = ORM::factory("item", $movie_id);
        access::required("view", $movie);
        access::required("edit", $movie);
        $form = embed::get_edit_form($movie);
        try {
            $valid = $form->validate();
            $movie->title = $form->edit_item->title->value;
            $movie->description = $form->edit_item->description->value;
            $movie->slug = $form->edit_item->slug->value;
            //$movie->name = $form->edit_item->inputs["name"]->value;
            $movie->validate();
        }
        catch(ORM_Validation_Exception $e) {
            // Translate ORM validation errors into form error messages
            foreach($e->validation->errors() as $key => $error) {
                $form->edit_item->inputs[$key]->add_error($error, 1);
            }
            $valid = false;
        }
        if ($valid) {
            $movie->save();
            module::event("item_edit_form_completed", $movie, $form);
            log::success("content", "Updated embed", "<a href=\"{$movie->url() }\">view</a>");
            message::success(t("Saved embed %movie_title", array("movie_title" => $movie->title)));
            if ($form->from_id->value == $movie->id) {
                // Use the new url; it might have changed.
                print json_encode(array("result" => "success", "location" => $movie->url()));
            } else {
                // Stay on the same page
                print json_encode(array("result" => "success"));
            }
        } else {
            print json_encode(array("result" => "error", "form" => (string)$form));
        }
    }
    public function create($id) {
        $album = ORM::factory("item", $id);
        access::required("view", $album);
        access::required("add", $album);
        access::verify_csrf();
        $form = embed::get_add_form($album);
        //$form->add_rules('youtubeid', array('required', 'length[11]'));
        //$form->add_callback('youtubeid', 'valid_youtubeid');
        batch::start();
        try {
            $valid = $form->validate();
            if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $form->add_embed->inputs['name']->value)) {
                $temp_filename = VARPATH . "tmp/" . $form->add_embed->inputs['name']->value . ".jpg";
                $item = ORM::factory("item");
                $item->type = "photo"; 
                $item->name = basename($form->add_embed->inputs['name']->value . ".jpg");
                //$item->youtubeid = $form->add_embed->youtubeid->value;
                $item->title = $form->add_embed->title->value;
                $item->parent_id = $album->id; 
                $item->description = $form->add_embed->description->value; 
                $item->slug = $form->add_embed->slug->value;
                //$item->validate();
                $content = file_get_contents("http://img.youtube.com/vi/" . $form->add_embed->inputs['name']->value . "/0.jpg");
                if ($content) {
                    $file = fopen($temp_filename, "wb");
                    fwrite($file, $content);
                    fclose($file);
                    gallery_graphics::composite($temp_filename, $temp_filename, array("file" => "modules/embed/images/embed_video_icon.png", "position" => "center", "transparency" => 95));
                    $item->set_data_file($temp_filename);
                    $path_info = @pathinfo($temp_filename);
                    $item->save();
                    $embedded_video = ORM::factory("embedded_video");
                    $embedded_video->video = true;
                    $embedded_video->embed_code = "test";
                    $embedded_video->source = "YouTube";
                    $embedded_video->item_id = $item->id;
                    $embedded_video->validate();
                    $embedded_video->save();
                    log::success("content", t("Added a embedded video"), html::anchor("embeds/$item->id", t("view video")));
                    module::event("add_event_form_completed", $item, $form);
                } else {
                    $form->add_embed->inputs['name']->add_error('invalid_id', 1);
                    $valid = false;
                }
            } else {
                $form->add_embed->inputs['name']->add_error('invalid_id', 1);
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
        print embed::get_add_form($album);
    }
    public function form_edit($id) {
        $embed = ORM::factory("item", $id);
        access::required("view", $embed);
        access::required("edit", $embed);
        print embed::get_edit_form($embed);
    }
}
