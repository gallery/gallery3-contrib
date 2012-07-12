<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2011 Chad Parry
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
class rawphoto_version {
  const MIN_RELEASE_VERSION = "3.0.3";
  const MIN_BUILD_NUMBER = "164";

  static function report_item_conversion_support() {
    if (gallery::RELEASE_CHANNEL == "release") {
      if (version_compare(gallery::VERSION, rawphoto_version::MIN_RELEASE_VERSION, ">=")) {
        site_status::clear("rawphoto_needs_item_conversion_support");
      } else {
        site_status::warning(
          t("The <em>Raw Photos</em> module requires Gallery %version or higher.",
            array("version" => rawphoto_version::MIN_RELEASE_VERSION)),
          "rawphoto_needs_item_conversion_support");
      }
    } else {
      if (version_compare(gallery::build_number(), rawphoto_version::MIN_BUILD_NUMBER, ">=")) {
        site_status::clear("rawphoto_needs_item_conversion_support");
      } else {
        site_status::warning(
          t("The <em>Raw Photos</em> module requires Gallery %version, build %build_number or higher.",
            array("version" => gallery::VERSION,
                  "build_number" => rawphoto_version::MIN_BUILD_NUMBER)),
          "rawphoto_needs_item_conversion_support");
      }
    }
  }
}
