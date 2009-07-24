<?php defined("SYSPATH") or die("No direct script access.");
class user_homes_menu_Core
{
	/**
	 * adds the users home admin to the menu screen
	 */
	static function admin($menu, $theme)
	{	
		$menu->add_after("users_groups",
			Menu::factory("link")
               		->id("user_homes")
               		->label(t("User Homes"))
               		->url(url::site("admin/user_homes")));
	}
}
