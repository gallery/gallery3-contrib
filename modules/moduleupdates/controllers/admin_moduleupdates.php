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

		$all_modules = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
		
		foreach (module::available() as $this_module_name => $module_info) {
			
			list ($remote_version, $remote_server) = $this->get_remote_module_version($this_module_name);
			
			$font_color = "black";
			if ($remote_version == "DNE") {
				$font_color = "blue";
			} else if ($remote_version < $module_info->code_version) {
				$font_color = "green";
			} else if ($remote_version > $module_info->code_version) {
				$font_color = "red";
			}
			$all_modules->$this_module_name = array ("name" => $module_info->name, "locked" => $module_info->locked,
      "code_version" => $module_info->code_version, "active" => $module_info->active, 
      "version" => $module_info->version,"description" => $module_info->description, 
      "remote_version" => $remote_version, "remote_server" => $remote_server, "font_color" => $font_color);
		}
		
		$view->content->vars = $all_modules;
		
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
	private function get_remote_module_version ($module_name) {
	
		$version = 'DNE';
		$server = '';
		$file = null;
		
		try {
			$file = fopen ("http://github.com/gallery/gallery3/raw/master/modules/".$module_name."/module.info", "r");
			$server = '(G3)';
		}
		catch (Exception $e) {
			//echo 'Message: ' .$e->getMessage() . '<br>';
		}
		
		if ($file == null) {
			try {
				$file = fopen ("http://github.com/gallery/gallery3-contrib/raw/master/modules/".$module_name."/module.info", "r");
				$server = '(G3CC)';
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
