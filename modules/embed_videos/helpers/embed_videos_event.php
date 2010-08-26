<?php defined("SYSPATH") or die("No direct script access.");


class embed_videos_event_Core {
static function item_deleted($item) {
	ORM::factory("embedded_video")
             ->where("item_id", "=", $item->id)
             ->find_all()
             ->delete_all();
}
 static function site_menu($menu, $theme) {
    $item = $theme->item();

    if ($can_add = $item && access::can("add", $item)) {
      $menu->get("add_menu")
        ->append(Menu::factory("dialog")
                 ->id("embed_add")
                 ->label(t("Embed Video"))
                 ->url(url::site("form/add/embedded_videos/$item->id")));
    }
  }
}
