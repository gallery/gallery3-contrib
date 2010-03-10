<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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

  static function get_edit_form_admin() {
    $form = new Forge("admin/theme_options/save/", "", null, array("id" =>"g-theme-options-form"));

    $group = $form->group("requirements")->label("Prerequisites checklist");
    $group->checkbox("shadowbox")->label(t("Shadowbox module"))
      ->checked((module::is_active("shadowbox")))->disabled(true);

    $file = THEMEPATH . "greydragon/theme.info";
    $theme_info = new ArrayObject(parse_ini_file($file), ArrayObject::ARRAY_AS_PROPS);

    $group = $form->group("edit_theme")->label(t("Grey Dragon Theme") . " - " . t("v.") . $theme_info->version);
    $group->input("row_count")->label(t("Rows per album page"))->id("g-page-size")
      ->rules("required|valid_digit")
      ->value(module::get_var("gallery", "page_size") / 3);

    $group->input("resize_size")->label(t("Resized image size (in pixels)"))->id("g-resize-size")
      ->rules("required|valid_digit")
      ->value(module::get_var("gallery", "resize_size"));
    $group->checkbox("build_resize")->label(t("Mark to build all resizes (from Maintenace page)"))->id("g-build-resize")->value(false);
    $group->checkbox("build_thumbs")->label(t("Mark to build all thumbnails (200x200) (from Maintenace page)"))->id("g-build-thumb")->value(false);

    $group->checkbox("photonav_top")->label(t("Show top photo navigator"))
      ->checked(module::get_var("th_greydragon", "photonav_top"));
    $group->checkbox("photonav_bottom")->label(t("Show bottom photo navigator"))
      ->checked(module::get_var("th_greydragon", "photonav_bottom"));

    $group->dropdown("sidebar_allowed")->label(t("Allowed SideBar Positions"))
      ->options(array("any" => t("Any"), "left" => t("Left"), "right" => t("Right"), "none" => t("None")))
      ->selected(module::get_var("th_greydragon", "sidebar_allowed"));
    $group->dropdown("sidebar_visible")->label(t("Default SideBar Position"))
      ->options(array("right" => t("Right"), "left" => t("Left"), "none" => t("None")))
      ->selected(module::get_var("th_greydragon", "sidebar_visible"));

    $group->input("header_text")->label(t("Header text"))->id("g-header-text")
      ->value(module::get_var("gallery", "header_text"));
    $group->input("footer_text")->label(t("Footer text"))->id("g-footer-text")
      ->value(module::get_var("gallery", "footer_text"));
    $group->checkbox("show_credits")->label(t("Show site credits"))->id("g-footer-text")
      ->checked(module::get_var("gallery", "show_credits"));

    $group->input("copyright")->label(t("Copyright message to display on footer"))->id("g-theme-copyright")
      ->value(module::get_var("th_greydragon", "copyright"));
    $group->input("logo_path")->label(t("URL or path to alternate logo image"))->id("g-site-logo")
      ->value(module::get_var("th_greydragon", "logo_path"));

    module::event("theme_edit_form", $form);

    $group = $form->group("buttons");
    $group->submit("")->value(t("Save"));
    return $form;
  }

  public function index() {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_theme_options.html");
    $view->content->form = self::get_edit_form_admin();
    print $view;
  }

  public function save() {
    access::verify_csrf();

    $form = self::get_edit_form_admin();
    if ($form->validate()) {
      $edit_theme = $form->edit_theme;

      module::set_var("gallery", "page_size", $edit_theme->row_count->value * 3);

      $resize_size  = $edit_theme->resize_size->value;
      $thumb_size   = 200;
      $build_resize = $edit_theme->build_resize->value;
      $build_thumbs = $edit_theme->build_thumbs->value;

      if (module::get_var("gallery", "resize_size") != $resize_size) {
        module::set_var("gallery", "resize_size", $resize_size);
        $build_resize = true;
      }
      if (module::get_var("gallery", "thumb_size") != $thumb_size) {
        module::set_var("gallery", "thumb_size", $thumb_size);
      }

      if ($build_resize) {
        graphics::remove_rule("gallery", "resize", "gallery_graphics::resize");
        graphics::add_rule("gallery", "resize", "gallery_graphics::resize",
          array("width" => $resize_size, "height" => $resize_size, "master" => Image::AUTO), 100);
      }

      if ($build_thumbs) {
        graphics::remove_rule("gallery", "thumb", "gallery_graphics::resize");
        graphics::add_rule("gallery", "thumb", "gallery_graphics::resize",
          array("width" => 200, "height" => 200, "master" => Image::AUTO), 100);
      }

      module::set_var("th_greydragon", "photonav_top",    $edit_theme->photonav_top->value);
      module::set_var("th_greydragon", "photonav_bottom", $edit_theme->photonav_bottom->value);

      $sidebar_allowed = $edit_theme->sidebar_allowed->value;
      $sidebar_visible = $edit_theme->sidebar_visible->value;    

      if ($sidebar_allowed == "none")  { $sidebar_visible = "none"; }
      if ($sidebar_allowed == "right") { $sidebar_visible = "right"; }
      if ($sidebar_allowed == "left")  { $sidebar_visible = "left"; }

      module::set_var("th_greydragon", "sidebar_allowed", $sidebar_allowed);
      module::set_var("th_greydragon", "sidebar_visible", $sidebar_visible);

      module::set_var("gallery", "header_text", $edit_theme->header_text->value);
      module::set_var("gallery", "footer_text", $edit_theme->footer_text->value);
      module::set_var("gallery", "show_credits", $edit_theme->show_credits->value);

      module::set_var("th_greydragon", "copyright", $edit_theme->copyright->value);
      module::set_var("th_greydragon", "logo_path", $edit_theme->logo_path->value);

      module::event("theme_edit_form_completed", $form);

      message::success(t("Updated theme details"));
      url::redirect("admin/theme_options");
    } else {
      $view = new Admin_View("admin.html");
      $view->content = $form;
      print $view;
    }
  }
}

