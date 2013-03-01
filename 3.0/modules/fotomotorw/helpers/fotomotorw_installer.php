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

class fotomotorw_installer {
  static function install() {
    // Set up some default values.
    module::set_var("fotomotorw", "fotomoto_site_key", '');
    module::set_var("fotomotorw", "fotomoto_private_key", md5(random::hash() . access::private_key()));
    module::set_var("fotomotorw", "fotomoto_buy_prints", 1);
    module::set_var("fotomotorw", "fotomoto_buy_cards", 1);
    module::set_var("fotomotorw", "fotomoto_buy_download", 1);
    module::set_var("fotomotorw", "fotomoto_share_ecard", 1);
    module::set_var("fotomotorw", "fotomoto_share_facebook", 1);
    module::set_var("fotomotorw", "fotomoto_share_twitter", 1);
    module::set_var("fotomotorw", "fotomoto_share_digg", 1);
    module::set_version("fotomotorw", 1);
  }
}
