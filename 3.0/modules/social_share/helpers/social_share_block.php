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
class social_share_block_Core {
    static function get_site_list() {
        return array("social_share" => t("Social Share"));
    }

    static function get($block_id, $theme) {
    /// Check if the user wants to show the block on all pages or just the image and movie page types.
        $impageonly = module::get_var("social_share", "general_impage_only");
        $showblock = !$impageonly || ($impageonly && ($theme->page_subtype == "photo") || ($theme->page_subtype == "movie"));
        
        if ($showblock){
            $block = new Block();
            $block->css_id = "g-social-share";
            $block->title = 'Share With Friends';
            $block->content = '';
            if(module::get_var("social_share", "facebook_share_enabled")){
                $block->content .= new View("facebook_share.html");
            }
            if(module::get_var("social_share", "facebook_like_enabled")){
                $block->content .= new View("facebook_like.html");
            }
            if(module::get_var("social_share", "google_enabled")){
                $block->content .= new View("google.html");
            }
            if(module::get_var("social_share", "pinterest_enabled")){
                $block->content .= new View("pinterest.html");
            }
            if(module::get_var("social_share", "twitter_enabled")){
                $block->content .= new View("twitter.html");
            }

            return $block;
        }
    }
}