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
class social_share_installer {
    static function deactivate() {
        module::clear_var("social_share", "general_impage_only");
        module::clear_var("social_share", "facebook_share_enabled");
        module::clear_var("social_share", "facebook_share_layout");
        module::clear_var("social_share", "facebook_share_link_text");
        module::clear_var("social_share", "facebook_like_enabled");
        module::clear_var("social_share", "facebook_like_appId");
        module::clear_var("social_share", "facebook_like_adminId");
        module::clear_var("social_share", "facebook_like_site_name");
        module::clear_var("social_share", "facebook_like_code_type");
        module::clear_var("social_share", "facebook_like_show_faces");
        module::clear_var("social_share", "facebook_like_send");
        module::clear_var("social_share", "facebook_like_action");
        module::clear_var("social_share", "facebook_like_layout");
        module::clear_var("social_share", "google_enabled");
        module::clear_var("social_share", "google_size");
        module::clear_var("social_share", "google_annotation");
        module::clear_var("social_share", "pinterest_enabled");
        module::clear_var("social_share", "pinterest_count_location");
        module::clear_var("social_share", "twitter_enabled");
        module::clear_var("social_share", "twitter_count_location");
        module::clear_var("social_share", "twitter_size");
    }
    static function upgrade($version) {
        if ($version < 1) {
            module::set_version("social_share", $version = 1);
        }
	
        if ($version < 2) {
            module::set_var("social_share", "facebook_share_enabled", module::get_var("social_share", "facebook"));
            module::clear_var("social_share", "facebook");
            module::set_var("social_share", "google_enabled", module::get_var("social_share", "google"));
            module::clear_var("social_share", "google");
            module::set_var("social_share", "twitter_enabled", module::get_var("social_share", "twitter"));
            module::clear_var("social_share", "twitter");
            module::set_version("social_share", $version = 2);
        }
    
        if ($version < 3) {
            module::set_version("social_share", $version = 3);
        }
    }
}