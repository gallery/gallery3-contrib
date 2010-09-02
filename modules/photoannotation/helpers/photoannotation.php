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
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
* General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA 02110-1301, USA.
*/
class photoannotation_Core {
  static function add($item, $tag_title, $description, $x1, $y1, $x2, $y2, $bTag) {
    if ( !$bTag && !empty($tag_title) ) {
      try {
        //we are trying to add a note
        $newnote = ORM::factory("items_note");
        $newnote->item_id = $item->id;
        $newnote->x1 = $x1;
        $newnote->y1 = $y1;
        $newnote->x2 = $x2;
        $newnote->y2 = $y2;
        $newnote->title = $tag_title;
        $newnote->description = $description;
        $newnote->save();
      } catch (Exception $e) {
        Kohana_Log::add("error", "Error adding note annotation.\n" .
                        $e->getMessage() . "\n" . $e->getTraceAsString());
      }
    } elseif ( $bTag && !empty($tag_title) ) {
      try {
        //we are adding a tag
        //first find the tag
        $tag = ORM::factory("tag")->where("name", "=", $tag_title)->find();
        //tag was not found
        if (!$tag->loaded()) {
          $tag->name = $tag_title;
          $tag->count = 0;
        }

        $tag->add($item);
        $tag->count++;
        $tag->save();
        //check if the tag is attached to the item
        // if the tag isn't attached, attach it
        //check if the face is already tagged
        $existingFace = ORM::factory("items_face")
                             ->where("tag_id", "=", $tag->id)
                             ->where("item_id", "=", $item->id)
                             ->find_all();

        if (count($existingFace) == 0) {
          // Save the new face to the database.
          $newface = ORM::factory("items_face");
          $newface->tag_id = $tag->id;
          $newface->item_id = $item->id;
          $newface->x1 = $x1;
          $newface->y1 = $y1;
          $newface->x2 = $x2;
          $newface->y2 = $y2;
          $newface->description = $description;
          $newface->save();
        } else {
          // Update the coordinates of an existing face.
          $updatedFace = ORM::factory("items_face", $existingFace[0]->id);
          $updatedFace->x1 = $x1;
          $updatedFace->y1 = $y1;
          $updatedFace->x2 = $x2;
          $updatedFace->y2 = $y2;
          $updatedFace->description = $description;
          $updatedFace->save();
        }
      } catch (Exception $e) {
        Kohana_Log::add("error", "Error adding note annotation.\n" .
                        $e->getMessage() . "\n" . $e->getTraceAsString());
      }
    } else {
      throw new exception("@todo MISSING_TAG_OR_DESCRIPTION");
    }
  }
}
?>

