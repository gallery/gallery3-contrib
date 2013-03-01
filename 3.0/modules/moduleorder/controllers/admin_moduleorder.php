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
class Admin_Moduleorder_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }
  
  private function _get_view() {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Manage module order");
    $view->content = new View("admin_moduleorder.html");
    $view->content->csrf = access::csrf_token();
    $view->content->available = new View("admin_moduleorder_blocks.html");
    $view->content->active = new View("admin_moduleorder_blocks.html");
    if (module::get_version("gallery") > 31) {
      $view->content->available->modules = $this->_get_modules();
    }
    return $view;
  }

  public function update() {
    //Get the ordered list of modules    
    $modulerawlist = explode("&", trim($_POST['modulelist'], "&"));
    
    //Make sure that gallery and user modules are first in the list
    $current_weight = 2;
    $identity_provider = module::get_var("gallery", "identity_provider");
    foreach ($modulerawlist as $row) {
      $currentry = explode("=", $row);
      $currentry = explode(":", $currentry[1]);
      if ($currentry[0] == "gallery") {
        $modulelist[0] = $row;
      } elseif ($currentry[0] == $identity_provider) {
        $modulelist[1] = $row;
      } else {
        $modulelist[$current_weight] = $row;
        $current_weight++;
      }
    }
    ksort($modulelist);
    
    //Write the correct weight values
    $current_weight = 0;
    foreach ($modulelist as $row) {
      $current_weight++;
      $currentry = explode("=", $row);
      $currentry = explode(":", $currentry[1]);
      db::build()
        ->update("modules")
        ->set("weight", $current_weight)
        ->where("id", "=", $currentry[1])
        ->execute();
    }
    
    message::success(t("Your settings have been saved."));
    url::redirect("admin/moduleorder");
    print $this->_get_view();
  }

  private function _get_modules() {
    $active_blocks = array();
    $available_modules = moduleorder::get_available_site_modules();
    return $available_modules;
  }
}

