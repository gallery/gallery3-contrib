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
class social_share_theme_Core {
    static function head($theme) {
        $impageonly = module::get_var("social_share", "general_impage_only");
        $showblock = !$impageonly || ($impageonly && ($theme->page_subtype == "photo") || ($theme->page_subtype == "movie"));

        if ($showblock && $theme->item()) {
            $item = $theme->item();
            $url = $item->thumb_url(true);
            $appId = module::get_var("social_share", "facebook_like_appId");
            $adminId = module::get_var("social_share", "facebook_like_adminId");
            $site_name = module::get_var("social_share", "facebook_like_site_name");
            $selfURL = url::abs_current(true);
            return "\t<meta property=\"og:image\" content=\"$url\"/>
                  <meta property=\"og:title\" content=\"$item->title\"/>
                  <meta property=\"og:type\" content=\"article\"/>
                  <meta property=\"og:url\" content=\"$selfURL\"/>
                  <meta property=\"og:site_name\" content=\"$site_name\"/>
                  <meta property=\"fb:app_id\" content=\"$appId\"/>
                  <meta property=\"fb:admins\" content=\"$adminId\"/>";
        }
    }
}