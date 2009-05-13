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
class Admin_Developer_Controller extends Admin_Controller {
  public function module() {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_developer.html");
    $view->content->title = t("Generate module");

    if (!is_writable(MODPATH)) {
      message::warning(
        t("The module directory is not writable. Please ensure that it is writable by the web server"));
    }
    list ($form, $errors) = $this->_get_module_form();
    $view->content->developer_content = $this->_get_module_create_content($form, $errors);
    print $view;
  }

  public function test_data() {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_developer.html");
    $v->content->title = t("Generate Test Data");

    list ($form, $errors) = $this->_get_module_form();
    $v->content->developer_content = $this->_get_test_data_view($form, $errors);
    print $v;
  }

  public function module_create() {
    access::verify_csrf();

    list ($form, $errors) = $this->_get_module_form();

    $post = new Validation($_POST);
    $post->add_rules("name", "required");
    $post->add_rules("description", "required");
    $post->add_callbacks("name", array($this, "_is_module_defined"));

    if ($post->validate()) {
      $task_def = Task_Definition::factory()
        ->callback("developer_task::create_module")
        ->description(t("Create a new module"))
        ->name(t("Create Module"));
      $task = task::create($task_def, array_merge(array("step" => 0), $post->as_array()));

      $success_msg = t("Generation of %module completed successfully",
                       array("module" => $post->name));
      $error_msg = t("Generation of %module failed.", array("module" => $post->name));
      print json_encode(array("result" => "started",
                              "max_iterations" => 15,
                              "success_msg" => $success_msg, "error_msg" => $error_msg,
                              "url" => url::site("admin/developer/run_task/{$task->id}?csrf=" .
                                                 access::csrf_token()),
                              "task" => $task->as_array()));
    } else {
      $v = $this->_get_module_create_content(arr::overwrite($form, $post->as_array()),
        arr::overwrite($errors, $post->errors()));
      print json_encode(array("result" => "error",
                              "form" => $v->__toString()));
    }
  }

  public function session($key) {
    access::verify_csrf();
    Session::instance()->set($key, Input::instance()->get("value"));
    url::redirect("albums/1");
  }

  public function test_data_create() {
    access::verify_csrf();

    list ($form, $errors) = $this->_get_test_data_form();

    $post = new Validation($_POST);
    $post->add_rules("albums", "numeric");
    $post->add_rules("photos", "numeric");
    $post->add_rules("comments", "numeric");
    $post->add_rules("tags", "numeric");
    $post->add_callbacks("albums", array($this, "_set_default"));
    $post->add_callbacks("photos", array($this, "_set_default"));
    $post->add_callbacks("comments", array($this, "_set_default"));
    $post->add_callbacks("tags", array($this, "_set_default"));

    if ($post->validate()) {
      $task_def = Task_Definition::factory()
        ->callback("developer_task::create_content")
        ->description(t("Create test content"))
        ->name(t("Create Test Data"));
      $total = $post->albums + $post->photos + $post->comments + $post->tags;
      $success_msg = t("Successfully generated test data");
      $error_msg = t("Problems with test data generation was encountered");
      $task = task::create($task_def, array("total" => $total, "batch" => (int)ceil($total / 10),
                                            "success_msg" => $success_msg,
                                            "current" => 0, "error_msg" => $error_msg,
                                            "albums" => $post->albums, "photos" => $post->photos,
                                            "comments" => $post->comments, "tags" => $post->tags));
      batch::start();

      print json_encode(array("result" => "started",
                              "max_iterations" => $total + 5,
                              "url" => url::site("admin/developer/run_task/{$task->id}?csrf=" .
                                                 access::csrf_token()),
                              "task" => $task->as_array()));
    } else {
      $v = $this->_get_test_data_view(arr::overwrite($form, $post->as_array()),
        arr::overwrite($errors, $post->errors()));
       print json_encode(array("result" => "error",
                              "form" => $v->__toString()));
    }
  }

