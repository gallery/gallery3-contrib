<?php defined("SYSPATH") or die("No direct script access.");


class pam_event {

  /**
  * remove the default login link and use our own
  */
  static function user_menu($menu, $theme) {
    $user = identity::active_user();
    if ($user->guest) {
      // disable the default login
      $menu->remove('user_menu_login');
      // add ours
      $menu->append(Menu::factory("dialog")
                    ->id("user_menu_pam")
                    ->css_id("g-pam-menu")
                    ->url(url::site("pam/ajax"))
                    ->label(t("Login")));
    }
  }

}
