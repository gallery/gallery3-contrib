<?php defined("SYSPATH") or die("No direct script access.");

class Item_Model extends Item_Model_Core {

    private $_aws_s3_meta;

    public function thumb_url($full_uri=false) {
        if (!module::get_var("aws_s3", "enabled")|| Router::$controller == "rest" || !$this->s3_thumb_uploaded)
            return parent::thumb_url($full_uri);

        if ($this->is_photo())
            return aws_s3::generate_url("th/" . $this->relative_path(), ($this->view_1 == 1 ? false : true), $this->updated);
        else if ($this->is_album() && $this->id > 1)
            return aws_s3::generate_url("th/" . $this->relative_path() . "/.album.jpg", ($this->view_1 == 1 ? false : true), $this->updated);
        else if ($this->is_movie())
            return aws_s3::generate_url("th/" . preg_replace("/...$/", "jpg", $this->relative_path()), ($this->view_1 == 1 ? false : true), $this->updated);
    }

    public function file_url($full_uri=false) {
        if (!module::get_var("aws_s3", "enabled") || Router::$controller == "rest" || !$this->s3_fullsize_uploaded)
            return parent::file_url($full_uri);

        return aws_s3::generate_url("fs/" . $this->relative_path(), ($this->view_1 == 1 ? false : true), $this->updated);
    }

    public function resize_url($full_uri=false) {
        if (!module::get_var("aws_s3", "enabled") || Router::$controller == "rest" || !$this->s3_resize_uploaded)
            return parent::resize_url($full_uri);

        if ($this->is_album() && $this->id > 1)
            return aws_s3::generate_url("rs/" . $this->relative_path() . "/.album.jpg", ($this->view_1 == 1 ? false : true), $this->updated);
        else
            return aws_s3::generate_url("rs/" . $this->relative_path(), ($this->view_1 == 1 ? false : true), $this->updated);
    }

    private function _load_aws_s3_meta($create_if_not_exists = true) {
        $this->_aws_s3_meta = ORM::factory("aws_s3_meta")->find($this->id);
        if (!$this->_aws_s3_meta->item_id) {
            if ($create_if_not_exists) {
                $this->_aws_s3_meta->item_id = $this->id;
                $this->_aws_s3_meta->save();
            }
            else
                return false;
        }
        return $this;
    }

    public function has_aws_s3_meta() {
        if (!$this->_load_aws_s3_meta(false))
            return false;
        return true;
    }

    public function get_aws_s3_meta() {
        if (!$this->_aws_s3_meta)
            $this->_load_aws_s3_meta();

        return $this->_aws_s3_meta;
    }

    public function save_s3_meta() {
        if ($this->_aws_s3_meta)
            $this->_aws_s3_meta->save();
    }

    public function save() {
        $this->save_s3_meta();
        return parent::save();
    }

    public function __get($column) {
        if (substr($column, 0, 3) == "s3_") {
            $var = substr($column, 3);
            return $this->get_aws_s3_meta()->$var;
        }
        return parent::__get($column);
    }

    public function __set($column, $value) {
        if (substr($column, 0, 3) == "s3_") {
            $var = substr($column, 3);
            $this->get_aws_s3_meta()->$var = $value;
        }
        else {
            parent::__set($column, $value);
        }
    }
  
}