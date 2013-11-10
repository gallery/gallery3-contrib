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
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA
 */
class ratings_installer {

  static function install() {

/** UNTIL RELEASED LET'S CLEAN UP EVERYTHING **/
$clearit = "";
if($clearit){
Database::instance()->query("DROP TABLE IF EXISTS ratables;");
Database::instance()->query("DROP TABLE IF EXISTS ratings;");
module::clear_all_vars("ratings");
dir::unlink(VARPATH . "modules/ratings");
}
/** ---------------------------------------- **/

    $dbvarpath = VARPATH."modules/ratings";


    if(!is_dir($dbvarpath)){
      @mkdir($dbvarpath);
    }
    
    module::set_version("ratings", 20);
    $iconset = url::file("modules/ratings/vendor/img/stars.png");
    $iconset = preg_replace("/\/index\.php/","",$iconset);

    if(!module::get_var("ratings","iconset")){
      module::set_var("ratings","iconset", $iconset);
    }
    if(!module::get_var("ratings","imageword")){
      module::set_var("ratings","imageword", "star");
    }
    if(!module::get_var("ratings","votestring")){
      module::set_var("ratings","votestring", "vote");
    }
    if(!module::get_var("ratings","castyourvotestring")){
      module::set_var("ratings","castyourvotestring", "Click on a star to cast your vote:");
    }
    if(!module::get_var("ratings","showinsidebar")){
      module::set_var("ratings","showinsidebar", "1");
    }
    if(!module::get_var("ratings","showunderphoto")){
      module::set_var("ratings","showunderphoto", "0");
    }
    if(!module::get_var("ratings","bgcolor")){
      module::set_var("ratings","bgcolor", "#FFFFFF");
    }
    if(!module::get_var("ratings","fillcolor")){
      module::set_var("ratings","fillcolor", "#FF0000");
    }
    if(!module::get_var("ratings","hovercolor")){
      module::set_var("ratings","hovercolor", "#FFA800");
    }
    if(!module::get_var("ratings","votedcolor")){
      module::set_var("ratings","votedcolor", "#0069FF");
    }
    if(!module::get_var("ratings","textcolor")){
      module::set_var("ratings","textcolor", "#000000");
    }
    if(!module::get_var("ratings","regonly")){
      module::set_var("ratings","regonly", "0");
    }

/**
 * Can we have duplicate weights for the modules?  
 * may want to adjust this and copy moduleorder module for reordering...
 */

// get comment block weight, so we can adjust ratings weight - see below
    $commentweight = db::build()
      ->select("weight")
      ->from("modules")
      ->where("name","=","comment")
      ->execute()
      ->current();

// set ratings weight to one below comments so we can display right
// under the photo
    $newratingsweight = $commentweight->weight - 1;
    db::build()
      ->update("modules")
      ->set("weight",$newratingsweight)
      ->where("name", "=", "ratings")
      ->execute();
  }

  static function upgrade($version) {
    if($version < 16) {
      $db = Database::instance();
      $db->query("ALTER TABLE `ratings` ADD `userid` INT( 9 ) NOT NULL AFTER `rating`");
    }
    module::set_version("ratings", $version = 20);
  }
}
