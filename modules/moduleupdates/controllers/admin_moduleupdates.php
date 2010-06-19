<?php defined("SYSPATH") or die("No direct script access.");

DEFINE("SITEMAP_FILENAME", "sitemap.xml");

class Admin_Moduleupdates_Controller extends Admin_Controller {

	public function index() {
		$view = new Admin_View("admin.html");
		$view->page_title = t("Gallery 3 :: Manage Module Updates");
		$view->content = new View("admin_moduleupdates.html");

		$all_modules = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
		
		foreach (module::available() as $this_module_name => $module_info){
			
			list($remote_version, $remote_server) = $this->get_remote_module_version($this_module_name);
			
			$font_color = "black";
			if($remote_version == "DNE"){
				$font_color = "blue";
			}else if($remote_version < $module_info->code_version){
				$font_color = "green";
			}else if($remote_version > $module_info->code_version){
				$font_color = "red";
			}
			$all_modules->$this_module_name = array("name" => $module_info->name, "locked" => $module_info->locked, "code_version" => $module_info->code_version, "locked" => $module_info->active, "version" => $module_info->version,"description" => $module_info->description, "remote_version" => $remote_version, "remote_server" => $remote_server, "font_color" => $font_color);
			//echo $this_module_name."=".$this->get_remote_module_version($this_module_name)."<br>";
		}
		
		//$view->content->vars = module::available();
		
		$view->content->vars = $all_modules;
		
		print $view;
	}
  
	private function get_remote_module_version($module_name){
	
		//http://github.com/gallery/gallery3-contrib/raw/master/modules/**MOD_NAME**/module.info
		$version = 'DNE';
		$server = '';
		$file = null;
		
		try{
			$file = fopen ("http://github.com/gallery/gallery3/raw/master/modules/".$module_name."/module.info", "r");
			//$file = fopen ("http://github.com/gallery/gallery3-contrib/raw/master/modules/google_analytics/module.info", "r");
			$server = '(G3)';
		}
		catch(Exception $e){
			//echo 'Message: ' .$e->getMessage() . '<br>';
		}
		
		if ($file == null) {
			try{
				$file = fopen ("http://github.com/gallery/gallery3-contrib/raw/master/modules/".$module_name."/module.info", "r");
				$server = '(G3CC)';
			}
			catch(Exception $e){
				//echo 'Message: ' .$e->getMessage() . '<br>';
			}
		}
		
		if ($file == null) {
			try{
				$file = fopen ("http://github.com/rWatcher/gallery3-contrib/raw/master/modules/".$module_name."/module.info", "r");
				$server = '(rW)';
			}
			catch(Exception $e){
				//echo 'Message: ' .$e->getMessage() . '<br>';
			}
		}
		
		
		if ($file != null) {
			while (!feof ($file)) {
				$line = fgets ($file, 1024);
				/* This only works if the title and its tags are on one line */
				if (preg_match ("@version = (.*)@i", $line, $out)) {
					$version = $out[1];
					break;
				}
			}
			fclose($file);
		}		
        return array ($version, $server);
    }
  
}
