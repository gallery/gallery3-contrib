<?php defined("SYSPATH") or die("No direct script access.");

class fittoscreen_event_Core {
  static function admin_menu($menu, $theme) {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("fittoscreen_menu")
               ->label(t("Fit to Screen"))
               ->url(url::site("admin/fittoscreen")));
  }
}

?>
