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
    * to determine if versions stored in the GitHub are newer.  
    * 
    * List ID: The name of the folder the module resides in (obtained from module::available)
    * name: The given name of the module (obtained from module::available)
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

		$devDebug = false;
    $refreshCache = false;
    
		$cache = unserialize(Cache::instance()->get("moduleupdates_cache"));
    $cache_updates = unserialize(Cache::instance()->get("moduleupdates_cache_updates"));
    
    //---------------------------------------------------------------------------------------------
    //echo 'Message 01: ' .$cache_updates . '<br>';
    //---------------------------------------------------------------------------------------------

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
    
    //Check the ability to access the Gallery3 GitHub
    $GitHub = null;
    try {
      $GitHub = fopen ("http://github.com", "r");
      if ($GitHub != null) {
        $GitHub = '<font color=green>Online</font>';
      }else{
        $GitHub = '<font color=red>Offline</font>';
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
        
        list ($remote_version, $remote_server) = $this->get_remote_module_version($this_module_name, $devDebug);
        
        $font_color = "black";
        //BLUE - DNE: Does Not Exist, this module was not found
        if ($remote_version == "DNE") {
          $font_color = "blue";
        //PINK - Your installed version is newer than file version
        } else if ($module_info->version != '' and $module_info->code_version < $module_info->version) {
          $font_color = "pink";
        //ORANGE - Your file version is newer than the installed version
        } else if ($module_info->version != '' and $module_info->code_version > $module_info->version) {
          $font_color = "orange";
        //GREEN - Your version is newer than the GitHub
        } else if ($remote_version < $module_info->code_version  or ($module_info->version != '' 
              and $remote_version < $module_info->version)) {
          $font_color = "green";
        //RED - Your version is older than the GitHub
        } else if ($remote_version > $module_info->code_version   or ($module_info->version != '' 
              and $remote_version > $module_info->version)) {
          $font_color = "red";
          $update_count++;
          /*
          if($remote_server == "(G3)"){
            $module_info->name = "<a href=\"http://github.com/gallery/gallery3/tree/master/modules/".$this_module_name."\" target=\"_new\">".$module_info->name."</a>";
          }else if($remote_server == "(G3CC)"){
            $module_info->name = "<a href=\"http://github.com/gallery/gallery3-contrib/tree/master/modules/".$this_module_name."\" target=\"_new\">".$module_info->name."</a>";
          }else if($remote_server == "(brentil)"){
            $module_info->name = "<a href=\"http://github.com/brentil/gallery3-contrib/tree/master/modules/".$this_module_name."\" target=\"_new\">".$module_info->name."</a>";
          }
          */
        }
        
        $module_info->name = "<a href=\"http://codex.gallery2.org/Gallery3:Modules:".$this_module_name."\" target=\"_new\">".$module_info->name."</a>";
        
        //populate the list fo modules and their data
        $cache->$this_module_name = array ("name" => $module_info->name, "locked" => $module_info->locked,
          "code_version" => $module_info->code_version, "active" => $module_info->active, 
          "version" => $module_info->version,"description" => $module_info->description, 
          "remote_version" => $remote_version, "remote_server" => $remote_server, "font_color" => $font_color);
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
    $view->content->GitHub = $GitHub;
		
        
		print $view;
	}
  
  
  /**
    * Parses the known GitHub repositories for new versions of modules.
    *
    * Searches the remote GitHub repositories for a module with a like filename to that of the ones
    * installed in the running Gallery isntall.  Reads the remote modules module.info file to
    * gather the version information.  Uses the following locations;
    *
    * http://github.com/gallery/gallery3
    * http://github.com/gallery/gallery3-contrib
    * 
    * @author brentil <forums@inner-ninja.com>
    * @param String The folder name of the module to search for on the remote GitHub server
    * @return Array An array with the remote module version and the server it was found on.
    */
	private function get_remote_module_version ($module_name, $devDebug) {
	
		$version = 'DNE';
		$server = '';
		$file = null;
		
    //For development debug only
    if ($devDebug == true){
			if ($file == null) {
        try {
          $file = fopen ("http://github.com/brentil/gallery3-contrib/raw/master/3.0/modules/".$module_name."/module.info", "r");
          if ($file != null) {
            $server = '(brentil)';
          }
        }
        catch (Exception $e) {
          //echo 'Message: ' .$e->getMessage() . '<br>';
        }
      }
		}
    
    //Check the main Gallery3 GitHub
    if ($file == null) {
      try {
        $file = fopen ("http://github.com/gallery/gallery3/raw/master/modules/".$module_name."/module.info", "r");
        if ($file != null) {
          $server = '(G3)';
        }
      }
      catch (Exception $e) {
        //echo 'Message: ' .$e->getMessage() . '<br>';
      }
    }
		
    //Check the Gallery3 Community Contributions GitHub
		if ($file == null) {
			try {
				$file = fopen ("http://github.com/gallery/gallery3-contrib/raw/master/3.0/modules/".$module_name."/module.info", "r");
				if ($file != null) {
          $server = '(G3CC)';
        }
			}
			catch (Exception $e) {
				//echo 'Message: ' .$e->getMessage() . '<br>';
			}
		}
						
		if ($file != null) {
			while (!feof ($file)) {
				$line = fgets ($file, 1024);

        //Regular expression to find & gather the version number in the remote module.info file
				if (preg_match ("@version = (.*)@i", $line, $out)) {
					$version = $out[1];
					break;
				}
			}
			fclose ($file);
		}
    
        return array ($version, $server);
    }
}
