<?php defined("SYSPATH") or die("No direct script access.");

class embed_videos_installer {
	static function install() {
		$db = Database::instance()
		$db->query("CREATE TABLE {embedded_videos} (
						`id` int(9) NOT NULL auto_increment,
						`embed_code` varchar(2048) DEFAULT NULL,
						`source` varchar(64) DEFAULT NULL,
						`item_id` int(9) NOT NULL,
						PRIMARY KEY (`id`)
						KEY fk_item_id (`id`))
						DEFAULT CHARSET=utf8;");
		module::set_version("embed_video", 2);
		//exec("cd modules/gallery/controllers/; ln -s ../../embed/controllers/embeds.php embeds.php");
	}
	
	static function deactivate() {

    }
}
