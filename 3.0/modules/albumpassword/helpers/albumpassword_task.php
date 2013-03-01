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
class albumpassword_task_Core {
  static function available_tasks() {
    // Check for any albums listed in albumpasswords but not idcaches.
    //  If found, set the severity for this task to warning, as there's 
    //  obviously something missing from idcaches.
    $bad_albums = ORM::factory("items_albumpassword")
      ->join("albumpassword_idcaches", "items_albumpasswords.id", "albumpassword_idcaches.password_id", "LEFT OUTER")
      ->and_where("albumpassword_idcaches.password_id", "IS", NULL)->count_all();

    $tasks = array();

    $tasks[] = Task_Definition::factory()
               ->callback("albumpassword_task::update_idcaches")
               ->name(t("Rebuild Album Password ID Caches DB"))
               ->description(t("Logs the contents of all protected albums into the db."))
               ->severity($bad_albums ? log::WARNING : log::SUCCESS);

    $tasks[] = Task_Definition::factory()
               ->callback("albumpassword_task::lowercase_passwords")
               ->name(t("Fix Password DB Casing"))
               ->description(t("Fixes case sensitivity issues."))
               ->severity(log::SUCCESS);

    return $tasks;
  }

  static function lowercase_passwords($task) {
    // Converts all passwords to lower case.

    $start = microtime(true);
    $total = $task->get("total");
    $existing_passwords = ORM::factory("items_albumpassword")->find_all();

    if (empty($total)) {
      // Set the initial values for all variables.
      $task->set("total", count($existing_passwords));
      $total = $task->get("total");
      $task->set("last_password_id", 0);
      $task->set("completed_passwords", 0);
    }

    // Retrieve the values for variables from the last time this
    //  function was run.
    $last_password_id = $task->get("last_password_id");
    $completed_passwords = $task->get("completed_passwords");

    foreach (ORM::factory("items_albumpassword")
             ->where("id", ">", $last_password_id)
             ->order_by("id")
             ->find_all(100) as $one_password) {
      $one_password->password = strtolower($one_password->password);
	  $one_password->save();

      $last_password_id = $one_password->id;
      $completed_passwords++;

      if ($completed_passwords == count($existing_passwords) || microtime(true) - $start > 1.5) {
        break;
      }
    }
	
    $task->set("last_password_id", $last_password_id);
    $task->set("completed_passwords", $completed_passwords);

    if ($completed_passwords == count($existing_passwords)) {
      $task->done = true;
      $task->state = "success";
      $task->percent_complete = 100;
    } else {
      $task->percent_complete = round(100 * $completed_passwords / count($existing_passwords));
    }
    $task->status = t2("One password fixed", "%count / %total passwords fixed", $completed_passwords,
                       array("total" => count($existing_passwords)));
  }

  static function update_idcaches($task) {
    // Populate the idcaches table with the contents of all protected albums.

    $start = microtime(true);
    $total = $task->get("total");
    $existing_passwords = ORM::factory("items_albumpassword")->find_all();
    // If this is the first time this function has been run, 
    //  delete and re-create the idcaches table, and set up 
    //  some initial variables.
    if (empty($total)) {
      // Delete the idcache table and make a new one.
      $db = Database::instance();
      $db->query("DROP TABLE IF EXISTS {albumpassword_idcaches};");
      $db->query("CREATE TABLE IF NOT EXISTS {albumpassword_idcaches} (
                 `cache_id` int(9) NOT NULL auto_increment,
                 `password_id` int(9) NOT NULL,
                 `item_id` int(9) NOT NULL,
                 PRIMARY KEY (`cache_id`))
                 DEFAULT CHARSET=utf8;");

      // Set the initial values for all variables.
      $task->set("total", count($existing_passwords));
      $total = $task->get("total");
      $task->set("last_album_counter", 0);
      $task->set("last_id", 0);
      $task->set("completed_albums", 0);
      $task->set("completed_items", 0);
      $task->set("total_items", 0);
    }

    // Retrieve the values for variables from the last time this
    //  function was run.
    $last_album_counter = $task->get("last_album_counter");
    $completed_albums = $task->get("completed_albums");
    $completed_items = $task->get("completed_items");
    $total_items = $task->get("total_items");
    $last_id = $task->get("last_id");

    // If completed_items is 0, then we're just starting to process this 
    //  album.  Add the album to idcaches before adding it's contents.
    if ($completed_items == 0) {
      // Add the album to the id cache.
      $cached_album = ORM::factory("albumpassword_idcache");
      $cached_album->password_id = $existing_passwords[$last_album_counter]->id;
      $cached_album->item_id = $existing_passwords[$last_album_counter]->album_id;
      $cached_album->save();
		
      // Set total_items to the number of items in this album.
      $total_items = ORM::factory("item", $existing_passwords[$last_album_counter]->album_id)
             ->descendants_count();
      $task->set("total_items", $total_items);
    }

    // Add each item in the album to idcaches.			   
    foreach (ORM::factory("item", $existing_passwords[$last_album_counter]->album_id)
             ->where("id", ">", $last_id)
             ->order_by("id")
             ->descendants(100) as $item) {

      $cached_item = ORM::factory("albumpassword_idcache");
      $cached_item->password_id =$existing_passwords[$last_album_counter]->id;
      $cached_item->item_id = $item->id;
      $cached_item->save();
		
      $last_id = $item->id;
      $completed_items++;

      // Set a time limit so the script doesn't time out.
      if (microtime(true) - $start > 1.5) {
        break;
      }
    } // end foreach

    // If completed_items equals total_items, then we've
    //  processed everything in the current album.
    //  Increase variables and set everything up for the
    //  next album.
    if ($completed_items == $total_items) {
  	  $completed_items = 0;
      $last_album_counter++;
      $completed_albums++;
      $last_id = 0;
    }

    // Store the current values of the variables for the next
    //  time this function is called.
    $task->set("last_album_counter", $last_album_counter);
    $task->set("last_id", $last_id);
    $task->set("completed_albums", $completed_albums);
    $task->set("completed_items", $completed_items);

    // Display the number of albums that have been completed before exiting.
    if ($total == $completed_albums) {
      $task->done = true;
      $task->state = "success";
      $task->percent_complete = 100;
      $task->status = t("Scanning Protected Album $completed_albums of $total");
    } else {
      $task->percent_complete = round(100 * $completed / $total);
      $task->status = t("Scanning Protected Album $completed_albums of $total -- $completed_items / $total_items files");
    }
  }
}
