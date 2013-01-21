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
class developer_task_Core {
  static function available_tasks() {
    // Return empty array so nothing appears in the maintenance screen
    return array();
  }

  static function create_module($task) {
    $context = unserialize($task->context);

    if (empty($context["module"])) {
      $context["class_name"] = strtr($context["name"], " ", "_");
      $context["module"] = strtolower($context["class_name"]);
      $context["module_path"] = (MODPATH . $context["module"]);
    }

    switch ($context["step"]) {
    case 0:               // Create directory tree
      foreach (array("", "controllers", "helpers", "views") as $dir) {
        $path = "{$context['module_path']}/$dir";
        if (!file_exists($path)) {
          mkdir($path);
          chmod($path, 0755);
         }
      }
      break;
    case 1:               // Generate installer
      $context["installer"] = array();
      self::_render_helper_file($context, "installer");
      break;
    case 2:               // Generate theme helper
      $context["theme"] = !isset($context["theme"]) ? array() : $context["theme"];
      self::_render_helper_file($context, "theme");
      break;
    case 3:               // Generate block helper
      $context["block"] = array();
      self::_render_helper_file($context, "block");
      break;
    case 4:               // Generate event helper
      self::_render_helper_file($context, "event");
      break;
    case 5:               // Generate admin controller
      $file = "{$context['module_path']}/controllers/admin_{$context['module']}.php";
      ob_start();
      $v = new View("admin_controller.txt");
      $v->name = $context["name"];
      $v->module = $context["module"];
      $v->class_name = $context["class_name"];
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      break;
    case 6:               // Generate admin form
      $file = "{$context['module_path']}/views/admin_{$context['module']}.html.php";
      ob_start();
      $v = new View("admin_html.txt");
      $v->name = $context["name"];
      $v->module = $context["module"];
      $v->css_id = preg_replace("#\s+#", "", $context["name"]);
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      break;
    case 7:               // Generate controller
      $file = "{$context['module_path']}/controllers/{$context['module']}.php";
      ob_start();
      $v = new View("controller.txt");
      $v->name = $context["name"];
      $v->module = $context["module"];
      $v->class_name = $context["class_name"];
      $v->css_id = preg_replace("#\s+#", "", $context["name"]);
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      break;
    case 8:               // Generate sidebar block view
      $file = "{$context['module_path']}/views/{$context['module']}_block.html.php";
      ob_start();
      $v = new View("block_html.txt");
      $v->name = $context["name"];
      $v->module = $context["module"];
      $v->class_name = $context["class_name"];
      $v->css_id = preg_replace("#\s+#", "", $context["name"]);
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      break;
    case 9:               // Generate dashboard block view
      $file = "{$context['module_path']}/views/admin_{$context['module']}_block.html.php";
      ob_start();
      $v = new View("dashboard_block_html.txt");
      $v->name = $context["name"];
      $v->module = $context["module"];
      $v->class_name = $context["class_name"];
      $v->css_id = preg_replace("#\s+#", "", $context["name"]);
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      break;
    case 10:              // Generate module.info (do last)
      $file = "{$context["module_path"]}/module.info";
      ob_start();
      $v = new View("module_info.txt");
      $v->module_name = $context["display_name"];
      $v->module_description = $context["description"];
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      break;
    }
    if (isset($file)) {
      chmod($file, 0765);
    }
    $task->done = (++$context["step"]) >= 11;
    $task->context = serialize($context);
    $task->state = "success";
    $task->percent_complete = ($context["step"] / 11.0) * 100;
  }

  private static function _render_helper_file($context, $helper) {
    if (isset($context[$helper])) {
      $config = Kohana::config("developer.methods");
      $file = "{$context["module_path"]}/helpers/{$context["module"]}_{$helper}.php";
      touch($file);
      ob_start();
      $v = new View("$helper.txt");
      $v->helper = $helper;
      $v->name = $context["name"];
      $v->module = $context["module"];
      $v->module_name = $context["name"];
      $v->css_id = strtr($context["name"], " ", "");
      $v->css_id = preg_replace("#\s#", "", $context["name"]);
      $v->callbacks = empty($context[$helper]) ? array() : array_fill_keys($context[$helper], 1);
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
    }
  }

  static function create_content($task) {
    $context = unserialize($task->context);
    $batch_cnt = $context["batch"];
    while ($context["albums"] > 0 && $batch_cnt > 0) {
      set_time_limit(30);
      self::_add_album_or_photo("album");

      $context["current"]++;
      $context["albums"]--;
      $batch_cnt--;
    }
    while ($context["photos"] > 0 && $batch_cnt > 0) {
      set_time_limit(30);
      self::_add_album_or_photo();

      $context["current"]++;
      $context["photos"]--;
      $batch_cnt--;
    }
    while ($context["comments"] > 0 && $batch_cnt > 0) {
      self::_add_comment();
      $context["current"]++;
      $context["comments"]--;
      $batch_cnt--;
    }
    while ($context["tags"] > 0 && $batch_cnt > 0) {
      self::_add_tag();
      $context["current"]++;
      $context["tags"]--;
      $batch_cnt--;
    }
    $task->done = $context["current"] >= $context["total"];
    $task->context = serialize($context);
    $task->state = "success";
    $task->percent_complete = $context["current"] / $context["total"] * 100;
  }

