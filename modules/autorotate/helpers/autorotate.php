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
class autorotate {
	static function rotate_item($item) {
		// Only try to rotate photos based on EXIF 
		if ($item->is_photo() && $item->mime_type == "image/jpeg") {
		  require_once(MODPATH . "exif/lib/exif.php");
		  $exif_raw = read_exif_data_raw($item->file_path(), false);
		  if (isset($exif_raw['ValidEXIFData'])) {		
			$orientation = $exif_raw["IFD0"]["Orientation"];
			$degrees = 0;
			if ($orientation == '3: Upside-down') {
				$degrees = 180;
			}
			else if ($orientation == '8: 90 deg CW') {
				$degrees = -90;
			}
			else if ($orientation == '6: 90 deg CCW') {
				$degrees = 90;
			}
			if($degrees) {
				$tmpfile = tempnam(TMPPATH, "rotate");
				gallery_graphics::rotate($item->file_path(), $tmpfile, array("degrees" => $degrees));
				$item->set_data_file($tmpfile);
				$item->save();
				unlink($tmpfile);			
			}
		  }
		}
		return;
	}
}