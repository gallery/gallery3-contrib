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

/**
 * This is the API for handling exif data.
 */
class author_Core {
	static function fix($item) {
		if ($item->is_album()) { return false; }
		
		$mime = $item->mime_type;
		if ($mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif') {}
		else { return false; }
		
	    $owner = ORM::factory("user")->where("id", "=", $item->owner_id)->find();
    	$user_name = $owner->full_name;
    	
		$exiv = module::get_var('author', 'exiv_path');
		$version = module::get_var('author', 'exiv_version');
		
		/*
			Debian stable ships with exiv2 0.16 at the time of writing. You get 
			roughly the same output out of the utility as with 0.20, but you have
			to invoke it several times.
			
			The real threshhold for this might be somewhere between 0.16 and 0.20, 
			but the 0.16 way of doing things is forward compatible. 
		*/
		$exivData = array();
		if ($version < 0.20) {
			exec("$exiv -p x " . escapeshellarg($item->file_path()), $exivData);
			exec("$exiv -p i " . escapeshellarg($item->file_path()), $exivData);
			exec("$exiv -p t " . escapeshellarg($item->file_path()), $exivData);
		} else {
			exec("$exiv -p a " . escapeshellarg($item->file_path()), $exivData);
		}
		
		$has = array();
		$mod = array();
		foreach ($exivData as $line)
		{
			$tokens = preg_split('/\s+/', $line, 4);
			$has[ $tokens[0] ] = $tokens[3];
		}
		
		$candidates = array(
			$has['Xmp.dc.creator'],
			$has['Iptc.Application2.Byline'],
			$has['Exif.Image.Artist'],
			$user_name,
			'Unknown');
		
		foreach ($candidates as $cand) {
			if ($cand != '') { $byline = $cand; break; }
		}
		
		if (!array_key_exists('Exif.Image.Artist', $has)) { $mod['Exif.Image.Artist'] = $byline; }
		if (!array_key_exists('Iptc.Application2.Byline', $has)) { $mod['Iptc.Application2.Byline'] = $byline; }

		/* Apply the credit block */
		$credit = module::get_var("author", "credit");
		if ($credit != '') {
			$mod['Iptc.Application2.Credit'] = $credit;
		}

		/*
			Older versions doesn't support XMP writing.
		*/
		if ($version >= 0.20) {
			if (!array_key_exists('Xmp.dc.creator', $has)) { $mod['Xmp.dc.creator'] = $byline; }
			
			/* Apply our own image terms URL */
			$terms = module::get_var("author", "usage_terms");
			if ($terms != '') {
				$mod['Xmp.xmpRights.UsageTerms'] = 'http://wiki.sverok.se/wiki/Bildbank-Bilder';
			}			
		}
		
		$line = $exiv . ' ';
		foreach ($mod as $key => $value) {
			$line .= "-M \"set $key " . escapeshellarg($value) . "\" ";
		}
		
		$files = array(
			$item->file_path(),
			$item->thumb_path(),
			$item->resize_path()
		);
		
		foreach ($files as $file) {
			system("$line " . escapeshellarg($file));
		}
		
		$record = ORM::factory("author_record")->where("item_id", "=", $item->id)->find();
		if (!$record->loaded()) {
			$record->item_id = $item->id;
		}
		$record->author = $byline;
		$record->dirty = 0;
		$record->save();
		return $byline;		
  	}
  	
}
