<?php defined("SYSPATH") or die("No direct script access.");

class user_homes_installer 
{
	static function install()
	{
		module::set_version("user_homes", 1);
	}

	/**
	 * installs the extra collumn on the users table when the
	 * module is installed
	 */
	 
	static function activate()
	{
		$db = Database::instance();
		$db->query("ALTER TABLE {users} ADD home int(9) default NULL;");
	}

	/**
	 * uninstalls the extra collumn on the users table when the
	 * module is uninstalled
	 */
	static function deactivate() 
	{
		$db = Database::instance();
		$db->query("ALTER TABLE {users} DROP COLUMN home;");
	}
}
