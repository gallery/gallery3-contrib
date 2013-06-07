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
class movie_overlay_theme_Core {
  static function get_movie_time($item) {
    $ffmpeg = movie::find_ffmpeg();
    if (empty($ffmpeg)) {
      return t("00:00");
    }
    $cmd = escapeshellcmd($ffmpeg) . " -i " . escapeshellarg($item->file_path()) . " 2>&1";
    $result = `$cmd`;
    if (preg_match("/Duration: (\d+):(\d+):(\d+\.\d+)/", $result, $regs)) {
      return 3600 * $regs[1] + 60 * $regs[2] + $regs[3];
    } else if (preg_match("/duration.*?:.*?(\d+)/", $result, $regs)) {
      return $regs[1];
    } else {
      return '00';
    }
  }
  static function head($theme, $child) {
	if ($theme->page_type == "collection") {
	$trans 		= module::get_var("movie_overlay", "trans");
	return "\t<style type=\"text/css\"> 
	.g-movie-thumb {
	  position:relative;
	  margin: auto;
	}
	.g-movie-thumb .g-movie-time {
	  position:absolute;
	  -moz-border-radius: 5px;
	  border-radius: 5px;
	  background-color: #000000;
	  background-color:rgb(0,0,0);
	  background-color:rgba(0,0,0,0.6);
	  filter: alpha(opacity=60);
	  color: #fff;
	  height: 1.5em;
	  font-weight:bold;
	  text-align:right;
	  margin-right: 4px;
	  margin-bottom: 3px;
	  padding: 3px 3px 0px 5px;
	  right:0px;
	}
	.g-movie-thumb .g-description {
	  margin-top: -6px!important;
	  margin-left: -5px!important;
	}
	.g-movie-thumb .g-context-menu {
	  margin-top: -6px!important;
	  margin-left: -5px!important;
	}
	</style>";
	}
  }
  static function thumb_top($theme, $child) {
	if ($child->type == "movie") {
	return ("<div class=\"g-movie-thumb\" style=\"width: {$child->thumb_width}px;\">\n");
	}
  }  
  static function thumb_bottom($theme, $child) {
	if ($child->type == "movie") {
	  $view = new View("movie_thumb_bottom.html");
	  // pass some variable to the view 
	  $view->url = 			$child->url();
	  $view->top = 			round($child->thumb_height / 2 - 20);
	  $view->texttime_top =	round($child->thumb_height - 25);  // position the movie duration from the top as some themes add stuff below the thumb
	  $view->left = 		round($child->thumb_width / 2 - 20);
	  $view->images_url = 	url::file("modules/movie_overlay/images");
	  $view->icon = 		module::get_var("movie_overlay", "icon");
	  $view->trans = 		module::get_var("movie_overlay", "trans");
	  $view->movie_time = 	number_format(movie_overlay_theme_Core::get_movie_time($child), 2);
	  return $view;
	}
  }
  
}
