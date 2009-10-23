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

/**
 * This is the API for handling comments.
 *
 * Note: by design, this class does not do any permission checking.
 */
class theme_3nids_Core {

	public function fancylink($item, $viewtype="album") {
		//viewtype = album || dynamic || header
		$link = "";
		access::required("view", $item);
		

		
		if ($item->is_photo() || ($item->is_movie()) && module::is_active("theme_3nids")){
			$fancymodule = ""; 
			if (module::is_active("exif")){
				$fancymodule .= "exif::" . url::site("exif/show/{$item->id}") . ";;";} 
			if (module::is_active("comment") && module::is_active("theme_3nids")){
				$fancymodule .= "comment::" . url::site("comments_3nids?item_id={$item->id}") . ";;comment_count::" . comment_3nids::count($item) . ";;" ;} 
			if ($item->is_photo()){
				$link .= "<a href=\"" . $item->file_url() ."\" rel=\"fancygroup\" class=\"fancyclass\" title=\"" . $item->parent()->title .", " . $item->parent()->description ."\" name=\"" . $fancymodule  . " \">";
			}else{
				$link .= "<a href=\"" . url::site("movie_3nids?item_id={$item->id}") . "&width=" . $item->width . "endwidth&height=" . $item->height . "endheight\" rel=\"fancygroup\" class=\"fancyclass iframe\" title=\"" . $item->parent()->title .", " . $item->parent()->description ."\" name=\"" . $fancymodule  . " \">";
			}
		} elseif( $item->is_album()  && $viewtype != "header"){
			$link .= "<a href=\"" . $item->url() . "\">";
		}

		if($viewtype != "header"){
			$link .= $item->thumb_img(array("class" => "g-thumbnail")) . "</a>";
			if( $item->is_album()  && $viewtype == "album" ){
				$link .= "<a href=\"" . $item->url() . "?show=" . $item->id . "\"><h2><span></span>" . html::clean($item->title) . "</h2></a>";
			} elseif ( !($item->is_album()) &&  $viewtype == "dynamic")  {
				$link .= "<a href=\"" . $item->parent()->url() . "?show=" . $item->id . "\"><h2><span></span>" . html::clean($item->parent()->title) . "</h2></a>";
			}
			if ( !($item->is_album()) &&  $user->admin) {
				$link .= "<a href=\"" . $item->url() ."\">edit</a>";
			}
			
			if (($item->is_photo() || $item->is_movie()) && module::is_active("comment") && module::is_active("theme_3nids")) {
				$link .= "<ul class=\"g-metadata\"><li><a href=\"" . url::site("comments_3nids?item_id={$item->id}") ."\" class=\"iframe fancyclass\">" . comment_3nids::count($item) . t("comments") . "</a></li></ul>";
			}
		}else{
			$link .= "</a>";
		}
		return $link;
	}





}

?>