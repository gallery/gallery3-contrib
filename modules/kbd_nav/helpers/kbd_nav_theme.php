<?php defined("SYSPATH") or die("No direct script access.");

class Kbd_Nav_theme_Core {

  static function head($theme) {
    $theme->script("kbd_nav.js");
  }
}