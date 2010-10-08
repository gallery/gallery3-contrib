<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2008 Bharat Mediratta
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
class polar_rose_theme_Core {
  static function head($theme) {
    if (module::is_installed("rss") && ($theme->item() || $theme->tag())) {
      if ($item = $theme->item()) {
        $url = rss::feed_link("gallery/album/{$item->id}");
      } else if ($tag = $theme->tag()) {
        $url = rss::feed_link("tag/tag/{$tag->id}");
      }

      // Polar Rose doesn't understand relative URLs.  Hack around that until they fix it.
      $url = url::abs_site(substr($url, strpos($url, "index.php") + 10));

      return "<script type=\"text/javascript\">" .
        "var polarroseconfig = {" .
        "partner: 'gallery3'," .
        "rss: '$url'," .
        "insert: 'g-polar-rose'," .
        "optin: ''," .
        "theme: 'dark'," .
        "progress: true" .
        "}</script>" .
        "<script type=\"text/javascript\" " .
        "src=\"http://cdn.widget.polarrose.com/polarrosewidget.js\">" .
        "</script>";
    }
  }

  static function page_bottom($theme) {
    return "<div id=\"g-polar-rose\"></div>";
  }
}
