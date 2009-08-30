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

class basket_installer
{
  static function install(){
   module::set_version("basket", 1);
  }
  static function activate() {

   $db = Database::instance();

   $db->query("CREATE TABLE IF NOT EXISTS {products} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` TEXT NOT NULL,
                 `cost` INTEGER(9) default 0,
                 `description` varchar(1024),
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");



   /*
   $db->query("CREATE TABLE IF NOT EXISTS {orders} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` varchar(1024),
                 `house` varchar(255),
                 `street` varchar(255),
                 `suburb` varchar(255),
                 `town` varchar(255),
                 `postcode` varchar(255),
                 `email` varchar(255),
                 `phone` varchar(255),
                 `status` char(1),
                 `activity_time` int(9),
                 `created` int(9),
                 `total` int(9),
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

   $db->query("CREATE TABLE IF NOT EXISTS {order_details} (
                 `id` int(9) NOT NULL auto_increment,
                 `order_id` int(9) NOT NULL,
                 `item_id` int(9) NOT NULL,
                 `product_name` TEXT NOT NULL,
                 `product_cost` INTEGER(9) default 0,
                 `product_description` varchar(1024),
                 `quantity` INTEGER(9) default 1,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

   $db->query("CREATE TABLE IF NOT EXISTS {order_histories} (
                 `id` int(9) NOT NULL auto_increment,
                 `order_id` int(9) NOT NULL,
                 `status_from` char(1),
                 `status_to` char(1),
                 `public` boolean,
                 `date` int(9),
                 `notes` TEXT default null,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");
  */
   product::create("4x6",5,"4\"x6\" print");
   product::create("8x10",25,"8\"x10\" print");
   product::create("8x12",30,"8\"x12\" print");

  }

  static function deactivate(){
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {products}");
    //$db->query("DROP TABLE IF EXISTS {orders}");
    //$db->query("DROP TABLE IF EXISTS {order_details}");
    //$db->query("DROP TABLE IF EXISTS {order_histories}");
  }
}
