<?php defined("SYSPATH") or die("No direct script access.");

class user_homes_installer 
{
	static function install()
	{
		module::set_version("user_homes", 1);
	}

	/**
	 * installs the the table of user homes when the
	 * module is installed
	 */
	 
	static function activate()
	{
		$db = Database::instance();
		$db->query("CREATE TABLE IF NOT EXISTS {user_homes} (
			`id` int(9) NOT NULL,
			`home` int(9) default NULL,
                 	PRIMARY KEY (`id`),
                 	UNIQUE KEY(`id`))
               		ENGINE=InnoDB DEFAULT CHARSET=utf8;");			
	}

	/**
	 * drops the table of user homes when the 
	 * module is uninstalled
	 */
	static function deactivate() 
	{
		$db = Database::instance();
		$db->query("DROP TABLE IF EXISTS {user_homes};");
	}
}