  private static function _add_album_or_photo($desired_type=null) {
    srand(time());
    $parents = ORM::factory("item")->where("type", "=", "album")->find_all()->as_array();
    $owner_id = identity::active_user()->id;

    $test_images = glob(dirname(dirname(__FILE__)) . "/data/*.[Jj][Pp][Gg]");

    $parent = $parents[array_rand($parents)];
    $parent->reload();
    $type = $desired_type;
    if (!$type) {
      $type = rand(0, 10) ? "photo" : "album";
    }
    if ($type == "album") {
      $thumb_size = module::get_var("core", "thumb_size");
      $rand = rand();
      $item = ORM::factory("item");
      $item->type = "album";
      $item->parent_id = $parent->id;
      $item->name = "rnd_$rand";
      $item->title = "Rnd $rand";
      $item->description = "random album $rand";
      $item->owner_id = $owner_id;
      $parents[] = $item->save();
    } else {
      $photo_index = rand(0, count($test_images) - 1);
      $item = ORM::factory("item");
      $item->type = "photo";
      $item->parent_id = $parent->id;
      $item->set_data_file($test_images[$photo_index]);
      $item->name = basename($test_images[$photo_index]);
      $item->title = "rnd_" . rand();
      $item->description = "sample thumb";
      $item->owner_id = $owner_id;
      $item->save();
    }
  }

  private static function _add_comment() {
    srand(time());
    $photos = ORM::factory("item")->where("type", "=", "photo")->find_all()->as_array();
    $users = ORM::factory("user")->find_all()->as_array();

    if (empty($photos)) {
      return;
    }

    if (module::is_active("akismet")) {
      akismet::$test_mode = 1;
    }

    $photo = $photos[array_rand($photos)];
    $author = $users[array_rand($users)];
    $guest_name = ucfirst(self::_random_phrase(rand(1, 3)));
    $guest_email = sprintf("%s@%s.com", self::_random_phrase(1), self::_random_phrase(1));
    $guest_url = sprintf("http://www.%s.com", self::_random_phrase(1));

    $comment = ORM::factory("comment");
    $comment->author_id = $author->id;
    $comment->item_id = $photo->id;
    $comment->text = self::_random_phrase(rand(8, 500));
    $comment->guest_name = $guest_name;
    $comment->guest_email = $guest_email;
    $comment->guest_url = $guest_url;
    $comment->save();
  }

  private static function _add_tag() {
    $items = ORM::factory("item")->find_all()->as_array();

    if (!empty($items)) {
      $tags = self::_generateTags();

      $tag_name = $tags[array_rand($tags)];
      $item = $items[array_rand($items)];

      tag::add($item, $tag_name);
    }
  }

  private static function _random_phrase($count) {
    static $words;
    if (empty($words)) {
      $sample_text = "Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium
        laudantium, totam rem aperiam eaque ipsa, quae ab illo inventore veritatis et quasi
        architecto beatae vitae dicta sunt, explicabo. Nemo enim ipsam voluptatem, quia voluptas
        sit, aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos, qui ratione
        voluptatem sequi nesciunt, neque porro quisquam est, qui dolorem ipsum, quia dolor sit,
        amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt, ut
        labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis
        nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi
        consequatur? Quis autem vel eum iure reprehenderit, qui in ea voluptate velit esse, quam
        nihil molestiae consequatur, vel illum, qui dolorem eum fugiat, quo voluptas nulla
        pariatur?  At vero eos et accusamus et iusto odio dignissimos ducimus, qui blanditiis
        praesentium voluptatum deleniti atque corrupti, quos dolores et quas molestias excepturi
        sint, obcaecati cupiditate non provident, similique sunt in culpa, qui officia deserunt
        mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
        expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio, cumque
        nihil impedit, quo minus id, quod maxime placeat, facere possimus, omnis voluptas
        assumenda est, omnis dolor repellendus.  Temporibus autem quibusdam et aut officiis
        debitis aut rerum necessitatibus saepe eveniet, ut et voluptates repudiandae sint et
        molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut
        reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores
        repellat.";
      $words = preg_split('/\s+/', $sample_text);
    }

    $chosen = array();
    for ($i = 0; $i < $count; $i++) {
      $chosen[] = $words[array_rand($words)];
    }

    return implode(' ', $chosen);
  }

  private static function _generateTags($number=10){
    // Words from lorem2.com
    $words = explode(
      " ",
      "Lorem ipsum dolor sit amet consectetuer adipiscing elit Donec odio Quisque volutpat " .
      "mattis eros Nullam malesuada erat ut turpis Suspendisse urna nibh viverra non " .
      "semper suscipit posuere a pede  Donec nec justo eget felis facilisis " .
      "fermentum Aliquam porttitor mauris sit amet orci Aenean dignissim pellentesque " .
      "felis Morbi in sem quis dui placerat ornare Pellentesque odio nisi euismod in " .
      "pharetra a ultricies in diam Sed arcu Cras consequat Praesent dapibus neque " .
      "id cursus faucibus tortor neque egestas augue eu vulputate magna eros eu " .
      "erat Aliquam erat volutpat Nam dui mi tincidunt quis accumsan porttitor " .
      "facilisis luctus metus Phasellus ultrices nulla quis nibh Quisque a " .
      "lectus Donec consectetuer ligula vulputate sem tristique cursus Nam nulla quam " .
      "gravida non commodo a sodales sit amet nisi Pellentesque fermentum " .
      "dolor Aliquam quam lectus facilisis auctor ultrices ut elementum vulputate " .
      "nunc Sed adipiscing ornare risus Morbi est est blandit sit amet sagittis vel " .
      "euismod vel velit Pellentesque egestas sem Suspendisse commodo ullamcorper " .
      "magna");

    while ($number--) {
      $results[] = $words[array_rand($words, 1)];
    }
    return $results;
  }
}