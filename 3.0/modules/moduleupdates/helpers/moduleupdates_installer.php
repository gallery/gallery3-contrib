<?php defined("SYSPATH") or die("No direct script access.");/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
 

class moduleupdates_installer {

  static function install() {
  
    $version = module::get_version("moduleupdates");
    if ($version < 1) {
      module::set_version("moduleupdates", 8);
      //Remove the ModuleUpdates cache entry 'JIC'
      Cache::instance()->delete("ModuleUpdates");
      //create the blank ModuleUpdates cache entry with an expiration of 0 days
      Cache::instance()->set("moduleupdates_cache", "", array("ModuleUpdates"), null);
      Cache::instance()->set("moduleupdates_cache_updates", "", array("ModuleUpdates"), null);
    }
  }

  static function upgrade($version) {
    module::set_version("moduleupdates", 8);
    //Remove the ModuleUpdates cache entry 'JIC'
    Cache::instance()->delete("ModuleUpdates");
    //Empty the ModuleUpdates cache entry so our new version starts from scratch
    Cache::instance()->set("moduleupdates_cache", "", array("ModuleUpdates"), null);
    Cache::instance()->set("moduleupdates_cache_updates", "", array("ModuleUpdates"), null);
  }

  static function uninstall() {
	
    //Remove the ModuleUpdates cache entry as we remove the module
    Cache::instance()->delete("ModuleUpdates");
    module::delete("moduleupdates");
  }
}