<?php defined("SYSPATH") or die("No direct script access.");/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
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

    protected $gm_ini;
    protected $gm_core_ini;

    public function __construct() {
        parent::__construct();

        $this->gm_ini = TMPPATH.'/gm.ini';
        $this->gm_core_ini = TMPPATH.'/gm_core.ini';
    }

	public function index() {
  
    //Start execution timer
    $bgtime=time();
    
		$view = new Admin_View("admin.html");
		$view->page_title = t("Gallery 3 :: Manage Module Updates");
		$view->content = new View("admin_moduleupdates.html");
		$view->content->mu_version = module::get_version("moduleupdates");

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
    }
		
    $update_count = 0;
    
    if($refreshCache == true){
		// Only poll GalleryModules.com once for the ini file.
		$fp = fopen($this->gm_ini, 'w');
		$fp2 = fopen($this->gm_core_ini,'w');
		if(function_exists("curl_init")) {
			$cp = curl_init("http://www.gallerymodules.com/gallerymodules.ini");
			curl_setopt($cp, CURLOPT_FILE, $fp);
			$buffer = curl_exec($cp);
			curl_close($cp);
			fclose($fp);
			
			$cp = curl_init("http://www.gallerymodules.com/core.ini");
			curl_setopt($cp, CURLOPT_FILE, $fp2);
			$buffer = curl_exec($cp);
			curl_close($cp);
			fclose($fp2);
		} else {
			fwrite($fp,file_get_contents("http://www.gallerymodules.com/gallerymodules.ini"));
		    	fclose($fp);

			fwrite($fp2,file_get_contents("http://www.gallerymodules.com/core.ini"));
		    	fclose($fp2);
		}		
		
      foreach (module::available() as $this_module_name => $module_info) {

        $font_color_local = "black";
        $core_version = '';
        $core_server = '';
        $core_dlink = '';
        $font_color_core = "black";
        $contrib_version = '';
        $contrib_server = '';
        $contrib_dlink = '';
        $font_color_contrib = "black";
        $gh_version = '';
        $gh_server = '';
        $gh_dlink = '';
        $font_color_gh = "black";
        
        
        
        $font_color_local = $this->get_local_module_version_color ($module_info->version, $module_info->code_version);
        list ($core_version, $core_server) = $this->get_remote_module_version($this_module_name, "CORE");
        $font_color_core = $this->get_module_version_color ($module_info->version, $module_info->code_version, $core_version);
        if(!is_numeric($core_version)) {
          list ($gh_version, $gh_server) = $this->get_remote_module_version($this_module_name, "GH");
          $font_color_gh = $this->get_module_version_color ($module_info->version, $module_info->code_version, $gh_version);
        }
        
        if($font_color_core == "red" or $font_color_contrib == "red" or $font_color_gh == "red"){
          $update_count++;
        }
        
        $module_info->name = "<a href=\"http://codex.gallery2.org/Gallery3:Modules:".$this_module_name."\" target=\"_new\">".$module_info->name."</a>";
        
        if (is_numeric($core_version)) {
          if($core_version > $module_info->version) {
            $core_dlink = "http://github.com/gallery/gallery3/tree/master/modules/".$this_module_name;
          }
        }
      
        if (is_numeric($gh_version)) {
          if($gh_version > $module_info->version) {
            $this_gm_repo = str_replace(".","",substr_replace(gallery::VERSION,"",strpos(gallery::VERSION," ")));
            if($this_gm_repo == "30"){
              $gh_dlink = "http://www.gallerymodules.com/update/".$this_module_name;
            } else {
              $gh_dlink = "http://www.gallerymodules.com/update".$this_gm_repo."/".$this_module_name;
            }
            
          }
        }
        
        //populate the list fo modules and their data
        $cache->$this_module_name = array ("name" => $module_info->name, "locked" => $module_info->locked,
          "code_version" => $module_info->code_version, "active" => $module_info->active, 
          "version" => $module_info->version,"description" => $module_info->description, 
          "core_version" => $core_version, "core_server" => $core_server, "font_color_core" => $font_color_core,
          "contrib_version" => $contrib_version, "contrib_server" => $contrib_server, "font_color_contrib" => $font_color_contrib,
          "gh_version" => $gh_version, "gh_server" => $gh_server, "font_color_gh" => $font_color_gh,
          "font_color_local" => $font_color_local, "core_dlink" => $core_dlink, "contrib_dlink" => $contrib_dlink, 
          "gh_dlink" => $gh_dlink);
      }
      
      //Define right now as YYYY.MM.DD HH:MM with the # of updates that are out of date
      $cache_updates = array("date" => date("Y.m.d - H:i"), "updates" => $update_count);

      //Write out the new data to cache with a 30 day expiration & 0 for update data so it's always present
      Cache::instance()->set("moduleupdates_cache", serialize($cache), array("ModuleUpdates"), 30*86400);
      Cache::instance()->set("moduleupdates_cache_updates", serialize($cache_updates), array("ModuleUpdates"), null);
      log::success("moduleupdates", t("Completed checking remote GitHub for modules updates."));
		}
		
    if(is_file($this->gm_ini))
      unlink($this->gm_ini); 
    if(is_file($this->gm_core_ini))
      unlink($this->gm_core_ini);		
    
		$view->content->vars = $cache;
    $view->content->update_time = $cache_updates['date'];
    $view->content->csrf = access::csrf_token();
    $view->content->Google = $Google;
    $view->content->GitHub = $GitHub;
    $view->content->Gallery_Version = substr_replace(gallery::VERSION,"",strpos(gallery::VERSION," "));
		
    //End execution timer
    $ExecutionTime = (time()-$bgtime);
    if ($ExecutionTime < 1) {
      $ExecutionTime = '<font color=green>1</font>';
    }else if ($ExecutionTime <= 30){
      $ExecutionTime = '<font color=green>' . $ExecutionTime . '</font>';
    }else if ($ExecutionTime <= 60){
      $ExecutionTime = '<font color=orange>' . $ExecutionTime . '</font>';
    }else{
      $ExecutionTime = '<font color=red>' . $ExecutionTime . '</font>';
    }


    $view->content->ExecutionTime = $ExecutionTime;
        
		print $view;
	}
  
  
  /**
    *
  **/
  private function get_module_version_color ($version, $code_version, $remote_version) {
  
    $font_color = "black";
  
    //BLACK - no module version detected
    if ($remote_version == "") {
      $font_color = "black";
    //BLUE - DNE: Does Not Exist, this module was not found
    } else if ($remote_version == "DNE") {
      $font_color = "blue";
    //GREEN - Your version is newer than the GitHub
    } else if ($remote_version < $code_version  or ($version != '' 
          and $remote_version < $version)) {
      $font_color = "green";
    //RED - Your version is older than the GitHub
    } else if ($remote_version > $code_version   or ($version != '' 
          and $remote_version > $version)) {
      $font_color = "red";
    }
  
    return $font_color;
  }
 

  /**
    *
  **/
  private function get_local_module_version_color ($version, $code_version) {
  
    $font_color = "black";
  
    //PINK - Your installed version is newer than file version
    if ($version != '' and $code_version < $version) {
      $font_color = "pink";
    //ORANGE - Your file version is newer than the installed version
    } else if ($version != '' and $code_version > $version) {
      $font_color = "orange";
    }
  
    return $font_color;
  }
  
  /**
    * Parses the known GitHub repositories for new versions of modules.
    *
    * Searches the remote GitHub repositories for a module with a like filename to that of the ones
    * installed in the running Gallery isntall.  Reads the remote modules module.info file to
    * gather the version information.  Uses the following locations;
    *
    * http://github.com/gallery/gallery3
    * http://www.gallerymodules.com
    * 
    * @author brentil <forums@inner-ninja.com>
    * @param String - The folder name of the module to search for on the remote GitHub server
    * @param String - The remote server to check against
    * @return Array - An array with the remote module version and the server it was found on.
    */
	private function get_remote_module_version ($module_name, $server_location) {
	
		$version = '';
		$server = '';
		$file = null;
		
    switch ($server_location) {
      case "CORE":
          //Check the main Gallery3 GitHub
          if ($file == null) {
            try {
              if(file_exists($this->gm_core_ini)) {
                $file = 1;
              }	
              if ($file != null) {
                $gm_core_array = parse_ini_file($this->gm_core_ini,true);
                $server = '(G)';
              }
            }
              catch (Exception $e) {
            }
          }
          break;
      case "GH":
          //Parse ini file from GalleryModules.com
            try {
              if(file_exists($this->gm_ini)) {
                $file = 1;
              }	
              if ($file != null) {
                $gm_array = parse_ini_file($this->gm_ini,true);
                $server = '(GH)';
              }
            }
            catch (Exception $e) {
            	echo $e;
            }
          break;
    } 

	if ($file != null) {
    //Search in the GM listing
		if ($server_location == "GH"){
			//Search if this is a Gallery 3.0 module
      if(array_key_exists($module_name,$gm_array)){
        if(array_key_exists('g3',$gm_array[$module_name])){
          $version = $gm_array[$module_name]['g3'];
        }
        if($version == ''){
          if(array_key_exists('g31',$gm_array[$module_name])){
            $version = $gm_array[$module_name]['g31'];
          }
        }
        if($version == ''){
          if(array_key_exists('codex',$gm_array[$module_name])){
            $version = $gm_array[$module_name]['codex'];
          }
        }
      } else { //Module not found
          $version = '';
      }
    //Search in the Core listing
		} else {
      if(array_key_exists($module_name,$gm_core_array)){
        $version = $gm_core_array[$module_name]['version'];
      } else { //Module not found
        $version = '';
      }
		}
	}
    
      return array ($version, $server);
  }
}
