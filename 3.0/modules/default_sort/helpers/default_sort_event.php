<?php defined("SYSPATH") or die("No direct script access.");

class default_sort_event_Core {
    static function admin_menu($menu, $theme) {
        $menu
            ->get("settings_menu")
            ->append(Menu::factory("link")
            ->id("default_sort_menu")
            ->label(t("Default Sort Order"))
            ->url(url::site("admin/default_sort")));
    }

    static function item_created($item) {
        if ($item->is_album()) {
            if (($s = module::get_var("default_sort", "default_sort_column", "")))
                $item->sort_column = $s;

            if (($s = module::get_var("default_sort", "default_sort_direction", "")))
                $item->sort_order = $s;

            $item->save();
	}
    }
}

# vim: tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
