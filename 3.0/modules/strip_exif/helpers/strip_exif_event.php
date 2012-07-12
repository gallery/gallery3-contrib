<?php defined("SYSPATH") or die("No direct script access.");

class strip_exif_event_Core {
    static function admin_menu($menu, $theme) {
        $menu
            ->get("settings_menu")
            ->append(Menu::factory("link")
            ->id("strip_exif_menu")
            ->label(t("Strip EXIF/IPTC Data"))
            ->url(url::site("admin/strip_exif")));
    }

    static function item_created($item) {
        strip_exif::strip($item);
    }

    static function item_updated_data_file($item) {
        strip_exif::strip($item);
    }
}

# vim: tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
