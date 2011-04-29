<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Grey Dragon Theme - a custom theme for Gallery 3
 * This theme was designed and built by Serguei Dosyukov, whose blog you will find at http://blog.dragonsoft.us
 * Copyright (C) 2009-2011 Serguei Dosyukov
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to
 * the Free Software Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<?    
class Admin_Theme_Options_Controller extends Admin_Controller {

  protected $min_gallery_ver = 46;

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

  private function get_packlist($type, $filename) {
    $packlist = array();
    $packroot = THEMEPATH . 'greydragon/css/' . $type . '/';

    foreach (scandir($packroot) as $pack_name):
      if (file_exists($packroot . "$pack_name/" . $filename . ".css")):
        if ($pack_name[0] == "."):
          continue;
        endif;

        $packlist[$pack_name] = t($pack_name);
      endif;
    endforeach;
    return $packlist;
  }

  private function get_colorpacks() {
    return $this->get_packlist('colorpacks', 'colors');
  }

  private function get_framepacks() {
    return $this->get_packlist('framepacks', 'frame');
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
    if (module::get_var("th_greydragon", "hide_thumbmeta")):
      module::set_var("th_greydragon", "thumb_metamode", "hide");
      module::clear_var("th_greydragon", "hide_thumbmeta");
    endif;

    if (module::get_var("th_greydragon", "flex_rows", FALSE)):
      module::set_var("th_greydragon", "column_count", -1);
      module::clear_var("th_greydragon", "flex_rows");
    endif;

    if (module::get_var("th_greydragon", "thumb_descmode") == "overlay_static"):
      module::set_var("th_greydragon", "thumb_descmode", "overlay_top");
    endif;

    if (module::get_var("th_greydragon", "mainmenu_position") == "1"):
      module::set_var("th_greydragon", "mainmenu_position", "top");
    endif;

    if (module::get_var("th_greydragon", "hide_breadcrumbs")):
      module::set_var("th_greydragon", "breadcrumbs_position", "hide");
      module::clear_var("th_greydragon", "hide_breadcrumbs");
    endif;
  }

