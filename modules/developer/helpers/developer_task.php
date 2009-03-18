<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2008 Bharat Mediratta
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
      foreach (array("", "controllers", "helpers", "js", "views") as $dir) {
        $path = "{$context['module_path']}/$dir";
        if (!file_exists($path)) {
          mkdir($path);
          chmod($path, 0777);
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
    case 4:               // Generate menu helper
      $context["menu"] = !isset($context["menu"]) ? array() : $context["menu"];
      self::_render_helper_file($context, "menu");
      break;
    case 5:               // Generate event helper
      self::_render_helper_file($context, "event");
      break;
    case 6:               // Generate admin controller
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
    case 7:               // Generate admin form
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
    case 8:               // Generate controller
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
    case 9:               // Generate sidebar block view
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
    case 10:              // Generate module.info (do last)
      $file = "{$context["module_path"]}/module.info";
      ob_start();
      $v = new View("module_info.txt");
      $v->module_name = $context["name"];
      $v->module_description = $context["description"];
      print $v->render();
      file_put_contents($file, ob_get_contents());
      ob_end_clean();
      break;
    }
    if (isset($file)) {
      chmod($file, 0666);
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
      chmod($file, 0666);
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
}