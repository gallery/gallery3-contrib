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
class picasa_faces_task_Core {
  static function available_tasks() {
    return array(Task_Definition::factory()
                  ->callback("picasa_faces_task::import_faces")
                  ->name(t("Import faces from Picasa"))
                  ->description(t("Scan all albums for Picasa face data and add any faces that don't already exist"))
                  ->severity(log::SUCCESS));
  }

  static function import_faces($task) {
    if (!module::is_active("photoannotation")) {
      $task->done = true;
      $task->status = t("Photo Annotation module is inactive, no faces will be imported");
      return;
    }

    $start = microtime(true);

    // Figure out the total number of albums in the database.
    // If this is the first run, also set last_id and completed to 0.
    $total = $task->get("total");
    if (empty($total)) {
      $task->set("total", $total = count(ORM::factory("item")->where("type", "=", "album")->find_all()));
      $task->set("last_id", 0);
      $task->set("completed", 0);
      $task->set("new_faces", 0);
      $task->set("old_faces", 0);
    }
    $last_id = $task->get("last_id");
    $completed = $task->get("completed");
    $new_faces = $task->get("new_faces");
    $old_faces = $task->get("old_faces");

    // Try to find a contacts.xml file, and parse out the contents if it exists
    $contacts = null;
    $contactsXML = VARPATH . "albums/contacts.xml";
    if (file_exists($contactsXML)) {
      $xml = simplexml_load_file($contactsXML);
      $contacts = $xml->contact;
    }

    // Check each album in the database to see if it has a .picasa.ini file on disk,
    // and extract any faces if it does.
    foreach (ORM::factory("item")
             ->where("id", ">", $last_id)
             ->where("type", "=", "album")
             ->order_by("id")
             ->find_all(100) as $albumItem) {
      $picasaFile = $albumItem->file_path()."/.picasa.ini";
      if (file_exists($picasaFile)) {
        // Parse the .picasa.ini file and extract any faces
        $photosWithFaces = self::parsePicasaIni($picasaFile);

        // Build a mapping from photo filenames in this album to the items
        $photos = array();
        foreach ($albumItem->children() as $child) {
            if ($child->is_photo()) {
                $photos[$child->name] = $child;
            }
        }

        // Iterate through all the photos with faces in them
        foreach ($photosWithFaces as $photoName => $faces) {
          // Find the item for this photo
          $photoItem = $photos[$photoName];
          if ($photoItem) {
            foreach ($faces as $faceId => $faceCoords) {
              $faceMapping = ORM::factory("picasa_face")->where("face_id", "=", $faceId)->find();

              // This is a special id Picasa uses for ignored faces, skip it
              if ($faceId == "ffffffffffffffff") {
                continue;
              }

              // If we don't already have a mapping for this face, create one
              if (!$faceMapping->loaded()) {
                $newTagId = self::getFaceMapping($faceId, $contacts);

                // Save the mapping from Picasa face id to tag id, so
                // faces will be grouped properly
                $faceMapping->face_id = $faceId;
                $faceMapping->tag_id = $newTagId;
                $faceMapping->user_id = 0;
                $faceMapping->save();
              }

              if ($faceMapping->user_id == 0) {
                $numTagsOnPhoto = ORM::factory("items_face")
                                    ->where("tag_id", "=", $faceMapping->tag_id)
                                    ->where("item_id", "=", $photoItem->id)
                                    ->count_all();
              } else {
                $numTagsOnPhoto = ORM::factory("items_user")
                                    ->where("user_id", "=", $faceMapping->user_id)
                                    ->where("item_id", "=", $photoItem->id)
                                    ->count_all();
              }

              // If this face tag isn't already on this photo, add it (we
              // assume a face should only ever appear once per photo)
              if ($numTagsOnPhoto == 0) {
                  self::addNewFace($faceMapping, $faceCoords, $photoItem);
                  $new_faces++;
              } else {
                  $old_faces++;
              }
            }
          }
        }
      }

      $last_id = $albumItem->id;
      $completed++;

      if ($completed == $total || microtime(true) - $start > 1.5) {
          break;
      }
    }

    $task->set("completed", $completed);
    $task->set("last_id", $last_id);
    $task->set("new_faces", $new_faces);
    $task->set("old_faces", $old_faces);

    if ($total == $completed) {
      $task->done = true;
      $task->state = "success";
      $task->percent_complete = 100;
    } else {
      $task->percent_complete = round(100 * $completed / $total);
    }

    $task->status = t("%completed / %total albums scanned, %new_faces faces added, %old_faces faces unchanged",
      array("completed" => $completed, "total" => $total, "new_faces" => $new_faces, "old_faces" => $old_faces));
  }

