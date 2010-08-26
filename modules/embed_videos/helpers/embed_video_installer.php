<?php defined("SYSPATH") or die("No direct script access.");

class embed_installer {
	static function install() {
		exec("cd modules/gallery/controllers/; ln -s ../../embed/controllers/embeds.php embeds.php");
	}
	
	static function deactivate() {
		if (is_file("modules/gallery/controllers/embeds.php")) {
		 unlink("modules/gallery/controllers/embeds.php");
	 }
    }
}
