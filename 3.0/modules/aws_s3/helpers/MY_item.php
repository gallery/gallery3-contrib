<?php

class item extends item_Core {

    static function make_album_cover($item, $sync = false) {
        if (!$sync)
            parent::make_album_cover($item);

        $parent = $item->parent();
        if ($parent->id > 1) {
            aws_s3::upload_album_cover($parent);
        }
    }

    static function remove_album_cover($album) {
        parent::remove_album_cover($album);

        if ($album->id > 1) {
            aws_s3::remove_album_cover($album);
        }
    }

}