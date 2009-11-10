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

	public function fancylink($item, $viewtype="album", $groupImg = true, $displayComment = true, $parentTitleClass = "h2") {
		//viewtype = album || dynamic || header
		$link = "";
		access::required("view", $item);
		
		$photo_size = module::get_var("theme_3nids","photo_size");
		if ($photo_size == "full"){
			$width = $item->width;
			$height = $item->height;
		}else{
			$width = $item->resize_width;
			$height = $item->resize_height;
		}
		
		$desriptionMode = module::get_var("theme_3nids", "description");
		$description = "";
		$tags = tag::item_tags($item);
		if(count($tags) && $desriptionMode == "tags"){
			$description =  " || " . implode(", ", $tags);
		}elseif($desriptionMode == "item" && $item->description != ""){
			$description = " || " . str_replace("\"","&quot;",$item->description);
		}elseif (($desriptionMode == "parent" || $desriptionMode == "item") && $item->parent()->description != ""){
			$description = " || " . str_replace("\"","&quot;",$item->parent()->description);
		}
		
		$titleMode = module::get_var("theme_3nids", "title");
		if ($titleMode == "parent"){
			$title = html::clean($item->parent()->title);
		}else{
			$title = html::clean($item->title);
		}
		
		$rel = "";
		if ($groupImg == true) {$rel = " rel=\"fancygroup\" ";}
		
		if ($item->is_photo() || ($item->is_movie()) && module::is_active("theme_3nids")){
			$fancymodule = ""; 
			if (module::is_active("exif")){
				$fancymodule .= "exif::" . url::site("exif/show/{$item->id}") . ";;";} 
			if (module::is_active("comment") && module::is_active("theme_3nids")){
				$fancymodule .= "comment::" . url::site("comments_3nids?item_id={$item->id}") . ";;comment_count::" . comment_3nids::count($item) . ";;" ;} 
			if ($item->is_photo()){
				$link .= "<a href=\"" . url::site("photo_3nids/show/{$item->id}") ."/?w=" . $width . "xewx&h=" . $height . "xehx\" " . $rel . " class=\"fancyclass iframe\" title=\"" . $title . $description ."\" name=\"" . $fancymodule  . " \">";
			}else{
				$link .= "<a href=\"" . url::site("movie_3nids/show/{$item->id}") . "/?w=" . strval(20+($width)) . "xewx&h=" . strval(50+($height)) . "xehx\" " . $rel . " class=\"fancyclass iframe\" title=\"" . $item->parent()->title . $description ."\" name=\"" . $fancymodule  . " \">";
			}
		} elseif( $item->is_album()  && $viewtype != "header"){
			$link .= "<a href=\"" . $item->url() . "\">";
		}

		if($viewtype != "header"){
			$link .= $item->thumb_img(array("class" => "g-thumbnail")) . "</a>";
			if( $item->is_album()  && $viewtype == "album" ){
				$link .= "<a href=\"" . $item->url() . "?show=" . $item->id . "\"><$parentTitleClass><span></span>" . html::clean($item->title) . "</$parentTitleClass></a>";
			} elseif ( !($item->is_album()) &&  $viewtype == "dynamic")  {
				$link .= "<a href=\"" . $item->parent()->url() . "?show=" . $item->id . "\" class=\"g-parent-album\"><$parentTitleClass><span></span>" . html::clean($item->parent()->title) . "</$parentTitleClass></a>";
			}
			
			if (($item->is_photo() || $item->is_movie()) && $displayComment==true && module::is_active("comment") && module::is_active("theme_3nids")) {
				$link .= "<ul class=\"g-metadata\"><li><a href=\"" . url::site("comments_3nids?item_id={$item->id}") ."\" class=\"iframe fancyclass g-hidden\">" . comment_3nids::count($item) . " " . t("comments") . "</a></li></ul>";
			}
		}else{
			$link .= "</a>";
		}
		return $link;
	}





}

?>