  static function getFaceMapping($faceId, $contacts) {
    $personTag = null;

    // If we have a contacts.xml file, try to find the face id there
    if ($contacts != null) {
      foreach ($contacts as $contact) {
        if ($contact['id'] == $faceId) {
          // We found this face id in the contacts.xml.  See if a tag already exists.
          $personTag = ORM::factory("tag")->where("name", "=", $contact['name'])->find();

          // If the tag doesn't exist already, add it
          if (!$personTag->loaded()) {
            $personTag->name = $contact['name'];
            $personTag->save();
          }

          break;
        }
      }
    }

    // We either didn't find the face in contacts.xml, or we don't have contacts.xml.
    // Add the face using a generic name.
    if ($personTag == null) {
      // Find an unused "Picasa x" tag
      $personID = 0;
      $personName = "";
      do {
        $personID++;
        $personName = "Picasa ".$personID;
        $personTag = ORM::factory("tag")->where("name", "=", $personName)->find();
      } while ($personTag->loaded());

      // We found an open name, save it so we can get the id
      $personTag->name = $personName;
      $personTag->save();
    }

    return $personTag->id;
  }

  static function addNewFace($faceMapping, $faceCoords, $photoItem) {
    // Calculate the face coordinates.  Picasa stores them as 0-65535, and we remap
    // that to the resize dimensions.
    $left   = (int)(($photoItem->resize_width  * $faceCoords["left"])   / 65535);
    $top    = (int)(($photoItem->resize_height * $faceCoords["top"])    / 65535);
    $right  = (int)(($photoItem->resize_width  * $faceCoords["right"])  / 65535);
    $bottom = (int)(($photoItem->resize_height * $faceCoords["bottom"]) / 65535);

    if ($faceMapping->user_id == 0) {
      // Add the photo to this tag
      $tag = ORM::factory("tag")->where("id", "=", $faceMapping->tag_id)->find();
      $tag->add($photoItem);
      $tag->count++;
      $tag->save();

      // Add the face
      $newFace = ORM::factory("items_face");
      $newFace->tag_id = $faceMapping->tag_id;
      $newFace->item_id = $photoItem->id;
      $newFace->x1 = $left;
      $newFace->y1 = $top;
      $newFace->x2 = $right;
      $newFace->y2 = $bottom;
      $newFace->description = "";
      $newFace->save();
    } else {
      // Add the face
      $newFace = ORM::factory("items_user");
      $newFace->user_id = $faceMapping->user_id;
      $newFace->item_id = $photoItem->id;
      $newFace->x1 = $left;
      $newFace->y1 = $top;
      $newFace->x2 = $right;
      $newFace->y2 = $bottom;
      $newFace->description = "";
      $newFace->save();
    }
  }

  static function parsePicasaIni($filePath) {
    // It would be nice to use parse_ini_file here, but the parens used with the rect64 values break it
    $ini_lines = file($filePath);

    $curFilename = "";

    $photosWithFaces = array();

    foreach ($ini_lines as $ini_line) {
      // Trim off any whitespace at the ends
      $ini_line = trim($ini_line);

      if ($ini_line[0] == '[') {
        // If this line starts with [ it's a filename, strip off the brackets
        $curFilename = substr($ini_line, 1, -1);
      } else {
        // If this isn't a filename, it must be data for a file, get the key/value pair
        $photoData = explode("=", $ini_line);

        if ($photoData[0] == "faces") {
          // If it's face data, break it up by face
          $faces = explode(";", $photoData[1]);

          $photoFaces = array();

          foreach ($faces as $face) {
            // Split a face into the rectangle and face id
            $splitface = explode(",", $face);

            $hexrect = substr($splitface[0], 7, -1);
            // We need a string with 16 chars. Fill up with zeros from left.
            $hexrect = str_pad($hexrect, 16, "0", STR_PAD_LEFT);
            $person = $splitface[1];

            // The rectangle is 4 4-character hex values
            $left   = hexdec(substr($hexrect,0,4));
            $top    = hexdec(substr($hexrect,4,4));
            $right  = hexdec(substr($hexrect,8,4));
            $bottom = hexdec(substr($hexrect,12,4));

            $facePos = array("left"   => $left,
                             "top"    => $top,
                             "right"  => $right,
                             "bottom" => $bottom);

            $photoFaces[$person] = $facePos;
          }

          $photosWithFaces[$curFilename] = $photoFaces;
        }
      }
    }

    return $photosWithFaces;
  }
}

?>
