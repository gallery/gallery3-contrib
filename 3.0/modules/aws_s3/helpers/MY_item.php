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

class item extends item_Core {

    static function make_album_cover($item, $sync = false) {
        if (!$sync)
            parent::make_album_cover($item);

        $parent = $item->parent();
        if ($parent->id > 1) {
            aws_s3::upload_item($parent);
        }
    }

    static function remove_album_cover($album) {
        parent::remove_album_cover($album);

        if ($album->id > 1) {
            aws_s3::remove_item($album);
        }
    }

}