  public function run_task($task_id) {
    access::verify_csrf();

    try {
      $task = task::run($task_id);
    } catch (Exception $e) {
      $error_msg = $e->getMessage();
      $task->done = true;
    }

    if ($task->done) {
      batch::stop();
      $context = unserialize($task->context);
      switch ($task->state) {
      case "success":
        message::success($context["success_msg"]);
        break;

      case "error":
        message::success(empty($error_msg) ? $context["error_msg"] : $error_msg);
        break;
      }
      print json_encode(array("result" => "success",
                              "task" => $task->as_array()));

    } else {
      print json_encode(array("result" => "in_progress",
                              "task" => $task->as_array()));
    }
  }

  function mptt() {
    $v = new Admin_View("admin.html");
    $v->content = new View("mptt_tree.html");

    $v->content->tree = $this->_build_tree();

    if (exec("which /usr/bin/dot")) {
      $v->content->url = url::site("admin/developer/mptt_graph");
    } else {
      $v->content->url = null;
      message::warning(t("The package 'graphviz' is not installed, degrading to text view"));
    }
    print $v;
  }

  function mptt_graph() {
    $items = ORM::factory("item")->orderby("id")->find_all();
    $data = $this->_build_tree();

    $proc = proc_open("/usr/bin/dot -Tsvg",
                      array(array("pipe", "r"),
                            array("pipe", "w")),
                      $pipes,
                      VARPATH . "tmp");
    fwrite($pipes[0], $data);
    fclose($pipes[0]);

    header("Content-Type: image/svg+xml");
    print(stream_get_contents($pipes[1]));
    fclose($pipes[1]);
    proc_close($proc);
  }

  private function _build_tree() {
    $items = ORM::factory("item")->orderby("id")->find_all();
    $data = "digraph G {\n";
    foreach ($items as $item) {
      $data .= "  $item->parent_id -> $item->id\n";
      $data .= "  $item->id [label=\"$item->id [$item->level] <$item->left, $item->right>\"]\n";
    }
    $data .= "}\n";
    return $data;
  }

  public function _is_module_defined(Validation $post, $field) {
    $module_name = strtolower(strtr($post[$field], " ", "_"));
    if (file_exists(MODPATH . "$module_name/module.info")) {
      $post->add_error($field, "module_exists");
    }
  }

  public function _set_default(Validation $post, $field) {
    if (empty($post->$field)) {
      $post->$field = 0;
    }
  }

  private function _get_module_form() {
    $form = array("name" => "", "description" => "", "theme[]" => array(), "menu[]" => array(),
                  "event[]" => array());
    $errors = array_fill_keys(array_keys($form), "");

    return array($form, $errors);
  }

  private function _get_module_create_content($form, $errors) {
    $config = Kohana::config("developer.methods");

    $v = new View("developer_module.html");
    $v->action = "admin/developer/module_create";
    $v->hidden = array("csrf" => access::csrf_token());
    $v->theme = $config["theme"];
    $v->event = $config["event"];
    $v->menu = $config["menu"];
    $v->form = $form;
    $v->errors = $errors;
    return $v;
  }

  private function _get_test_data_form() {
    $form = array("albums" => "10", "photos" => "10", "comments" => "10", "tags" => "10",
                  "generate_albums" => "");
    $errors = array_fill_keys(array_keys($form), "");

    return array($form, $errors);
  }

  private function _get_test_data_view($form, $errors) {
    $v = new View("admin_developer_test_data.html");
    $v->action = "admin/developer/test_data_create";
    $v->hidden = array("csrf" => access::csrf_token());
    $album_count = ORM::factory("item")->where("type", "album")->count_all();
    $photo_count = ORM::factory("item")->where("type", "photo")->count_all();

    $v->comment_installed = module::is_installed("comment");
    $comment_count = empty($v->comment_installed) ? 0 : ORM::factory("comment")->count_all();

    $v->tag_installed = module::is_installed("tag");
    $tag_count = empty($v->tag_installed) ? 0 : ORM::factory("tag")->count_all();

    $v->album_count = t2("%count album", "%count albums", $album_count);
    $v->photo_count = t2("%count photo", "%count photos", $photo_count);
    $v->comment_count = t2("%count comment", "%count comments", $comment_count);
    $v->tag_count = t2("%count tag", "%count tags", $tag_count);
    $v->form = $form;
    $v->errors = $errors;
    return $v;
  }
}
