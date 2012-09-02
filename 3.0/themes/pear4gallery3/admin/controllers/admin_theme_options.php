<?php defined("SYSPATH") or die("No direct script access.");

/*
 */
?>
<?
class Admin_Theme_Options_Controller extends Admin_Controller {

  protected $min_gallery_ver = 46;

  private function load_theme_info() {
    $theme_id = module::get_var("gallery", "active_site_theme");
    $file = THEMEPATH . "$theme_id/theme.info";
    $theme_info = new ArrayObject(parse_ini_file($file), ArrayObject::ARRAY_AS_PROPS);
    return $theme_info;
  }

  private function get_theme_version() {
    $theme_info = $this->load_theme_info();
    return ($theme_info->version);
  }

  private function get_theme_name() {
    $theme_info = $this->load_theme_info();
    return ($theme_info->name);
  }

  private function prerequisite_check($group, $id, $is_ok, $caption, $caption_ok, $caption_failed, $iswarning, $msg_error) {
    $confirmation_caption = ($is_ok)? $caption_ok : $caption_failed;
    $checkbox = $group->checkbox($id)
      ->label($caption . " " . $confirmation_caption)
      ->checked($is_ok)
      ->disabled(true);
    if ($is_ok):
      $checkbox->class("g-success");
    elseif ($iswarning):
      $checkbox->class("g-prerequisite g-warning")->error_messages("failed", $msg_error)->add_error("failed", 1);
    else:
      $checkbox->class("g-error")->error_messages("failed", $msg_error)->add_error("failed", 1);
    endif;
  }

