<?php defined("SYSPATH") or die("No direct script access.");

class sitemap_xtra_installer {
	static function deactivate() {
		module::clear_var("sitemap_xtra", "path");
		module::clear_var("sitemap_xtra", "base_url");
		module::clear_var("sitemap_xtra", "zip");
		module::clear_var("sitemap_xtra", "ping_google");
		module::clear_var("sitemap_xtra", "ping_bing");
		module::clear_var("sitemap_xtra", "ping_ask");
		module::clear_var("sitemap_xtra", "robots_txt");
		module::clear_var("sitemap_xtra", "albums");
		module::clear_var("sitemap_xtra", "albums_freq");
		module::clear_var("sitemap_xtra", "albums_prio");
		module::clear_var("sitemap_xtra", "photos");
		module::clear_var("sitemap_xtra", "photos_freq");
		module::clear_var("sitemap_xtra", "photos_prio");
		module::clear_var("sitemap_xtra", "movies");
		module::clear_var("sitemap_xtra", "movies_freq");
		module::clear_var("sitemap_xtra", "movies_prio");
// New for Pages: 
		module::clear_var("sitemap_xtra", "pages");
		module::clear_var("sitemap_xtra", "pages_freq");
		module::clear_var("sitemap_xtra", "pages_prio");
	}
}
