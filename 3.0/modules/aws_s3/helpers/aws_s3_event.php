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

class aws_s3_event_Core {

    static function admin_menu($menu, $theme) {
        $menu
            ->get("settings_menu")
            ->append(
                    Menu::factory("link")
                            ->id("aws_s3_link")
                            ->label(t("Amazon S3"))
                            ->url(url::site("admin/aws_s3"))
                    );
    }

    static function item_created($item) {
        if ($item->is_album())
            return true;

        aws_s3::log("Item created - " . $item->id);
        aws_s3::upload_item($item);
    }

    static function item_deleted($item) {
        aws_s3::log("Item deleted - " . $item->id);
        aws_s3::remove_item($item);
    }

    static function item_moved($new_item, $old_item) {
        aws_s3::log("Item moved - " . $item->id);
        aws_s3::move_item($old_item, $new_item);
    }

}