<?php defined("SYSPATH") or die("No direct script access.");

class sitemap_xtra_event_Core {
	static function admin_menu($menu, $theme) {
		$menu->get("settings_menu")
			->append(Menu::factory("link")
			->id("sitemap_menu")
			->label(t("XML Sitemap_xtra"))
			->url(url::site("admin/sitemap_xtra")));
	}
}
