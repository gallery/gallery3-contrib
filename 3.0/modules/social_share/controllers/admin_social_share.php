<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
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
class Admin_Social_Share_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var("social_share", "general_impage_only", $form->general_settings->general_impage_only->value);
      module::set_var("social_share", "facebook_share_enabled", $form->facebook_share_settings->facebook_share_enabled->value);
      module::set_var("social_share", "facebook_share_layout", $form->facebook_share_settings->facebook_share_layout->value);
      module::set_var("social_share", "facebook_share_link_text", $form->facebook_share_settings->facebook_share_link_text->value);
	  module::set_var("social_share", "facebook_like_enabled", $form->facebook_like_settings->facebook_like_enabled->value);
	  module::set_var("social_share", "facebook_like_appId", $form->facebook_like_settings->facebook_like_appId->value);
      module::set_var("social_share", "facebook_like_adminId", $form->facebook_like_settings->facebook_like_adminId->value);
      module::set_var("social_share", "facebook_like_site_name", $form->facebook_like_settings->facebook_like_site_name->value);
      module::set_var("social_share", "facebook_like_code_type", $form->facebook_like_settings->facebook_like_code_type->value);
      module::set_var("social_share", "facebook_like_show_faces", $form->facebook_like_settings->facebook_like_show_faces->value, true);
      module::set_var("social_share", "facebook_like_send", $form->facebook_like_settings->facebook_like_send->value, true);
      module::set_var("social_share", "facebook_like_action", $form->facebook_like_settings->facebook_like_action->value);
      module::set_var("social_share", "facebook_like_layout", $form->facebook_like_settings->facebook_like_layout->value);
      module::set_var("social_share", "google_enabled", $form->google_settings->google_enabled->value);
      module::set_var("social_share", "google_size", $form->google_settings->google_size->value);
      module::set_var("social_share", "google_annotation", $form->google_settings->google_annotation->value);
      module::set_var("social_share", "pinterest_enabled", $form->pinterest_settings->pinterest_enabled->value);
      module::set_var("social_share", "pinterest_count_location", $form->pinterest_settings->pinterest_count_location->value);
      module::set_var("social_share", "twitter_enabled", $form->twitter_settings->twitter_enabled->value);
      module::set_var("social_share", "twitter_count_location", $form->twitter_settings->twitter_count_location->value);
      module::set_var("social_share", "twitter_size", $form->twitter_settings->twitter_size->value);
      message::success(t("Your settings have been saved."));  
      url::redirect("admin/social_share");
    }

    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_social_share.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_form() {
    $form = new Forge("admin/social_share/handler", "", "post", array("id" => "g-admin-form"));
    
/// General Settings
    $group_general = $form->group("general_settings")->label(t("General Settings"));
    $group_general->checkbox("general_impage_only")->label(t("Display the enabled buttons on image and movie pages only"))
      ->checked(module::get_var("social_share", "general_impage_only", true) == 1);
    
/// Facebook share settings
	$group_facebook_share = $form->group("facebook_share_settings")->label(t("Facebook Share Button Settings"));
	$group_facebook_share->checkbox("facebook_share_enabled")->label(t("Display the button"))
      ->checked(module::get_var("social_share", "facebook_share_enabled", false) == 1);
    $group_facebook_share->dropdown("facebook_share_layout")
      ->label(t("The button layout to use"))
      ->options(array( "button" => t("Button"),
					   "icon_link" => t("Icon and Link"),
					   "icon" => t("Icon")))
      ->selected(module::get_var("social_share", "facebook_share_layout"));
	$group_facebook_share->input("facebook_share_link_text")->label(t('Enter the text to place next to the Facebook icon.'))
		->value(module::get_var("social_share", "facebook_share_link_text", "Share"));

/// Facebook like settings
	$group_facebook_like = $form->group("facebook_like_settings")->label(t("Facebook Like Button Settings"));
	$group_facebook_like->checkbox("facebook_like_enabled")->label(t("Display the button"))
      ->checked(module::get_var("social_share", "facebook_like_enabled", false) == 1);
	$group_facebook_like->input("facebook_like_appId")->label(t('Enter the appId from <a href="http://developers.facebook.com/setup/" target="_blank">FaceBook Create an App</a>.  You must get your *own* appID.  If you see the number "123456789012345" it is only a demo.<br />
										Note: www.example.com/ is different than example.com/'))
		->value(module::get_var("social_share", "facebook_like_appId", "123456789012345"))
		->rules("valid_numeric");
	$group_facebook_like->input("facebook_like_adminId")->label(t('Enter <strong>your</strong Facebook <strong>numeric</strong> ID, which you can get as the "Admin" field in Stage 2 of <a href="http://developers.facebook.com/docs/reference/plugins/like/" target="_blank">FaceBook Like Button Config</a>.  If you see the number "123456789012345" it is only a demo.<br />'))
		->value(module::get_var("social_share", "facebook_like_adminId", "123456789012345"))
		->rules("valid_numeric");
	$group_facebook_like->input("facebook_like_site_name")->label(t('Enter the site name you want to show on Facebook.'))
		->value(module::get_var("social_share", "facebook_like_site_name", "Gallery"));
	$group_facebook_like->dropdown("facebook_like_code_type")
      ->label(t("The type of Ccde to display for the button"))
      ->options(array( "html5" => t("HTML5 (allows the Send button and better dialogs)"),
					   "xfbml" => t("XFBML (allows the Send button and better dialogs)"),
					   "iframe" => t("iFrame")))
      ->selected(module::get_var("social_share", "facebook_like_code_type"));
	$group_facebook_like->checkbox("facebook_like_show_faces")->label(t("Display profile photos of the Facebook friends who 'Like' below the Like button (standard layout only)."))
		->checked(module::get_var("social_share", "facebook_like_show_faces"));
	$group_facebook_like->checkbox("facebook_like_send")->label(t("Include a Send button with the Like button. This only works with the XFBML version."))
		->checked(module::get_var("social_share", "facebook_like_send"));
    $group_facebook_like->dropdown("facebook_like_action")
      ->label(t("Verb to display"))
      ->options(array("like" => t("Like"),
                      "recommend" => t("Recommend")))
      ->selected(module::get_var("social_share", "facebook_like_action"));
	$group_facebook_like->dropdown("facebook_like_layout")
      ->label(t("Layout style. Determines the size and amount of social context next to the button"))
      ->options(array("standard" => t("Standard."),
                      "button_count" => t("Button count"),
					  "box_count" => t("Box count")))
	  ->selected(module::get_var("social_share", "facebook_like_layout"));

/// Google settings
	$group_google = $form->group("google_settings")->label(t("Google+ +1 Button Settings"));
	$group_google->checkbox("google_enabled")->label(t("Display the button"))
      ->checked(module::get_var("social_share", "google_enabled", false) == 1);
    $group_google->dropdown("google_size")
      ->label(t("Size of the button"))
      ->options(array("standard" => t("Standard (24px)"),
                      "small" => t("Small (15px)"),
					  "medium" => t("Medium (20px)"),
                      "tall" => t("Tall (60px)")))
	  ->selected(module::get_var("social_share", "google_size"));
    $group_google->dropdown("google_annotation")
      ->label(t("Annotation location"))
      ->options(array("inline" => t("Inline"),
                      "bubble" => t("Bubble"),
					  "none" => t("None")))
	  ->selected(module::get_var("social_share", "google_annotation"));
	
/// Pinterest settings
    $group_pinterest = $form->group("pinterest_settings")->label(t("Pinterest Pinit Settings"));
	$group_pinterest->checkbox("pinterest_enabled")->label(t("Display the button"))
      ->checked(module::get_var("social_share", "pinterest_enabled", false) == 1);
    $group_pinterest->dropdown("pinterest_count_location")
      ->label(t("Tweet count location"))
      ->options(array("horizontal" => t("Horizontal"),
                      "vertical" => t("Vertical"),
					  "none" => t("None")))
	  ->selected(module::get_var("social_share", "pinterest_count_location"));
    
/// Twitter settings
	$group_twitter = $form->group("twitter_settings")->label(t("Twitter Tweet Settings"));
	$group_twitter->checkbox("twitter_enabled")->label(t("Display the button"))
      ->checked(module::get_var("social_share", "twitter_enabled", false) == 1);
    $group_twitter->dropdown("twitter_count_location")
      ->label(t("Tweet count location"))
      ->options(array("horizontal" => t("Horizontal"),
                      "vertical" => t("Vertical"),
					  "none" => t("None")))
	  ->selected(module::get_var("social_share", "twitter_count_location"));
    $group_twitter->dropdown("twitter_size")
      ->label(t("Size of the button. Large does not work with a vertical count location"))
      ->options(array("medium" => t("Medium"),
                      "large" => t("Large")))
	  ->selected(module::get_var("social_share", "twitter_size"));
    
    $form->submit("submit")->value(t("Save"));
    return $form;
  }
}