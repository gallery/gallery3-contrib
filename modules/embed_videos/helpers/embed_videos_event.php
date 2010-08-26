<?php defined("SYSPATH") or die("No direct script access.");


class embed_videos_event_Core {
	
	static function item_created($item) {

    if ($item->type == "embedded_video") {
      // Build our thumbnail/resizes.
      try {
        graphics::generate($item);
      } catch (Exception $e) {
        log::error("graphics", t("Couldn't create a thumbnail or resize for %item_title",
                                 array("item_title" => $item->title)),
                   html::anchor($item->abs_url(), t("details")));
        Kohana_Log::add("error", $e->getMessage() . "\n" . $e->getTraceAsString());
      }

      // If the parent has no cover item, make this it.
      $parent = $item->parent();
      if (access::can("edit", $parent) && $parent->album_cover_item_id == null)  {
        item::make_album_cover($item);
      }
    }
  }
static function item_deleted($item) {
	ORM::factory("embedded_video")
             ->where("item_id", "=", $item->id)
             ->find()
             ->delete();
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
