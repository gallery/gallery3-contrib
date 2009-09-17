<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class basket_installer {
  static function install() {
   $db = Database::instance();
   $db->query("CREATE TABLE IF NOT EXISTS {products} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` TEXT NOT NULL,
                 `cost` INTEGER(9) default 0,
                 `description` varchar(1024),
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

   $db->query("CREATE TABLE IF NOT EXISTS {product_overrides} (
                 `id` int(9) NOT NULL auto_increment,
                 `item_id` int(9) NOT NULL,
                 `none` BOOLEAN default false,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

   $db->query("CREATE TABLE IF NOT EXISTS {item_products} (
                 `id` int(9) NOT NULL auto_increment,
                 `product_override_id` int(9) NOT NULL,
                 `product_id` int(9) NOT NULL,
                 `include` BOOLEAN default false,
                 `cost` INTEGER(9) default -1,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

   product::create("4x6",5,"4\"x6\" print");
   product::create("8x10",25,"8\"x10\" print");
   product::create("8x12",30,"8\"x12\" print");

   module::set_version("basket", 1);
  }

  static function uninstall() {
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {products}");
    $db->query("DROP TABLE IF EXISTS {product_overrides}");
    $db->query("DROP TABLE IF EXISTS {item_products}");
  }
}
