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
class Admin_Moduleorder_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }
  
  private function _get_view() {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Manage Module Order");
    $view->content = new View("admin_moduleorder.html");
    $view->content->csrf = access::csrf_token();
    $view->content->available = new View("admin_moduleorder_blocks.html");
    $view->content->active = new View("admin_moduleorder_blocks.html");
    $view->content->available->modules = $this->_get_modules();
    return $view;
  }

  public function update() {
    //Get the ordered list of modules    
    $modulerawlist = explode("&", trim($_POST['modulelist'], "&"));
    
    //Make sure that gallery and user modules are first in the list
    $currentindex = 2;
    $identity_provider = module::get_var("gallery", "identity_provider");
    foreach ($modulerawlist as $row) {
      $currentry = explode("=", $row);
      $currentry = explode(":", $currentry[1]);
      if ($currentry[0] == "gallery") {
        $modulelist[0] = $row;
      } elseif ($currentry[0] == $identity_provider) {
        $modulelist[1] = $row;
      } else {
        $modulelist[$currentindex] = $row;
        $currentindex++;
      }
    }
    ksort($modulelist);
    
    //Get the highest used index
    $highestindex = 0;
    foreach ($modulelist as $row) {
      $currentry = explode(":", $row);
      if ($currentry[1] > $highestindex) {
        $highestindex = $currentry[1];
      }
    }
    
    $highestindex++;       //Have a safety margin just in case
    //To avoid conflicts on the index we now rewrite all indices of all modules
    foreach ($modulelist as $row) {
      $highestindex++;
      $currentry = explode("=", $row);
      $currentry = explode(":", $currentry[1]);
      db::build()
        ->update("modules")
        ->set("id", $highestindex)
        ->where("name", "=", $currentry[0])
        ->execute();
    }
    
    //Now we are ready to write the correct id values
    $highestindex = 0;
    foreach ($modulelist as $row) {
      $highestindex++;
      $currentry = explode("=", $row);
      $currentry = explode(":", $currentry[1]);
      db::build()
        ->update("modules")
        ->set("id", $highestindex)
        ->where("name", "=", $currentry[0])
        ->execute();
    }
    
    //As last step we optimize the table
    db::query("OPTIMIZE TABLE `modules`")
              ->execute();
    message::success(t("Your settings have been saved."));
    url::redirect("admin/moduleorder");
    print $this->_get_view();
  }

  private function _get_modules() {
    $active_blocks = array();
    $available_modules = module_manager::get_available_site_modules();
    return $available_modules;
  }
}

