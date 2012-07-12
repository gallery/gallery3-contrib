<?php defined("SYSPATH") or die("No direct script access."); ?>
<?
class pear4gallery3_installer {
  static function install() {
    site_status::warning(t("Initial configuration for the theme may be required. Visit Admin\Appearance\Theme Options before continue."), "gd_init_configuration");
  }

  static function deactivate() {
    site_status::warning(t("DeActivate."), "gd_init_configuration");
    //site_status::clear("gd_init_configuration");
  }

	static function activate() {
    site_status::warning(t("Activate."), "gd_init_configuration");
		if (module::get_var("gallery", "resize_size") != 800):
			module::set_var("gallery", "resize_size", 800);
		endif;
		if (module::get_var("gallery", "thumb_size") != 200):
			module::set_var("gallery", "thumb_size", 200);
		endif;
		if (module::get_var("gallery", "page_size") != 50):
			module::set_var("gallery", "page_size", 50);
		endif;
	}
}

?>
