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
class keeporiginal_Controller extends Controller {

  public function restore($id) {
    // Allow the user to restore the original photo.
    
    // Make sure the current user has suficient access to view and edit the item.
    $item = ORM::factory("item", $id);
    access::required("view", $item);
    access::required("edit", $item);

    // Figure out where the original was stashed at.
    $original_image = VARPATH . "original/" . str_replace(VARPATH . "albums/", "", $item->file_path());

    // Make sure the current item is a photo and that an original exists.
    if ($item->is_photo() && file_exists($original_image)) {
      // Delete the modified version of the photo.
      @unlink($item->file_path());

      // Copy the original image back over, display an error message if the copy fails.
      if (@rename($original_image, $item->file_path())) {
        // Re-generate the items resize and thumbnail.
        $item_data = model_cache::get("item", $id);
        $item_data->resize_dirty= 1;
        $item_data->thumb_dirty= 1;
        $item_data->save();
        graphics::generate($item_data);

        // If the item is the thumbnail for the parent album, 
        //   fix the parent's thumbnail as well.
        $parent = $item_data->parent();
        if ($parent->album_cover_item_id == $item_data->id) {
          copy($item_data->thumb_path(), $parent->thumb_path());
          $parent->thumb_width = $item_data->thumb_width;
          $parent->thumb_height = $item_data->thumb_height;
          $parent->save();
        }
        
        // Display a success message and redirect to the items page.
        message::success(t("Your original image has been restored."));
        url::redirect($item->url());
      
      } else {
        // Display an error message if the copy failed.
        message::error(t("Image restore failed!"));
        url::redirect($item->url());      
      }
    } else {
      // Display an error message if there is not an original photo.
      message::error(t("Image restore failed!"));
      url::redirect($item->url());      
    }
  }
}
