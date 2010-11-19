<?php defined("SYSPATH") or die("No direct script access.");/**
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

class Admin_Moduleupdates_Controller extends Admin_Controller {

/**
    * Builds the backend information for the Module Updates page.
    *
    * Builds an array of data containing the needed information about an installed copy of Gallery3
    * to determine if versions provided by GalleryModules.com are newer.  
    * 
    * List ID: The name of the folder the module resides in (obtained from module::available)
    * name: The given name of the module (obtained from module::available)
    * dlink: The download link for the module (obtained from module::available)
    * locked: If the module is considered locked by Gallery (obtained from module::available)
    * code_version: The version of the module in the modules directory (obtained from module::available)
    * active: If the module is installed and enabled (obtained from module::available)
    * version: The version installed and running (obtained from module::available)
    * description: The description of the module (obtained from module::available)
    * remote_version: The version of the code on GitHub (obtained from get_remote_module_version)
    * remote_server: The server the remote version is on (obtained from get_remote_module_version)
    * font_color: The color to display the update in depending on its status
    * 
    * @author brentil <forums@inner-ninja.com>
    */
	public function index() {
  
		$view = new Admin_View("admin.html");
		$view->page_title = t("Gallery 3 :: Manage Module Updates");
		$view->content = new View("admin_moduleupdates.html");

    $refreshCache = false;
    
		$cache = unserialize(Cache::instance()->get("moduleupdates_cache"));
    $cache_updates = unserialize(Cache::instance()->get("moduleupdates_cache_updates"));

    //if someone pressed the button to refresh now
    if (request::method() == "post") {
      access::verify_csrf();
      $cache = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
      $cache_updates = array("date" => "", "updates" => 0);
      $refreshCache = true;
    }else if(count($cache) < 1 or $cache_updates['date'] == ""){
      //if there are no items in the cache array or the update date is "" refresh the data
      $cache = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
      $cache_updates = array("date" => "", "updates" => 0);
      $refreshCache = true;
    }
    
    //Check the ability to access GalleryModules.com
    $GalleryModules = null;
    try {
      $GalleryModules = fopen ("http://www.gallerymodules.com", "r");
      if ($GalleryModules != null) {
        $GalleryModules = '<font color=green>Online</font>';
      }else{
        $GalleryModules = '<font color=red>Offline</font>';
      }
    }
    catch (Exception $e) {
      //echo 'Message: ' .$e->getMessage() . '<br>';
    }
    //Check the ability to access the Google
    $Google = null;
    try {
      $Google = fopen ("http://google.com", "r");
      if ($Google != null) {
        $Google = '<font color=green>Online</font>';
      }else{
        $Google = '<font color=red>Offline</font>';
      }
    }
    catch (Exception $e) {
      //echo 'Message: ' .$e->getMessage() . '<br>';
    }
		
    if($refreshCache == true){
      foreach (module::available() as $this_module_name => $module_info) {
        
        //example code for setting cache values
          //Cache::instance()->set($key, "$log{$msg}", array("task", "log", "import"), 2592000);
        //example delete cache
          //Cache::instance()->delete("update_l10n_cache:{$task->id}");
        //example for reading cache
          //$log = Cache::instance()->get($key);
        
        $remote_version = '';
        $remote_server = '';
        $update_count = 0;
        
        list ($remote_version, $remote_server) = $this->get_remote_module_version($this_module_name);
        
        $font_color = "black";
        //BLUE - DNE: Does Not Exist, this module was not found
        if ($remote_version == "DNE" or !is_numeric($remote_version)) {
          $font_color = "blue";
        //PINK - Your installed version is newer than file version
        } else if ($module_info->version != '' and $module_info->code_version < $module_info->version) {
          $font_color = "pink";
        //ORANGE - Your file version is newer than the installed version
        } else if ($module_info->version != '' and $module_info->code_version > $module_info->version) {
          $font_color = "orange";
        //GREEN - Your version is newer than the remote version
        } else if ($remote_version < $module_info->code_version  or ($module_info->version != '' 
              and $remote_version < $module_info->version)) {
          $font_color = "green";
        //RED - Your version is older than the remote version
        } else if ($remote_version > $module_info->code_version   or ($module_info->version != '' 
              and $remote_version > $module_info->version)) {
          $font_color = "red";
          $update_count++;
        }
        
        $module_info->name = "<a href=\"http://codex.gallery2.org/Gallery3:Modules:".$this_module_name."\" target=\"_new\">".$module_info->name."</a>";
				$module_info->dlink = "http://www.gallerymodules.com/update31/".$this_module_name;
        
        //populate the list fo modules and their data
        $cache->$this_module_name = array ("name" => $module_info->name, "dlink" => $module_info->dlink,
					"locked" => $module_info->locked, "code_version" => $module_info->code_version,
					"active" => $module_info->active, "version" => $module_info->version,
					"description" => $module_info->description, "remote_version" => $remote_version,
					"remote_server" => $remote_server, "font_color" => $font_color);
      }
      
      //Define right now as YYYY.MM.DD HH:MM with the # of updates that are out of date
      $cache_updates = array("date" => date("Y.m.d - H:i"), "updates" => $update_count);
      
      //---------------------------------------------------------------------------------------------
      //echo 'Message 02: ' .$cache_updates . '<br>';
      //---------------------------------------------------------------------------------------------
      
      //Write out the new data to cache with a 30 day expiration & 0 for update data so it's always present
      Cache::instance()->set("moduleupdates_cache", serialize($cache), array("ModuleUpdates"), 30*86400);
      Cache::instance()->set("moduleupdates_cache_updates", serialize($cache_updates), array("ModuleUpdates"), null);
      log::success("moduleupdates", t("Completed checking remote GitHub for modules updates."));
		}
    
		$view->content->vars = $cache;
    $view->content->update_time = $cache_updates['date'];
    $view->content->csrf = access::csrf_token();
    $view->content->Google = $Google;
		$view->content->GalleryModules = $GalleryModules;
        
		print $view;
	}
  
  
  /**
    * Checks GalleryModules.com for new versions of the modules.
    * 
    * @author brentil <forums@inner-ninja.com>
    * @param String The folder name of the module to search for on the remote GitHub server
    * @return Array An array with the remote module version and the server it was found on.
    */
	private function get_remote_module_version ($module_name) {
	
		$version = 'DNE';
		$server = '';
		$file = null;
		
    //Check GalleryModules.com
		if ($file == null) {
			try {
				$file = fopen ("http://www.gallerymodules.com/31m/".$module_name, "r");
				if ($file != null) {
          $server = '(GM)';
        }
			}
			catch (Exception $e) {
				//echo 'Message: ' .$e->getMessage() . '<br>';
			}
		}
						
		if ($file != null) {
			while (!feof ($file)) {
				$line = fgets ($file, 1024);
				$version = $line;
			}
			fclose ($file);
		}
    
        return array ($version, $server);
    }
}
