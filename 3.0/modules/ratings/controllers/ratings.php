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
 
class Ratings_Controller extends Controller {
  public function clear($id) {
#    access::verify_csrf();
#    access::required("view", $id);
#    access::required("edit", $id);e
    echo "Would you like to clear all the ratings and votes for this item?<br>";
    echo "<i><font color=#ff6666>This cannot be undone!</font></i><br><br><hr>";
    $form = new Forge("ratings/doClear/{$id}","","post", array("id" => "g-ratings-clear-form"));
    $group = $form->group("clear")->label(t("Clear all ratings/votes"));
    $group->submit("")
      ->value(t("Clear Them!"));
    print $form;
  }
  public function doClear($id) {
    $photo = ORM::factory("item", $id);
    $rateid = "rate".$id;

    $ratable = db::build()
		->select("id")
		->from("ratables")
		->where("ratableKey", "=", $rateid)
		->execute()
		->current();

    if(db::build()
		->select("id")
		->from("ratings")
		->where("ratable_id","=",$ratable->id)
		->execute()
		->count() < 1){
		  message::warning(t("No votes have been registered for this item:  Nothing cleared!"));
		  json::reply(array("result" => "success", "location" => $photo->url()));
		  return;
		}

    $ratings = db::build()
		->delete("ratings")
		->where("ratable_id","=",$ratable->id)
		->execute();


    message::success(t("All ratings and votes for this item have been cleared!"));
    json::reply(array("result" => "success", "location" => $photo->url()));
  }
}
