<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class transcode_theme_Core {
	
	static function resize_bottom($theme) {
		$block = new Block();
		
		$block->css_id = "g-resolutions";
		$block->title = t("Alternative Resolutions");
		
		$view = new View("transcode_resolution_variants.html");
		$view->item = $theme->item();
		$view->resolutions = ORM::factory("transcode_resolution")->where("item_id", "=", $view->item->id)->find_all();
		
		$block->content = $view;
		return $block;
	}
	
}