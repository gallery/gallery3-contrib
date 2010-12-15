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

class Item_Model extends Item_Model_Core {

    public function thumb_url($full_uri=false) {
        if (!module::get_var("aws_s3", "enabled"))
            return parent::thumb_url($full_uri);

        if ($this->is_photo()) {
            return aws_s3::generate_url("th/" . $this->relative_path(), ($this->view_1 == 1 ? false : true), $this->updated);
        }
        else if ($this->is_album() && $this->id > 1) {
            return aws_s3::generate_url("th/" . $this->relative_path() . "/.album.jpg", ($this->view_1 == 1 ? false : true), $this->updated);
        }
        else if ($this->is_movie()) {
            $relative_path = preg_replace("/...$/", "jpg", $this->relative_path());
            return aws_s3::generate_url("th/" . $relative_path, ($this->view_1 == 1 ? false : true), $this->updated);
        }
    }

    public function file_url($full_uri=false) {
        if (!module::get_var("aws_s3", "enabled"))
            return parent::file_url($full_uri);

        return aws_s3::generate_url("fs/" . $this->relative_path(), ($this->view_1 == 1 ? false : true), $this->updated);
    }

    public function resize_url($full_uri=false) {
        if (!module::get_var("aws_s3", "enabled"))
            return parent::resize_url($full_uri);

        if ($this->is_album() && $this->id > 1) {
            return aws_s3::generate_url("rs/" . $this->relative_path() . "/.album.jpg", ($this->view_1 == 1 ? false : true), $this->updated);
        }
        else {
            return aws_s3::generate_url("rs/" . $this->relative_path(), ($this->view_1 == 1 ? false : true), $this->updated);
        }
    }
  
}