  protected function get_edit_form_admin() {
    $this->upgrade_settings();

    $form = new Forge("admin/theme_options/save/", "", null, array("id" =>"g-theme-options-form"));

    $rssmodulecheck = (module::is_active("rss") && module::info("rss"));
    $imageblockexcheck = (module::info("imageblockex"));

    /* Prerequisites ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("requirements")->label(t("Prerequisites"));
    $gallery_ver = module::get_version("gallery");
    $this->prerequisite_check($group, "vercheck", $gallery_ver >= $this->min_gallery_ver, 
      t("Gallery 3 Core v.") . $this->min_gallery_ver . "+", t("Installed"), t("Required"), FALSE, sprintf(t("Check Failed. Minimum Required Version is %s. Found %s."), $this->min_gallery_ver, $gallery_ver));
    if (!module::get_var("th_greydragon", "hide_thumbmeta")):
      $this->prerequisite_check($group, "info", (module::is_active("info") and module::info("info")), 
        t("Info Module"), t("Found"), t("Required"), FALSE, t("Check Failed. Module is required to display Thumb metadata."));
    endif;
    if (module::get_var("th_greydragon", "allow_root_page")):
      $this->prerequisite_check($group, "rsscheck", $rssmodulecheck, 
        t("RSS Module"), t("Found"), t("not Found"), TRUE, t("Install RSS module to Enable Root Page Support"));
      $this->prerequisite_check($group, "imageblockexcheck", $imageblockexcheck, 
        t("ImageBlock Ex Module"), t("Present"), t("not Present"), TRUE, sprintf(t("Install %smodule%s for Root Page Support"), '<a href="http://codex.gallery2.org/Gallery3:Modules:imageblockex" target="_blank">', '</a>'));
    endif;

    /* Suggested Modules ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("recommended")->label(t("Suggested Modules"));

    $check_fb = module::is_active("fancybox") && module::info("fancybox");
    $check_sb = module::is_active("shadowbox") && module::info("shadowbox");
    $sb_fb_module = "fancybox";
    $sb_fb_caption = "Fancybox";
    $sb_fb_link = '<a href="http://codex.gallery2.org/Gallery3:Modules:fancybox" target="_blank">';
    if ($check_fb):
      if ($check_sb):
        $this->prerequisite_check($group, "fancybox", FALSE, 
          t("Both FancyBox and ShadowBox Modules are Active"),
          "",
          "",
          TRUE,
          t("Slideshow feature would work incorrectly. Please activate just one of these modules."));
      else:
        $sb_fb_check = TRUE;
      endif;
    else:
      if ($check_sb):
        $sb_fb_module = "shadowbox";
        $sb_fb_caption = "Shadowbox";
        $sb_fb_link = '<a href="http://codex.gallery2.org/Gallery3:Modules:shadowbox" target="_blank">';
        $sb_fb_check = TRUE;
      else:
        $sb_fb_check = FALSE;
      endif;
    endif;

    if (isset($sb_fb_check)):
      $this->prerequisite_check($group, $sb_fb_module, $sb_fb_check, 
        t($sb_fb_caption) . " " . t("Module"), t("Found"), t("not Found"), TRUE, sprintf(t("Install %smodule%s to Enable %s Support"), $sb_fb_link, '</a>', t($sb_fb_caption)));
    endif;

    $thumbnavcheck = module::is_active("thumbnav") and module::info("thumbnav");
    $iptccheck = module::is_active("iptc") and module::info("iptc");

    $this->prerequisite_check($group, "kbdnavcheck", ((module::is_active("kbd_nav")) and (module::info("kbd_nav"))), 
      t("Kbd Navigation Module"), t("Found"), t("not Found"), TRUE, sprintf(t("Install %smodule%s to Enable Keyboard Navigation Support"), '<a href="http://codex.gallery2.org/Gallery3:Modules:kbd_nav" target="_blank">', '</a>'));
    $this->prerequisite_check($group, "thumbnavcheck", $thumbnavcheck, 
      t("ThumbNav Module"), t("Found"), t("not Found"), TRUE, sprintf(t("Install %smodule%s to Enable Thumb Navigation Support"), '<a href="http://codex.gallery2.org/Gallery3:Modules:thumbnav" target="_blank">', '</a>'));
    if (!module::get_var("th_greydragon", "allow_root_page")):
      $this->prerequisite_check($group, "rsscheck", $rssmodulecheck, 
        t("RSS Module"), t("Found"), t("not Found"), TRUE, t("Install RSS module to Enable Root Page Support"));
    endif;

    $thumb_ratio = module::get_var("th_greydragon", "thumb_ratio", "photo");
    $thumb_ratio_ex = FALSE;
    switch ($thumb_ratio):
      case "photo_ex":
        $thumb_ratio = "photo";
        $thumb_ratio_ex = TRUE;
        break;
      case "film_ex":
        $thumb_ratio = "film";
        $thumb_ratio_ex = TRUE;
        break;
      case "digital_ex":
        $thumb_ratio = "digital";
        $thumb_ratio_ex = TRUE;
        break;
      case "wide_ex":
        $thumb_ratio = "wide";
        $thumb_ratio_ex = TRUE;
        break;
      default:
        break;
    endswitch;

    /* General Settings ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $sidebar_allowed = module::get_var("th_greydragon", "sidebar_allowed");
    $sidebar_visible = module::get_var("th_greydragon", "sidebar_visible");

    $group = $form->group("edit_theme")->label(t("General Settings"));
    $group->input("row_count")
      ->label(t("Rows per Album Page"))
      ->rules("required|valid_digit")
      ->error_messages("required", t("You must enter a number"))
      ->error_messages("valid_digit", t("You must enter a number"))
      ->value(module::get_var("th_greydragon", "row_count", 3));
    $group->dropdown("column_count")
      ->label(t("Columns per Album Page"))
      ->options(array("2" => t("2 columns"), "3" => t("3 columns"), "4" => t("4 columns"), "5" => t("5 columns"), "-1" => t("Flexible (3 x Number of Rows)")))
      ->selected(module::get_var("th_greydragon", "column_count", 3));
    $group->input("resize_size")
      ->label(t("Resized Image Size (in pixels)"))
      ->rules("required|valid_digit")
      ->error_messages("required", t("You must enter a number"))
      ->error_messages("valid_digit", t("You must enter a number"))
      ->value(module::get_var("gallery", "resize_size"));
    $group->input("logo_path")
      ->label(t("Alternate Logo Image"))
      ->value(module::get_var("th_greydragon", "logo_path"));
    $group->input("favicon")
      ->label(t("URL (or relative path) to your favicon.ico"))
      ->value(module::get_var("gallery", "favicon_url"));
    $group->input("appletouchicon")
      ->label(t("URL (or relative path) to your apple-touch-icon.png"))
      ->value(module::get_var("gallery", "appletouchicon_url"));
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
    $group->dropdown("framepack")
      ->label(t("Selected Photo Frame Pack"))
      ->options(self::get_framepacks())
      ->selected(module::get_var("th_greydragon", "frame_pack", "greydragon"));

    /* Advanced Options - General ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_adv_main")->label(t("Advanced Options - General"));
    $group->checkbox("show_credits")
      ->label(t("Show Site Credits"))
      ->checked(module::get_var("gallery", "show_credits"));
    $group->checkbox("loginmenu_position")
      ->label(t("Place Login Link in the Header"))
      ->checked(module::get_var("th_greydragon", "loginmenu_position") == "header");

    $group->dropdown("mainmenu_position")
      ->label(t("Main Menu Position"))
      ->options(array("default" => t("Bottom-Left (Default)"), "top" => t("Top-Left"), "bar" => t("Top Bar")))
      ->selected(module::get_var("th_greydragon", "mainmenu_position"));
    $group->checkbox("show_guest_menu")
      ->label(t("Show Main Menu for Guest Users"))
      ->checked(module::get_var("th_greydragon", "show_guest_menu"));
    $group->dropdown("breadcrumbs_position")
      ->label(t("Breadcrumbs Position"))
      ->options(array("default" => t("Bottom-Right (Default)"), "bottom-left" => t("Bottom-Left"), "top-right" => t("Top-Right"), "top-left" => t("Top-Left"), "hide" => t("Hide")))
      ->selected(module::get_var("th_greydragon", "breadcrumbs_position"));
    $group->checkbox("breadcrumbs_showinroot")
      ->label(t("Show Breadcrumbs in root album/root page"))
      ->checked(module::get_var("th_greydragon", "breadcrumbs_showinroot"));
    $group->dropdown("photonav_position")
      ->label(t("Item Navigator Position"))
      ->options(array("top" => t("Top"), "bottom" => t("Bottom"), "both" => t("Both"), "none" => t("None")))
      ->selected(module::get_var("th_greydragon", "photonav_position"));
    $group->input("custom_css_path")
      ->label(t("Relative path to custom.css (optional)"))
      ->value(module::get_var("th_greydragon", "custom_css_path"));
    $group->checkbox("blendpagetrans")
      ->label(t("Blend Page Transition"))
      ->checked(module::get_var("th_greydragon", "blendpagetrans"));
    $group->checkbox("disable_seosupport")
      ->label(t("Disallow Search Engine Indexing (No Bots)"))
      ->checked(module::get_var("th_greydragon", "disable_seosupport"));

    /* Advanced Options - Album page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_adv_album")->label(t("Advanced Options - Albums"));
    $group->dropdown("album_descmode")
      ->label(t("Album Description Display Mode"))
      ->options(array("hide" => t("Hide"), "top" => t("Top"), "bottom" => t("Bottom")))
      ->selected(module::get_var("th_greydragon", "album_descmode"));

    /* Advanced Options - Album page - Thumbs ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_adv_thumb")->label(t("Advanced Options - Album page - Thumbs"));
    $group->dropdown("thumb_ratio")
      ->label(t("Aspect Ratio"))
      ->options(array("photo" => t("Actual"), "film" => t("Film/Full Frame 3:2"), "digital" => t("Digital 4:3"), "wide" => t("Wide/HDTV 16:9")))
      ->selected($thumb_ratio);
    $group->checkbox("thumb_ratio_ex")
      ->label(t("Expanded Aspect Ratio (300px wide)"))
      ->checked($thumb_ratio_ex);
    $group->dropdown("thumb_descmode_a")
      ->label(t("Title Display Mode (Album)"))
      ->options(array("overlay" => t("Overlay Top"), "overlay_top" => t("Overlay Top (Static)"), 
          "overlay_bottom" => t("Overlay Bottom (Static)"), "bottom" => t("Bottom"), "hide" => t("Hide")))
      ->selected(module::get_var("th_greydragon", "thumb_descmode_a"));
    $group->dropdown("thumb_descmode")
      ->label(t("Title Display Mode (Photo)"))
      ->options(array("overlay" => t("Overlay Top"), "overlay_top" => t("Overlay Top (Static)"), 
          "overlay_bottom" => t("Overlay Bottom (Static)"), "bottom" => t("Bottom"), "hide" => t("Hide")))
      ->selected(module::get_var("th_greydragon", "thumb_descmode"));
    $group->dropdown("thumb_metamode")
      ->label(t("Meta Data Display Mode"))
      ->options(array("default" => t("Overlay (Default)"), "merged" => t("Merge with Title"), "hide" => t("Hide")))
      ->selected(module::get_var("th_greydragon", "thumb_metamode", "default"));
    $group->checkbox("thumb_random")
      ->label(t("Randomize Thumb Image"))
      ->checked(module::get_var("th_greydragon", "thumb_random"));
    $group->checkbox("thumb_topalign")
      ->label(t("Top Align Thumb Image"))
      ->checked(module::get_var("th_greydragon", "thumb_topalign"));

    /* Advanced Options - Photo page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_adv_photo")->label(t("Advanced Options - Photo Page"));
    $group->dropdown("photo_popupbox")
      ->label(t($sb_fb_caption) . " " . t("Mode"))
      ->options(array("default" => t("Default (Slideshow/Preview)"), "preview" => t("Preview Only"), "none" => t("Disable")))
      ->selected(module::get_var("th_greydragon", "photo_popupbox"));
    $group->dropdown("photo_descmode")
      ->label(t("Description Display Mode"))
      ->options(array("overlay_top" => t("Overlay Top"), "overlay_bottom" => t("Overlay Bottom"), "bottom" => t("Bottom"), "top" => t("Top"), "hide" => t("Hide")))
      ->selected(module::get_var("th_greydragon", "photo_descmode"));
    $group->checkbox("thumb_inpage")
      ->label(t("Keep Thumb Nav Block on the side"))
      ->checked(module::get_var("th_greydragon", "thumb_inpage"));
    if (!$thumbnavcheck):
      $group->thumb_inpage->disabled(true);
    endif;
    $group->checkbox("hide_photometa")
      ->label(t("Hide Item Meta Data"))
      ->checked(module::get_var("th_greydragon", "hide_photometa", TRUE));
    $group->checkbox("desc_allowbbcode")
      ->label(t("Allow BBCode/HTML in Descriptions"))
      ->checked(module::get_var("th_greydragon", "desc_allowbbcode"));

    /* Advanced Options - Root Page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_adv_root")->label(t("Advanced Options - Root Page"));
    $group->checkbox("allow_root_page")
      ->label(t("Allow root page (Note: ImageBlock Ex Module need to be installed)"))
      ->checked(module::get_var("th_greydragon", "allow_root_page"));
    $group->checkbox("show_root_desc")
      ->label(t("Show Gallery Description"))
      ->checked(!module::get_var("th_greydragon", "hide_root_desc"));
    $group->input("root_feed")
      ->label(t("Slideshow RSS Feed URL"))
      ->value(module::get_var("th_greydragon", "root_feed", "/gallery3/index.php/rss/feed/gallery/latest"));
    $group->input("root_delay")
      ->label(t("Slideshow Delay (Default: 15)"))
      ->rules("required|valid_digit")
      ->error_messages("required", t("You must enter a number"))
      ->error_messages("valid_digit", t("You must enter a number"))
      ->value(module::get_var("th_greydragon", "root_delay", "15"));
    $group->checkbox("hide_root_sidebar")
      ->label(t("Hide Sidebar"))
      ->checked(module::get_var("th_greydragon", "hide_root_sidebar"));
    $group->textarea("root_description")
      ->label(t("Alternative Description (optional)"))
      ->value(module::get_var("th_greydragon", "root_description"));

    /* Sidebar Options ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("edit_theme_side")->label(t("Sidebar Options"));
    $group->checkbox("hide_blockheader")
      ->label(t("Hide Block Header"))
      ->checked(module::get_var("th_greydragon", "hide_blockheader"));
    $group->checkbox("sidebar_albumonly")
      ->label(t("Show Sidebar for Albums Only"))
      ->checked(module::get_var("th_greydragon", "sidebar_albumonly"));
    $group->dropdown("sidebar_allowed")
      ->label(t("Allowed Sidebar Positions"))
      ->options(array("any" => t("Any"), "left" => t("Left"), "right" => t("Right"), "bottom" => t("Bottom"), "top" => t("Top"), "none" => t("Default Only")))
      ->selected($sidebar_allowed);
    $group->dropdown("sidebar_visible")
      ->label(t("Default Sidebar Position"))
      ->options(array("right" => t("Right"), "left" => t("Left"), "bottom" => t("Bottom"), "top" => t("Top"), "none" => t("No sidebar")))
      ->selected($sidebar_visible);

    /* Maintenance ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("maintenance")->label(t("Maintenance"));
    $group->checkbox("build_resize")->label(t("Mark all Image Resizes for Rebuild"))->checked(false);
    $group->checkbox("build_thumbs")->label(t("Mark all Thumbnails for Rebuild"))->checked(false);
    $group->checkbox("build_exif")->label(t("Mark Exif Info data for reload"))->checked(false);
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
    $help .= '<li><h3>Prerequisites</h3>
      <p style="color: red;">Requirements need to be met for theme to function properly.
      </li>';

    $help .= '<li><h3>General Settings</h3>
      <p>Theme is designed to display thumbnails as a table.
        You can choose different number of <b>Rows/Columns per Album Page</b> or can use <b>Flexible Columns</b>.
      <p>Default G3 logo can be replaced with your own by providing <b>Alternate Logo Image</b>. Recommended logo size
        is within 300x80px. If you need bigger space for your logo, CSS would have to be adjusted. Otherwise, Logo could be
        suppressed altogether by providing <b>Header Text</b> to replace it. <b>Footer Text</b> would be simply placed next
        to Site\'s credits.
      <p><b>Copyright Message</b> can be placed in the right top corner of the footer.
      <p>Important feature of the theme is a support for Color Packs which is managed by <b>Selected Color Pack</b> Option.
      <b>Color Pack</b> is a small CSS file defining color rules for the theme. By default theme comes with GreyDragon
      (default), Carbon and Wind sets, but it could be easily extended. Visit Theme\'s Download page for additional
      information.
      </li>';

    $help .= '<li><h3>Advanced Options - General</h3>
      <p>Show your appreciation for our hard work by allowing <b>Show Site Credits</b> or contributing to our Coffee Fund.
      <p>If main menu has functionality intended for guest users you can use <b>Show Main Menu for Guest Users</b> to keep it
        visible.
      <br />If you do not like login link in the footer you can move it into top right corner by selecting <b>Place Login Link
        in the Header</b>.
      <br />You can go even further and move main menu to the top of the header with breadcrumbs taking it place by selecting
        <b>Alternate Header Layout</b>. <br />Then if you prefer breadcrumbs not being used, simply hide it with <b>Hide
        Breadcrumbs</b> option.
      <br />Then you can decide if you want to <b>Show Breadcrumbs</b> in the root album/root page.
      <p>If you like to add some cool effects while navigating between pages,
        enable <b>Blend Page Transition</b>.
      <p><b>Item Navigator Position</b> could be changed to display it above and/or below the main content. 
      <p>If there is a need to provide additional CSS rules on top of the theme, <b>Relative path to custom.css</b> can be
        specified.
      <p>Block web crawlers from indexing your Gallery with <b>Disallow Search Engine Indexing</b> Option.
      </li>';
    $help .= '<li><h3>Advanced Options - Album page - Thumbs</h3>
      <p>Options in this section adjust how Photo\'s Thumb images are displayed.
      <p><b>Aspect Ratio</b> specifies layout/size of the thumb. Setting should be used with understanding that some
        information may be out of visible area (crop). When switching to/from <b>Actual Size</b>, it is recommended to rebuild
        thumbs so that proper settings are used for thumb resize logic (see Maintenance section below). Combined with
        <b>Expanded Aspect Ratio (300px wide)</b> you can switch between 200px and 300px wide thumb images.
      <p><b>Title Display Mode</b> and <b>Meta Data Display Mode</b> allows changing how Item\'s caption and Meta Data is
        displayed. And selecting <b>Merge with Title</b> would place meta data with the Title.
      <p>Randomize your thumbs by enabling <b>Randomize Thumb Image</b> (Please use with caution as it does introduce extra
        load for database.).
      </li>';
    $help .= '<li><h3>Advanced Options - Photo Page</h3>
      <p>Options in this section adjust how individual Photo are presented to the viewer.
      <p>With <b>ShadowBox Mode</b>, theme\'s logic could be adjusted in how Slideshow module is integrated.
      <br />As with Title in Photo Thumbs, <b>Description Display Mode</b> changes how Photo Description is displayed.
      <br />You can choose to <b>Keep Thumb Nav Block on the side</b> of the Photo when Page is displayed with bottom aligned
        or hidden Sidebar.
      <br />And if metadata (owner/clicks/etc.) is unnecessary, it could be removed with <b>Hide Item Meta Data</b>.
      <p>Theme allows use of BBCode/HTML in item\'s descriptions, to enable it select <b>Allow BBCode/HTML in Descriptions</b>.
      </li>';
    $help .= '<li><h3>Advanced Options - Root Page</h3>
      <p>Special option which allows adding special root/landing page with slideshow utilizing specified <b>Slideshow RSS Feed
        URL</b> (Default: /gallery3/index.php/rss/feed/gallery/latest).
      <p>To enable it, select <b>Allow root page</b>.
      <p>Add small description on the side of the slideshow with <b>Show Gallery Description</b>.
      <br />Adjust rotation delay with <b>Slideshow Delay</b>.
      <br />If Sidebar is not desired in the Root Page it could be hidden with <b>Hide Sidebar</b> option.
      <br />By default, description content is populated from description of the root album, but by providing <b>Alternative
        Description</b> you can overwrite it.
      </li>';
    $help .= '<li><h3>Sidebar Options</h3>
      <p>If Block\'s header is not desired, it could be removed using <b>Hide Block Header</b>.
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
      <p><b>Mark Exif/IPTC Info for reload</b> would mark all Exif or IPTC records as "Dirty" allowing it to be repopulated.
      <p>And just in case you think that something is not right, you can always <b>Reset Theme to a Default State</b>.
      </li>';
    $help .= '</ul></fieldset>';
    return t($help);
  }

  private function save_item_state($statename, $state, $value) {
    if ($state):
      module::set_var("th_greydragon", $statename, $value);
    else:
      module::clear_var("th_greydragon", $statename);
    endif;
  }

  protected function legacy() {
    module::clear_var("th_greydragon", "photonav_top");
    module::clear_var("th_greydragon", "photonav_bottom");
    module::clear_var("th_greydragon", "hide_sidebar_photo");
    module::clear_var("th_greydragon", "hide_thumbdesc");
    module::clear_var("th_greydragon", "use_detailview");
    module::clear_var("th_greydragon", "horizontal_crop");
    module::clear_var("th_greydragon", "photo_shadowbox");
    module::clear_var("th_greydragon", "root_text");
    module::clear_var("th_greydragon", "enable_pagecache");
  }

  protected function reset_theme() {
    // Default core theme settings
    module::set_var("gallery", "page_size", 9);
    module::set_var("gallery", "resize_size", 640);
    module::set_var("gallery", "thumb_size", 200);
    module::set_var("gallery", "header_text", "");
    module::set_var("gallery", "footer_text", "");
    module::set_var("gallery", "show_credits", FALSE);

    module::clear_all_vars("th_greydragon");
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

        $resize_size  = $form->edit_theme->resize_size->value;

        $build_resize = $form->maintenance->build_resize->value;
        $build_thumbs = $form->maintenance->build_thumbs->value;
        $build_exif   = $form->maintenance->build_exif->value;
        if (module::is_active("iptc") and module::info("iptc")):
          $build_iptc   = $form->maintenance->build_iptc->value;
        else:
          $build_iptc = FALSE;
        endif;
        $purge_cache  = $form->maintenance->purge_cache->value;

        $color_pack = $form->edit_theme->colorpack->value;
        $frame_pack = $form->edit_theme->framepack->value;
        $thumb_descmode_a = $form->edit_theme_adv_thumb->thumb_descmode_a->value;
        $thumb_descmode = $form->edit_theme_adv_thumb->thumb_descmode->value;
        $thumb_metamode = $form->edit_theme_adv_thumb->thumb_metamode->value;
        $photo_descmode = $form->edit_theme_adv_photo->photo_descmode->value;
        $photo_popupbox = $form->edit_theme_adv_photo->photo_popupbox->value;

        if ($build_resize):
          graphics::remove_rule("gallery", "resize", "gallery_graphics::resize");
          graphics::add_rule("gallery", "resize", "gallery_graphics::resize",
            array("width" => $resize_size, "height" => $resize_size, "master" => Image::AUTO), 100);
        endif;

        if (module::get_var("gallery", "resize_size") != $resize_size):
          module::set_var("gallery", "resize_size", $resize_size);
        endif;

        $_priorratio = module::get_var("th_greydragon", "thumb_ratio", "photo");
        $thumb_ratio = $form->edit_theme_adv_thumb->thumb_ratio->value;
        $thumb_ratio_ex = $form->edit_theme_adv_thumb->thumb_ratio_ex->value;
        if ($thumb_ratio_ex):
          $thumb_ratio .= "_ex";
        endif;

        if ($thumb_ratio_ex):
          $thumb_size   = 300;  
        else:
          $thumb_size   = 200;
        endif;

        if ($thumb_ratio == "photo"):
          $rule = Image::AUTO;
        else:
          $rule = Image::WIDTH;
        endif;

        if ($build_thumbs):
          graphics::remove_rule("gallery", "thumb", "gallery_graphics::resize");
          graphics::add_rule("gallery", "thumb", "gallery_graphics::resize",
            array("width" => $thumb_size, "height" => $thumb_size, "master" => $rule), 100);
        endif;

        if (module::get_var("gallery", "thumb_size") != $thumb_size):
          module::set_var("gallery", "thumb_size", $thumb_size);
        endif;

        $row_count    = $form->edit_theme->row_count->value;
        $column_count = $form->edit_theme->column_count->value;
        $this->save_item_state("row_count", 3, $row_count);
        $this->save_item_state("column_count", 3, $column_count);
        if ($column_count == -1):
          $column_count = 3;
        endif;
        module::set_var("gallery", "page_size", $row_count * $column_count);
        module::set_var("gallery", "header_text", $form->edit_theme->header_text->value);
        module::set_var("gallery", "footer_text", $form->edit_theme->footer_text->value);
        module::set_var("gallery", "favicon_url", $form->edit_theme->favicon->value);
        module::set_var("gallery", "appletouchicon_url", $form->edit_theme->appletouchicon->value);

        $this->save_item_state("copyright", $form->edit_theme->copyright->value, $form->edit_theme->copyright->value);
        $this->save_item_state("logo_path", $form->edit_theme->logo_path->value, $form->edit_theme->logo_path->value);
        $this->save_item_state("color_pack", (($color_pack) and ($color_pack != "greydragon")), $color_pack);
        $this->save_item_state("frame_pack", (($frame_pack) and ($frame_pack != "greydragon")), $frame_pack);

        // * Advanced Options - General ******************************************

        module::set_var("gallery", "show_credits",      $form->edit_theme_adv_main->show_credits->value);
        $this->save_item_state("show_guest_menu",       $form->edit_theme_adv_main->show_guest_menu->value, TRUE);
        $this->save_item_state("loginmenu_position",    $form->edit_theme_adv_main->loginmenu_position->value == "1", "header");
        $this->save_item_state("mainmenu_position",     $form->edit_theme_adv_main->mainmenu_position->value != "default", $form->edit_theme_adv_main->mainmenu_position->value);
        $this->save_item_state("breadcrumbs_position",  $form->edit_theme_adv_main->breadcrumbs_position->value != "default", $form->edit_theme_adv_main->breadcrumbs_position->value);
        $this->save_item_state("breadcrumbs_showinroot",$form->edit_theme_adv_main->breadcrumbs_showinroot->value, TRUE);
        $this->save_item_state("photonav_position",     $form->edit_theme_adv_main->photonav_position->value != "top", $form->edit_theme_adv_main->photonav_position->value);
        $this->save_item_state("custom_css_path",       $form->edit_theme_adv_main->custom_css_path->value != "", $form->edit_theme_adv_main->custom_css_path->value);
        $this->save_item_state("blendpagetrans",        $form->edit_theme_adv_main->blendpagetrans->value, TRUE);
        $this->save_item_state("disable_seosupport",    $form->edit_theme_adv_main->disable_seosupport->value, TRUE);

        // * Advanced Options - Album page ***************************************

        $this->save_item_state("album_descmode",     $form->edit_theme_adv_album->album_descmode->value != "hide", $form->edit_theme_adv_album->album_descmode->value);

        $this->save_item_state("thumb_ratio",       $thumb_ratio != "photo", $thumb_ratio);
        $this->save_item_state("thumb_descmode_a",  $thumb_descmode_a != "overlay", $thumb_descmode_a);
        $this->save_item_state("thumb_descmode",    $thumb_descmode != "overlay", $thumb_descmode);
        $this->save_item_state("thumb_metamode",    $thumb_metamode != "default", $thumb_metamode);
        $this->save_item_state("thumb_random",      $form->edit_theme_adv_thumb->thumb_random->value, TRUE);
        $this->save_item_state("thumb_topalign",    $form->edit_theme_adv_thumb->thumb_topalign->value, TRUE);

        // * Advanced Options - Photo page ***************************************
        
        $this->save_item_state("photo_descmode",   $photo_descmode != "overlay_top", $photo_descmode);
        $this->save_item_state("photo_popupbox",   $photo_popupbox != "default", $photo_popupbox);
        $this->save_item_state("thumb_inpage",     $form->edit_theme_adv_photo->thumb_inpage->value, TRUE);
        $this->save_item_state("hide_photometa",   !$form->edit_theme_adv_photo->hide_photometa->value, FALSE);
        $this->save_item_state("desc_allowbbcode", $form->edit_theme_adv_photo->desc_allowbbcode->value, TRUE);

        // * Advanced Options - Root page ****************************************

        $imageblockexcheck = module::info("imageblockex");
        $rssmodulecheck = module::is_active("rss") and module::info("rss");

        $root_feed = $form->edit_theme_adv_root->root_feed->value;
        $this->save_item_state("allow_root_page", $form->edit_theme_adv_root->allow_root_page->value, TRUE);
        $this->save_item_state("hide_root_desc",  !$form->edit_theme_adv_root->show_root_desc->value, TRUE);
        $this->save_item_state("root_feed", $root_feed != "gallery/latest", $root_feed);
        $this->save_item_state("root_delay", $form->edit_theme_adv_root->root_delay->value != "15", $form->edit_theme_adv_root->root_delay->value);
        $this->save_item_state("hide_root_sidebar", $form->edit_theme_adv_root->hide_root_sidebar->value, TRUE);
        $this->save_item_state("root_description", $form->edit_theme_adv_root->root_description->value, $form->edit_theme_adv_root->root_description->value);

        // * Sidebar Options *****************************************************

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

        module::event("theme_edit_form_completed", $form);

        if ($_priorratio != $thumb_ratio):
          message::warning(t("Thumb aspect ratio has been changed. Consider rebuilding thumbs if needed."));
        endif;

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
    $view->page_title = t("Grey Dragon Theme");
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