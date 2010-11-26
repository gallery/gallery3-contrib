<?php

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