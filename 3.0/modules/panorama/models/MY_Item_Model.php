<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
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
class Item_Model extends Item_Model_Core {
  public function resize_img($extra_attrs) {
    $panorama = ORM::factory("panorama")->where("item_id", "=", $this->id)->find();
    if ($panorama->loaded() && $panorama->checked) {
      $swfUrl = url::file("modules/panorama/lib/pan0.swf");
      $panoramaHFOV = $panorama->HFOV;
      $panoramaVFOV = $panorama->VFOV;
      $img_url = $this->file_url();
      return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
        codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\"
        width=\"640\" height=\"480\" title=\"FSPP Panorama Viewer\">
        <param name=\"allowFullScreen\" value=\"true\" />
        <param name=\"movie\" value=\"$swfUrl?panoSrc=$img_url&FOV=40&minFOV=20&maxFOV=40&panHFOV=$panoramaHFOV&panVFOV=$panorama->VFOV\" />
        <param name=\"quality\" value=\"high\" />
        <param name=\"BGCOLOR\" value=\"#AAAAAA\" />
        <embed src=\"$swfUrl?panoSrc=$img_url&FOV=40&minFOV=20&maxFOV=40&panHFOV=$panoramaHFOV&panVFOV=$panoramaVFOV\"
        allowFullScreen=\"true\"
        width=\"640\" height=\"480\" quality=\"high\" 
        pluginspage=\"http://www.macromedia.com/go/getflashplayer\"
        type=\"application/x-shockwave-flash\" bgcolor=\"#DDDDDD\">
        </embed>
        </object>";
    } else {
      return parent::resize_img($extra_attrs);
    }
  }
}
