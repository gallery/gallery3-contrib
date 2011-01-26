<?php defined("SYSPATH") or die("No direct script access.");

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
        if ($item->id == 1)
            return true;

        aws_s3::log("Item created - " . $item->id);
        aws_s3::schedule_item_sync($item);
    }

    static function item_deleted($item) {
        if ($item->id == 1)
            return true;

        aws_s3::log("Item deleted - " . $item->id);
        aws_s3::remove_item($item);
        
        ORM::factory("aws_s3_meta", $item->id)->delete();
    }

    static function item_updated($old_item, $new_item) {
        if ($new_item->id == 1)
            return true;

        if ($new_item->has_aws_s3_meta()) {
            aws_s3::log("Item updated - " . $new_item->id);

            if ($old_item->relative_path() == $new_item->relative_path() && $old_item->s3_item_hash == $new_item->s3_item_hash) {
                aws_s3::log("nothing changed?!");
            }
            else if ($old_item->relative_path() != $new_item->relative_path()) {
                aws_s3::log("Item moved...");
                aws_s3::move_item($old_item, $new_item);
            }
            else {
                aws_s3::log("Item hasn't moved. Image updated?");
                aws_s3::remove_item($old_item);
                aws_s3::schedule_item_sync($new_item);
            }
        }
    }

}