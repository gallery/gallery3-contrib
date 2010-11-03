<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

class Admin_Theme_Options_Controller extends Admin_Controller {

  protected $min_gallery_ver = 30;

  private function load_theme_info() {
    $file = THEMEPATH . "greydragon/theme.info";
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

  private function get_colorpacks() {
    $colorpacks = array();
    $colorpackroot = THEMEPATH . 'greydragon/css/colorpacks/';

    foreach (scandir($colorpackroot) as $colorpack_name):
      if (file_exists($colorpackroot . "$colorpack_name/colors.css")):
        if ($colorpack_name[0] == "."):
          continue;
        endif;

        $colorpacks[$colorpack_name] = t($colorpack_name);
      endif;
    endforeach;
    return $colorpacks;
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

  protected function get_edit_form_admin() {
    $form = new Forge("admin/theme_options/save/", "", null, array("id" =>"g-theme-options-form"));

    $group = $form->group("requirements")->label("Prerequisites Checklist");
    $gallery_ver = module::get_version("gallery");

    $this->prerequisite_check($group, "vercheck", $gallery_ver >= $this->min_gallery_ver, 
      t("Gallery 3 Core v.") . $this->min_gallery_ver, "Installed", "Required", FALSE, t("Check Failed. Minimum Required Version") . " " . $gallery_ver);

    $this->prerequisite_check($group, "shadowbox", ((module::is_active("shadowbox")) and (module::info("shadowbox"))), 
      t("Shadowbox Module"), "Found", "Required", FALSE, t("Check Failed. Shadowbox Module not Installed."));

    if (!module::get_var("th_greydragon", "hide_thumbmeta")):
      $this->prerequisite_check($group, "info", (module::is_active("info") and module::info("info")), 
        t("Info Module"), "Found", "Required", FALSE, t("Check Failed. Module is required to display Thumb metadata."));
    endif;

    $group = $form->group("recommended")->label("Module Recommendations");

    $organize_active = ((module::is_active("organize")) and (module::info("organize")));
    $this->prerequisite_check($group, "organizecheck", !$organize_active, 
      t("Organize Module"), "not Used", "Found", TRUE, t("Default Organize module is active but is not supported in full by the theme."));

    $kbdnav_active = ((module::is_active("kbd_nav")) and (module::info("kbd_nav")));
    $this->prerequisite_check($group, "kbdnavcheck", $kbdnav_active, 
      t("Kbd Navigation Module"), "Found", "not Found", TRUE, t('Install <a href="http://codex.gallery2.org/Gallery3:Modules:kbd_nav" target="_blank">module</a> to enable keyboard navigation support.'));

    $sidebar_allowed = module::get_var("th_greydragon", "sidebar_allowed");
    $sidebar_visible = module::get_var("th_greydragon", "sidebar_visible");

    $pagesize = module::get_var("gallery", "page_size");
    if (($sidebar_allowed == "none") and ($sidebar_visible == "none")):
      $pagesize = $pagesize / 4;
    else:
      $pagesize = $pagesize / 3;
    endif;

    $group = $form->group("edit_theme")->label(t("General Settings"));
    $group->input("row_count")
      ->label(t("Rows per Album Page"))
      ->rules("required|valid_digit")
      ->error_messages("required", t("You must enter a number"))
      ->error_messages("valid_digit", t("You must enter a number"))
      ->value($pagesize);
    $group->input("resize_size")
      ->label(t("Resized Image Size (in pixels)"))
      ->rules("required|valid_digit")
      ->error_messages("required", t("You must enter a number"))
      ->error_messages("valid_digit", t("You must enter a number"))
      ->value(module::get_var("gallery", "resize_size"));
    $group->input("logo_path")
      ->label(t("Alternate Logo Image"))
      ->value(module::get_var("th_greydragon", "logo_path"));
    $group->input("header_text")
      ->label(t("Header Text"))
      ->value(module::get_var("gallery", "header_text"));
    $group->input("footer_text")
      ->label(t("Footer Text"))
      ->value(module::get_var("gallery", "footer_text"));
    $group->input("copyright")
      ->label(t("Copyright Message"))
      ->value(module::get_var("th_greydragon", "copyright"));
    $group->dropdown("colorpack")
      ->label(t("Selected Color Pack"))
      ->options(self::get_colorpacks())
      ->selected(module::get_var("th_greydragon", "color_pack", "greydragon"));

    $group = $form->group("edit_theme_adv_main")->label(t("Advanced Options - Main"));
    $group->checkbox("show_credits")
      ->label(t("Show Site Credits"))
      ->checked(module::get_var("gallery", "show_credits"));
    $group->checkbox("show_guest_menu")
      ->label(t("Show Main Menu for Guest Users"))
      ->checked(module::get_var("th_greydragon", "show_guest_menu"));
    $group->checkbox("loginmenu_position")
      ->label(t("Place Login Link in the Header"))
      ->checked(module::get_var("th_greydragon", "loginmenu_position") == "header");
    $group->checkbox("mainmenu_position")
      ->label(t("Alternate Header Layout"))
      ->checked(module::get_var("th_greydragon", "mainmenu_position") == "top");
    $group->checkbox("hide_breadcrumbs")
      ->label(t("Hide Breadcrumbs"))
      ->checked(module::get_var("th_greydragon", "hide_breadcrumbs"));
    $group->dropdown("photonav_position")
      ->label(t("Item Navigator Position"))
      ->options(array("top" => t("Top"), "bottom" => t("Bottom"), "both" => t("Both"), "none" => t("None")))
      ->selected(module::get_var("th_greydragon", "photonav_position"));
    $group->checkbox("disable_seosupport")
      ->label(t("Disallow Search Engine Indexing"))
      ->checked(module::get_var("th_greydragon", "disable_seosupport"));
    $group->checkbox("enable_pagecache")
      ->label(t("Enable Page Cache (60 seconds)"))
      ->checked(module::get_var("th_greydragon", "enable_pagecache"));

    $group = $form->group("edit_theme_adv_thumb")->label(t("Advanced Options - Album page/Thumbs"));
    $group->dropdown("thumb_ratio")
      ->label(t("Aspect Ratio"))
      ->options(array("photo" => t("Actual Size"), "digital" => t("Digital 4:3"), "film" => t("Film 3:2") /* , "square" => t("Square 1:1") */ ))
      ->selected(module::get_var("th_greydragon", "thumb_ratio"));
    $group->dropdown("thumb_descmode")
      ->label(t("Title Display Mode"))
      ->options(array("overlay" => t("Overlay"), "bottom" => t("Bottom"), "hide" => t("Hide")))
      ->selected(module::get_var("th_greydragon", "thumb_descmode"));
    $group->checkbox("hide_thumbmeta")
      ->label(t("Hide Item Meta Data"))
      ->checked(module::get_var("th_greydragon", "hide_thumbmeta"));

    $group = $form->group("edit_theme_adv_photo")->label(t("Advanced Options - Photo page"));
    $group->dropdown("photo_descmode")
      ->label(t("Description Display Mode"))
      ->options(array("overlay" => t("Overlay"), "bottom" => t("Bottom"), "top" => t("Top"), "hide" => t("Hide")))
      ->selected(module::get_var("th_greydragon", "photo_descmode"));
    $group->checkbox("desc_allowbbcode")
      ->label(t("Allow BBCode/HTML in Descriptions"))
      ->checked(module::get_var("th_greydragon", "desc_allowbbcode"));
    $group->checkbox("hide_photometa")
      ->label(t("Hide Item Meta Data"))
      ->checked(module::get_var("th_greydragon", "hide_photometa", TRUE));

    $group = $form->group("edit_theme_side")->label(t("Sidebar Options"));
    $group->checkbox("hide_blockheader")
      ->label(t("Hide Block Header"))
      ->checked(module::get_var("th_greydragon", "hide_blockheader"));
    $group->checkbox("sidebar_albumonly")
      ->label(t("Show Sidebar for Albums Only"))
      ->checked(module::get_var("th_greydragon", "sidebar_albumonly"));
    $group->dropdown("sidebar_allowed")
      ->label(t("Allowed Sidebar Positions"))
      ->options(array("any" => t("Any"), "left" => t("Left"), "right" => t("Right"), "none" => t("Default Only")))
      ->selected($sidebar_allowed);
    $group->dropdown("sidebar_visible")
      ->label(t("Default Sidebar Position"))
      ->options(array("right" => t("Right"), "left" => t("Left"), "none" => t("No sidebar")))
      ->selected($sidebar_visible);

    $group = $form->group("maintenance")->label("Maintenance");
    $group->checkbox("build_resize")->label(t("Mark all Image Resizes for Rebuild"))->checked(false);
    $group->checkbox("build_thumbs")->label(t("Mark all Thumbnails for Rebuild"))->checked(false);
    $group->checkbox("build_exif")->label(t("Reset Exif Info"))->checked(false);
    $group->checkbox("reset_theme")->label(t("Reset Theme to a Default State"))->checked(false);

    module::event("theme_edit_form", $form);

    $form->submit("g-theme-options-save")->value(t("Save Changes"));
    
    return $form;
  }

  protected function get_edit_form_help() {
    $help = '<fieldset>';
    $help .= '<legend>Help</legend><ul>';
    $help .= '<li><h3>Prerequisites</h3>
      <p><b>Requirements need to be met for theme to function properly.</b></p>
      <p>If indicated please download and install <a href="http://codex.gallery2.org/Gallery3:Modules:shadowbox" target="_blank">
      Shadowbox module</a>. Module is required to properly display photos in maximized view and for any admin operations dialogs.</p>
      </li>';

    $help .= '<li><h3>Module Recommendations</h3>
      <p><b>Some recommendations to make your experience with the theme more pleasant.</b></p>
      <p>While there is some support for default Organize module, theme may not skin it properly all the way.
      Please consider using <a href="http://codex.gallery2.org/Gallery3:Modules:gwtorganize" target="_blank">GWT Organize</a> Module instead.</p>
      <p>Enable Keyboard navigation by installing <a href="http://codex.gallery2.org/Gallery3:Modules:kbd_nav" target="_blank">Kbd Navigation</a> Module.</p>
      </li>';

    $help .= '<li><h3>General Settings</h3>
      <p>Theme is designed to display thumbnails in fixed 3+sidebar or 4 columns format.
      Number of <b>Rows per Album Page</b> however can be adjusted.<br />
      Unlike in default theme, thumbnails size is restricted to max 200x200px.</p>
      <p>Default G3 logo can be replaced with your own by providing <b>Alternate Logo Image</b>.
      Recommended logo size is within 300x80px. If you need bigger space for your logo, CSS would have to be adjusted.</p>
      <p>Logo could be suppressed altogether by providing <b>Header Text</b> which would take its place.  
      <b>Footer Text</b> would be simply placed next to Site\'s credits.</p>
      <p>To indicate your rights for the artwork displayed <b>Copyright Message</b> can be placed in
      right top corner of the footer.</p>
      <p>Important feature of the theme is ability to specify <b>Selected Color Pack</b>. Color Pack is small CSS 
      file which defines color rules for the theme. By default theme comes with GreyDragon (default) and Wind sets, 
      but it could be easily extended. Visit our Download page for additional information.</p>
      </li>';
    $help .= '<li><h3>Advanced Options</h3>
      <p><b>Show Site Credits</b> simply shows appreciation for hard work of G3 team and Theme\'s author
      (you could do also do this by clicking <b>Donate</b> link above).</p>
      <p>If main menu has functionality intended for guest users you can use <b>Show Main Menu for Guest Users</b>
      to keep it visible.</p>
      <p>If you do not like login link in the footer you can move it into top right corner by selecting <b>Place Login Link in the Header</b>.</p>
      <p>You can go even further and move main menu to the top of the header with breadcrumbs taking it place by selecting <b>Alternate Header Layout</b>.</p>
      <p><b>Item Navigator Position</b> could be changed to display it above and/or below the main content.</p> 
      <p><b>Thumb: Aspect Ratio</b> should be used with understanding that some information
      may be out of visible area in photo thumbs. Based on specified aspect all thumbs sizes would be adjusted 
      accordingly. When switching to/from <b>Actual Size</b>, it is recommended to rebuild thumbs for proper display
      (see Maintenance section below).</p>
      <p>If you prefer including Item\'s caption as part of the thumb, you can use <b>Thumb: Title Display Mode</b> to change
      default Overlay mode. And if metadata (owner/clicks) is not necessary, it could be hidden with <b>Thumb: Hide Item Meta Data</b>.</p>
      <p>Similar to Thumb option above Item\'s description could be displayed where available.
      In non-Overlay mode, this is not limited to just Photo page, but description could be
      displayed for albums also.</p>
      </li>';
    $help .= '<li><h3>Sidebar Options</h3>
      <p>If Block\'s header is not desired, it could be removed using <b>Hide Block Header</b>.</p>
      <p>Sidebar visibility could be limited to individual Photo pages with
      <b>Show Sidebar for Albums Only</b>.
      <p>When sidebar is visible it can be placed on the left or right of the 
      screen or removed altogether using <b>Allowed Sidebar Positions</b>.
      If more than one position is allowed, <b>Default Sidebar Position</b>
      would indicate default state, but visitor would able change it later.
      </li>';
    $help .= '<li><h3>Maintenance</h3>
      <p>Without changing image size, you can <b>Mark all Resizes for Rebuild</b>.
      Then you need to visit Admin\Maintenance to initiate the process.
      <p>Same can be done for image thumbs with <b>Mark all Thumbnails for Rebuild</b>.
      <p><b>Reset Exif Info</b> would remove all exif info allowing it to be imported again.</p>
      <p>And just in case you think that something is not right, you can 
      always <b>Reset Theme to a Default State</b>.
      </li>';
    $help .= '</ul></fieldset>';
    return $help;
  }

  private function save_item_state($statename, $state, $value) {
    if ($state):
      module::set_var("th_greydragon", $statename, $value);
    else:
      module::clear_var("th_greydragon", $statename);
    endif;
  }

  public function save() {
    site_status::clear("gd_init_configuration");
    access::verify_csrf();

    $form = self::get_edit_form_admin();
    if ($form->validate()):
      module::clear_var("th_greydragon", "photonav_top");
      module::clear_var("th_greydragon", "photonav_bottom");
      module::clear_var("th_greydragon", "hide_sidebar_photo");
      module::clear_var("th_greydragon", "hide_thumbdesc");
      module::clear_var("th_greydragon", "use_detailview");

      if ($form->maintenance->reset_theme->value):
        module::set_var("gallery", "page_size", 9);
        module::set_var("gallery", "resize_size", 640);
        module::set_var("gallery", "thumb_size", 200);

        module::set_var("gallery", "header_text", "");
        module::set_var("gallery", "footer_text", "");
        module::clear_var("th_greydragon", "copyright");
        module::clear_var("th_greydragon", "logo_path");
        module::clear_var("th_greydragon", "color_pack");
        
        module::clear_var("th_greydragon", "enable_pagecache");
        module::set_var("gallery", "show_credits", FALSE);
        module::clear_var("th_greydragon", "show_guest_menu");
        module::clear_var("th_greydragon", "mainmenu_position");
        module::clear_var("th_greydragon", "loginmenu_position");
        module::clear_var("th_greydragon", "hide_breadcrumbs");
        module::clear_var("th_greydragon", "horizontal_crop");
        module::clear_var("th_greydragon", "thumb_descmode");
        module::clear_var("th_greydragon", "hide_thumbmeta");
        module::clear_var("th_greydragon", "hide_blockheader");
        module::clear_var("th_greydragon", "photonav_position");
        module::clear_var("th_greydragon", "photo_descmode");
        module::clear_var("th_greydragon", "desc_allowbbcode");
        module::clear_var("th_greydragon", "hide_photometa");
        module::clear_var("th_greydragon", "disable_seosupport");

        module::clear_var("th_greydragon", "sidebar_albumonly");
        module::clear_var("th_greydragon", "sidebar_allowed");
        module::clear_var("th_greydragon", "sidebar_visible");

        module::event("theme_edit_form_completed", $form);
        message::success(t("Theme details are reset"));
      else:
        // * General Settings ****************************************************

        $_priorratio = module::get_var("th_greydragon", "thumb_ratio");
        if (!$_priorratio):
          $_priorratio = "digital";
        endif;

        $resize_size  = $form->edit_theme->resize_size->value;
        $thumb_size   = 200;

        $build_resize = $form->maintenance->build_resize->value;
        $build_thumbs = $form->maintenance->build_thumbs->value;
        $build_exif   = $form->maintenance->build_exif->value;

        $thumb_ratio = $form->edit_theme_adv_thumb->thumb_ratio->value;
        if ($thumb_ratio == "photo") { $rule = Image::AUTO; } else { $rule = Image::WIDTH; }
        $color_pack = $form->edit_theme->colorpack->value;
        $thumb_descmode = $form->edit_theme_adv_thumb->thumb_descmode->value;
        $photo_descmode = $form->edit_theme_adv_photo->photo_descmode->value;

        if ($build_resize):
          graphics::remove_rule("gallery", "resize", "gallery_graphics::resize");
          graphics::add_rule("gallery", "resize", "gallery_graphics::resize",
            array("width" => $resize_size, "height" => $resize_size, "master" => Image::AUTO), 100);
        endif;
        if (module::get_var("gallery", "resize_size") != $resize_size):
          module::set_var("gallery", "resize_size", $resize_size);
        endif;

        if ($build_thumbs):
          graphics::remove_rule("gallery", "thumb", "gallery_graphics::resize");
          graphics::add_rule("gallery", "thumb", "gallery_graphics::resize",
            array("width" => $thumb_size, "height" => $thumb_size, "master" => $rule), 100);
        endif;

        if ($build_exif):
          db::build()
            ->delete("exif_records")
            ->execute();
        endif;

        if (module::get_var("gallery", "thumb_size") != $thumb_size):
          module::set_var("gallery", "thumb_size", $thumb_size);
        endif;
        module::set_var("gallery", "header_text", $form->edit_theme->header_text->value);
        module::set_var("gallery", "footer_text", $form->edit_theme->footer_text->value);
        $this->save_item_state("copyright", $form->edit_theme->copyright->value, $form->edit_theme->copyright->value);
        $this->save_item_state("logo_path", $form->edit_theme->logo_path->value, $form->edit_theme->logo_path->value);
        $this->save_item_state("color_pack", (($color_pack) and ($color_pack != "greydragon")), $color_pack);

        // * Advanced Options - main *********************************************

        module::set_var("gallery", "show_credits",   $form->edit_theme_adv_main->show_credits->value);
        $this->save_item_state("show_guest_menu",    $form->edit_theme_adv_main->show_guest_menu->value, TRUE);
        $this->save_item_state("loginmenu_position", $form->edit_theme_adv_main->loginmenu_position->value == "1", "header");
        $this->save_item_state("mainmenu_position",  $form->edit_theme_adv_main->mainmenu_position->value == "1", "top");
        $this->save_item_state("hide_breadcrumbs",   $form->edit_theme_adv_main->hide_breadcrumbs->value, TRUE);
        $this->save_item_state("photonav_position",  $form->edit_theme_adv_main->photonav_position->value != "top", $form->edit_theme_adv->photonav_position->value);
        $this->save_item_state("enable_pagecache",   $form->edit_theme_adv_main->enable_pagecache->value, TRUE);
        $this->save_item_state("disable_seosupport", $form->edit_theme_adv_main->disable_seosupport->value, TRUE);

        // * Advanced Options - Album page ***************************************

        $this->save_item_state("thumb_ratio",       $thumb_ratio != "photo", $thumb_ratio);
        $this->save_item_state("thumb_descmode",    $thumb_descmode != "overlay", $thumb_descmode);
        $this->save_item_state("hide_thumbmeta",    $form->edit_theme_adv_thumb->hide_thumbmeta->value, TRUE);

        // * Advanced Options - Photo page ***************************************
        
        $this->save_item_state("photo_descmode",    $photo_descmode != "overlay", $photo_descmode);
        $this->save_item_state("desc_allowbbcode",  $form->edit_theme_adv_photo->desc_allowbbcode->value, TRUE);
        $this->save_item_state("hide_photometa",    !$form->edit_theme_adv_photo->hide_photometa->value, FALSE);

        // * Sidebar Options ****************************************************

        $sidebar_allowed = $form->edit_theme_side->sidebar_allowed->value;
        $sidebar_visible = $form->edit_theme_side->sidebar_visible->value;    

        if ($sidebar_allowed == "right"):
          $sidebar_visible = "right";
        endif;
        if ($sidebar_allowed == "left"):
          $sidebar_visible = "left";
        endif;

        $this->save_item_state("hide_blockheader",  $form->edit_theme_side->hide_blockheader->value, TRUE);
        $this->save_item_state("sidebar_albumonly", $form->edit_theme_side->sidebar_albumonly->value, TRUE);
        $this->save_item_state("sidebar_allowed",   $sidebar_allowed != "any",   $sidebar_allowed);
        $this->save_item_state("sidebar_visible",   $sidebar_visible != "right", $sidebar_visible);

        if (($sidebar_allowed == "none") and ($sidebar_visible == "none")):
          module::set_var("gallery", "page_size", $form->edit_theme->row_count->value * 4);
        else:
          module::set_var("gallery", "page_size", $form->edit_theme->row_count->value * 3);
        endif;

        module::event("theme_edit_form_completed", $form);

        if ($_priorratio != $thumb_ratio):
          message::warning(t("Thumb aspect ratio has been changed. Consider rebuilding thumbs if needed."));
        endif;
  
        message::success(t("Updated theme details"));
      endif;

      url::redirect("admin/theme_options");
    else:
      $view = new Admin_View("admin.html");
      $view->content = $form;
      print $view;
    endif;
  }

  public function index() {
    site_status::clear("gd_init_configuration");

    $view = new Admin_View("admin.html");
    $view->page_title = t("Grey Dragon Theme");
    $view->content = new View("admin_theme_options.html");
    $view->content->name = self::get_theme_name();
    $view->content->version = self::get_theme_version();
    $view->content->form = self::get_edit_form_admin();
    $view->content->help = self::get_edit_form_help();
    print $view;
  }
}