  /* Convert old values ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
  protected function upgrade_settings() {
    if (module::get_var("th_pear4gallery3", "show_logo")):
      module::clear_var("th_pear4gallery3", "show_logo");
		  module::set_var("th_pear4gallery3", "hide_logo", FALSE);
    endif;
    if (module::get_var("th_pear4gallery3", "show_sidebar")):
        module::clear_var("th_pear4gallery3", "show_sidebar");
        module::set_var("th_pear4gallery3", "sidebar_view", "toggle");
    endif;
  }

  protected function get_edit_form_admin() {
    $this->upgrade_settings();

    $form = new Forge("admin/theme_options/save/", "", null, array("id" =>"g-theme-options-form"));

// Just commenting out, we might want rssmodule in future versions.
//    $rssmodulecheck = (module::is_active("rss") && module::info("rss"));

    /* Prerequisites ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("requirements")->label(t("Prerequisites"));
    $gallery_ver = module::get_version("gallery");
    $this->prerequisite_check($group, "vercheck", $gallery_ver >= $this->min_gallery_ver,
      t("Gallery 3 Core v.") . $this->min_gallery_ver . "+", t("Installed"), t("Required"), FALSE, sprintf(t("Check Failed. Minimum Required Version is %s. Found %s."), $this->min_gallery_ver, $gallery_ver));
		$this->prerequisite_check($group, "square_thumbs", (module::is_active("square_thumbs") and module::info("square_thumbs")),
			t("Square Thumbnails"), t("Found"), t("Required"), FALSE, t("Install <a href=\"http://codex.gallery2.org/Gallery3:Modules:square_thumbs\">Square Thumbnails</a> to display Thumbs correctly."));
    if (!module::get_var("th_pear4gallery3", "hide_thumbmeta")):
      $this->prerequisite_check($group, "info", (module::is_active("info") and module::info("info")),
        t("Info Module"), t("Found"), t("Required"), FALSE, t("Check Failed. Module is required to display Thumb metadata."));
    endif;

    /* General Settings ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme")->label(t("General Settings"));
    $group->input("favicon")
      ->label(t("URL (relative path) to your favicon.ico"))
      ->value(module::get_var("gallery", "favicon_url"));
    $group->input("appletouchicon")
      ->label(t("URL (relative path) to apple-touch-icon.png"))
      ->value(module::get_var("gallery", "appletouchicon_url"));
    $group->input("logo_path")
      ->label(t("URL (relative path) to custom logo"))
      ->value(module::get_var("th_pear4gallery3", "logo_path"));
    $group->input("slideshow_time")
      ->label(t("Slideshow timeout (in ms)"))
      ->value(module::get_var("th_pear4gallery3", "slideshow_time", "5000"));

    /* Advanced Options - General ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_adv_main")->label(t("Advanced Options - General"));
    $group->checkbox("hide_item_count")
      ->label(t("Hide Album Count"))
      ->checked(module::get_var("th_pear4gallery3", "hide_item_count"));
    $group->checkbox("hide_logo")
      ->label(t("Hide Bottom Pear Logo"))
      ->checked(module::get_var("th_pear4gallery3", "hide_logo"));
    $group->dropdown("mainmenu_view")
      ->label(t("Main page View"))
      ->options(array("grid" => t("Grid (Default)"), "mosaic" => t("Mosaic"), "carousel" => t("Carousel")))
      ->selected(module::get_var("th_pear4gallery3", "mainmenu_view"));
    $group->checkbox("show_guest_menu")
      ->label(t("Show Main Menu for Guest Users"))
      ->checked(module::get_var("th_pear4gallery3", "show_guest_menu"));
    $group->dropdown("background")
      ->label(t("Background color"))
      ->options(array("black" => t("Black (Default)"), "dkgrey" => t("Dark-Grey"), "ltgrey" => t("Light-Grey"), "white" => t("White")))
      ->selected(module::get_var("th_pear4gallery3", "background"));
    $group->checkbox("show_breadcrumbs")
      ->label(t("Show breadcrumbs for navigation."))
      ->checked(module::get_var("th_pear4gallery3", "show_breadcrumbs"));
    $group->dropdown("sidebar_view")
      ->label(t("Show Sidebar mode"))
      ->options(array("hidden" => t("Hidden (Default)"), "static" => t("Always visible"), "toggle" => t("Hover"), "button" => t("Togglable via button")))
      ->selected(module::get_var("th_pear4gallery3", "sidebar_view"));
    $group->input("ga_code")
      ->label(t("<a href=\"http://www.google.com/analytics/\">Google analytics</a> code."))
      ->value(module::get_var("th_pear4gallery3", "ga_code"));
    $group->input("skimm_lim")
      ->label(t("Limit amount of thumbs in album skimming (set to 0 to disable)"))
      ->value(module::get_var("th_pear4gallery3", "skimm_lim", "50"));

    /* Advanced Options - Mosaic page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_adv_mosaic")->label(t("Advanced Options - Mosaic Page"));
    $group->dropdown("mosaic_effect")
      ->label(t("Effect of image transition"))
      ->options(array(
          "blind" => t("Blinds the element away and shows it by blinding it in. (default)"),
          "clip" => t("Clips the element on and off, vertically"),
          "drop" => t("Drops the element away and shows it by dropping it in"),
          "explode" => t("Explodes the element into multiple pieces"),
          "fade" => t("Fades the element, by gradually changing its opacity"),
          "fold" => t("Folds the element like a piece of paper"),
          "puff" => t("Scale and fade out animations create the puff effect"),
          "slide" => t("Slides the element out of the viewport"),
          "scale" => t("Shrink and grow an element"),
          "none" => t("Disable effects (faster switching)")))
      ->selected(module::get_var("th_pear4gallery3", "mosaic_effect", "blind"));

    /* Advanced Options - Photo settings ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_adv_photo")->label(t("Advanced Options - Photo settings"));
    $group->input("resize_size")
      ->label(t("Photo resize size (800 is default)"))
      ->value(module::get_var("gallery", "resize_size", "800"));
/*
    $group->dropdown("photo_descmode")
      ->label(t("Description Display Mode"))
      ->options(array("overlay_top" => t("Overlay Top"), "overlay_bottom" => t("Overlay Bottom"), "bottom" => t("Bottom"), "top" => t("Top"), "hide" => t("Hide")))
      ->selected(module::get_var("th_pear4gallery3", "photo_descmode"));
    $group->checkbox("thumb_inpage")
      ->label(t("Keep Thumb Nav Block on the side"))
      ->checked(module::get_var("th_pear4gallery3", "thumb_inpage"));
    if (!$thumbnavcheck):
      $group->thumb_inpage->disabled(true);
    endif;
    $group->checkbox("hide_photometa")
      ->label(t("Hide Item Meta Data"))
      ->checked(module::get_var("th_pear4gallery3", "hide_photometa", TRUE));
    $group->checkbox("desc_allowbbcode")
      ->label(t("Allow BBCode/HTML in Descriptions"))
      ->checked(module::get_var("th_pear4gallery3", "desc_allowbbcode"));
*/
    /* Maintenance ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("maintenance")->label(t("Maintenance"));
    $group->checkbox("build_resize")->label(t("Mark all Image Resizes for Rebuild"))->checked(false);
    $group->checkbox("build_thumbs")->label(t("Mark all Thumbnails for Rebuild"))->checked(false);
    $group->checkbox("build_exif")->label(t("Mark Exif Info data for reload"))->checked(false);
    $iptccheck = module::is_active("iptc") and module::info("iptc");
    if ($iptccheck):
      $group->checkbox("build_iptc")->label(t("Mark IPTC Info data for reload"))->checked(false);
    endif;
    $group->checkbox("purge_cache")->label(t("Purge cache data"))->checked(false);
    $group->checkbox("reset_theme")->label(t("Reset Theme to a Default State"))->checked(false);

    module::event("theme_edit_form", $form);

    $form->submit("g-theme-options-save")->value(t("Save Changes"));

    return $form;
  }

  protected function get_edit_form_help() {
    $help = '<fieldset>';
    $help .= '<legend>Help</legend><ul>';
    $help .= '<li><h3>Prerequisites</h3>';

    $gallery_ver = module::get_version("gallery");
    if ( $gallery_ver >= $this->min_gallery_ver && (module::is_active("square_thumbs") and module::info("square_thumbs")) && (module::is_active("info") and module::info("info")) )
        $help .= '<p style="color: green;">Requirements are met for theme to function properly.</p>';
    else
        $help .= '<p style="color: red;">Requirements need to be met for theme to function properly.</p>';
    $help .= '
      </li>';

    $help .= '<li><h3>General Settings</h3>
      </li>';

    $help .= '<li><h3>Advanced Options - General</h3>
      </li>';
    $help .= '<li><h3>Advanced Options - Mosaic Page</h3>
      </li>';
    $help .= '<li><h3>Maintenance</h3>
      <p>Without changing image size, you can <b>Mark all Resizes for Rebuild</b>.
      Then you need to visit Admin\Maintenance to initiate the process.
      <p>Same can be done for image thumbs with <b>Mark all Thumbnails for Rebuild</b>.
      <p><b>Mark Exif/IPTC Info for reload</b> would mark all Exif or IPTC records as "Dirty" allowing it to be repopulated.
      <p>And just in case you think that something is not right, you can always <b>Reset Theme to a Default State</b>.
      </li>';
    $help .= '</ul></fieldset>';
    return t($help);
  }

  private function save_item_state($statename, $state, $value) {
    if ($state):
      module::set_var("th_pear4gallery3", $statename, $value);
    else:
      module::clear_var("th_pear4gallery3", $statename);
    endif;
  }

  protected function legacy() {
    module::clear_var("th_pear4gallery3", "hide_item_count");
    module::clear_var("th_pear4gallery3", "hide_logo");
    module::clear_var("th_pear4gallery3", "mainmenu_view");
    module::clear_var("th_pear4gallery3", "show_guest_menu");
    module::clear_var("th_pear4gallery3", "background");
    module::clear_var("th_pear4gallery3", "show_breadcrumbs");
    module::clear_var("th_pear4gallery3", "show_sidebar");
    module::clear_var("th_pear4gallery3", "sidebar_view");
    module::clear_var("th_pear4gallery3", "ga_code");
    module::clear_var("th_pear4gallery3", "skimm_lim");
    module::clear_var("th_pear4gallery3", "mosaic_effect");
  }

  protected function reset_theme() {
    // Default core theme settings
    module::set_var("gallery", "page_size", 50);
    module::set_var("gallery", "resize_size", 640);
    module::set_var("gallery", "thumb_size", 200);
    module::set_var("gallery", "header_text", "");
    module::set_var("gallery", "footer_text", "");
    module::set_var("gallery", "show_credits", FALSE);
    module::clear_all_vars("th_pear4gallery3");
    module::clear_var("th_pear4gallery3", "hide_logo");
  }

  public function save() {
    site_status::clear("gd_init_configuration");
    access::verify_csrf();

    $form = self::get_edit_form_admin();

    if ($form->validate()):
      $this->legacy();

      if ($form->maintenance->reset_theme->value):
        $this->reset_theme();
        module::event("theme_edit_form_completed", $form);
        message::success(t("Theme details are reset"));
      else:
        // * General Settings ****************************************************
        $old_resize_size  = module::get_var("gallery", "resize_size", 800);
        module::set_var("gallery", "resize_size", filter_var($form->edit_theme_adv_photo->resize_size->value, FILTER_VALIDATE_INT, array('options' => array('default' => 800, 'min_range' => 640))));

        $resize_size  = module::get_var("gallery", "resize_size", 800);

        $build_resize = $form->maintenance->build_resize->value;
        $build_thumbs = $form->maintenance->build_thumbs->value;
        $build_exif   = $form->maintenance->build_exif->value;
        if (module::is_active("iptc") and module::info("iptc")):
          $build_iptc   = $form->maintenance->build_iptc->value;
        else:
          $build_iptc = FALSE;
        endif;
        $purge_cache  = $form->maintenance->purge_cache->value;

        if ($build_resize || $old_resize_size != $resize_size):
          graphics::remove_rule("gallery", "resize", "gallery_graphics::resize");
          graphics::add_rule("gallery", "resize", "gallery_graphics::resize",
            array("width" => $resize_size, "height" => $resize_size, "master" => Image::AUTO), 100);
        endif;

        if (module::get_var("gallery", "resize_size") != $resize_size):
          module::set_var("gallery", "resize_size", $resize_size);
        endif;

        $thumb_size = 200;
        $rule = Image::AUTO;

        if ($build_thumbs):
          graphics::remove_rule("gallery", "thumb", "gallery_graphics::resize");
          graphics::add_rule("gallery", "thumb", "gallery_graphics::resize",
            array("width" => $thumb_size, "height" => $thumb_size, "master" => $rule), 100);
        endif;

        if (module::get_var("gallery", "thumb_size") != $thumb_size):
          module::set_var("gallery", "thumb_size", $thumb_size);
        endif;

        module::set_var("gallery", "page_size", 50);
        module::set_var("gallery", "favicon_url", $form->edit_theme->favicon->value);
        module::set_var("gallery", "appletouchicon_url", $form->edit_theme->appletouchicon->value);
        $this->save_item_state("logo_path", $form->edit_theme->logo_path->value, $form->edit_theme->logo_path->value);

        $this->save_item_state("slideshow_time", $form->edit_theme->slideshow_time->value != 5000, filter_var($form->edit_theme->slideshow_time->value, FILTER_VALIDATE_INT, array('options' => array('default' => 5000, 'min_range' => 1000))));

        // * Advanced Options - General ******************************************

        $this->save_item_state("hide_item_count",       $form->edit_theme_adv_main->hide_item_count->value, TRUE);
        $this->save_item_state("hide_logo",       $form->edit_theme_adv_main->hide_logo->value, TRUE);
        $this->save_item_state("mainmenu_view",         $form->edit_theme_adv_main->mainmenu_view->value != "grid", $form->edit_theme_adv_main->mainmenu_view->value);
        $this->save_item_state("show_guest_menu",$form->edit_theme_adv_main->show_guest_menu->value, TRUE);
        $this->save_item_state("background",            $form->edit_theme_adv_main->background->value != "black", $form->edit_theme_adv_main->background->value);
        $this->save_item_state("show_breadcrumbs",$form->edit_theme_adv_main->show_breadcrumbs->value, TRUE);
        $this->save_item_state("sidebar_view",$form->edit_theme_adv_main->sidebar_view->value != "hidden", $form->edit_theme_adv_main->sidebar_view->value);
        $this->save_item_state("ga_code",            $form->edit_theme_adv_main->ga_code->value, $form->edit_theme_adv_main->ga_code->value);
        $this->save_item_state("skimm_lim",            $form->edit_theme_adv_main->skimm_lim->value != 50, $form->edit_theme_adv_main->skimm_lim->value);

        // * Advanced Options - Photo page ***************************************
        $this->save_item_state("mosaic_effect",   $form->edit_theme_adv_mosaic->mosaic_effect->value != "blind", $form->edit_theme_adv_mosaic->mosaic_effect->value);
       /*
        $this->save_item_state("photo_popupbox",   $photo_popupbox != "default", $photo_popupbox);
        $this->save_item_state("thumb_inpage",     $form->edit_theme_adv_photo->thumb_inpage->value, TRUE);
        $this->save_item_state("hide_photometa",   !$form->edit_theme_adv_photo->hide_photometa->value, FALSE);
        $this->save_item_state("desc_allowbbcode", $form->edit_theme_adv_photo->desc_allowbbcode->value, TRUE);
*/

        module::event("theme_edit_form_completed", $form);

        message::success(t("Updated theme details"));

        if ($build_exif):
          db::update('exif_records')
            ->set(array('dirty'=>'1'))
            ->execute();
        endif;

        if ($build_iptc):
          db::update('iptc_records')
            ->set(array('dirty'=>'1'))
            ->execute();
        endif;

        if ($purge_cache):
          db::build()
            ->delete("caches")
            ->execute();
        endif;
      endif;
      url::redirect("admin/theme_options");
    else:
      print $this->get_admin_view();
    endif;
  }

  protected function get_admin_view() {
    $view = new Admin_View("admin.html");
    $view->page_title = t(".Pear Theme");
    $view->content = new View("admin_theme_options.html");
    $view->content->name = self::get_theme_name();
    $view->content->version = self::get_theme_version();
    $view->content->form = self::get_edit_form_admin();
    $view->content->help = self::get_edit_form_help();
    return $view;
  }

  public function index() {
    site_status::clear("gd_init_configuration");
    print $this->get_admin_view();
  }
}
?>
