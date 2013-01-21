<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
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
class displaytags_event_Core {
  static function pre_deactivate($data) {
    if ($data->module == "tag") {
      $data->messages["warn"][] = t("The DisplayTags module requires the Tags module.");
    }
  }

  static function module_change($changes) {
    if (!module::is_active("tag") || in_array("tag", $changes->deactivate)) {
      site_status::warning(
        t("The DisplayTags module requires the Tags module.  <a href=\"%url\">Activate the Tags module now</a>",
          array("url" => html::mark_clean(url::site("admin/modules")))),
        "displaytags_needs_tag");
    } else {
      site_status::clear("displaytags_needs_tag");
    }
  }
}
