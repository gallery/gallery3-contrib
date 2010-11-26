<?php

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