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

class basket_installer
{
  static function install(){

   $db = Database::instance();

   $db->query("CREATE TABLE IF NOT EXISTS {products} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` TEXT NOT NULL,
                 `cost` DECIMAL(10,2) default 0,
                 `description` varchar(1024),
                 `postage_band_id` int(9) default 1,
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
                 `cost` DECIMAL(10,2) default -1,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

   $db->query("CREATE TABLE IF NOT EXISTS {postage_bands} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` TEXT NOT NULL,
                 `flat_rate` DECIMAL(10,2) default 0,
                 `per_item` DECIMAL(10,2) default 0,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

   $db->query("CREATE TABLE IF NOT EXISTS {orders} (
                 `id` int(9) NOT NULL auto_increment,
                 `status` int(9) DEFAULT 0,
                 `name` varchar(1024),
                 `email` varchar(1024),
                 `cost` DECIMAL(10,2) default 0,
                 `method` int(9) DEFAULT 0,
                 `text` TEXT NOT NULL,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

  $db->query("CREATE TABLE IF NOT EXISTS {ipn_messages} (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `date`  int(11) NOT NULL,
    `key` varchar(20) NOT NULL,
    `txn_id` varchar(20) NOT NULL,
    `status` varchar(20) NOT NULL,
    `success` bool default false,
    `text` text,
    PRIMARY KEY  (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");


   postage_band::create("No Postage",0,0);

   product::create("4x6",5,"4\"x6\" print",1);
   product::create("8x10",25,"8\"x10\" print",1);
   product::create("8x12",30,"8\"x12\" print",1);

   basket::setPaymentDetails(
"<p>Use the following options to pay for this order.</p>
<p>Send a chequre to..</p>
<p>Visit the shop..</p>
<p>By using internet banking..</p>"
   );
   basket::setOrderPrefix("ORDER");
   basket::setOrderCompletePage(
"<p>Your order number is %order_number. To pay for this order please either:</p>
<p> - Send a cheque for %total_cost to with reference %order_number..</p>
<p> - Visit the shop and quote the order %order_number..</p>
<p> - Transfer %total_cost using internet banking with reference %order_number..</p>
<p>Order will be processed as soon as payment is received. You should receive an e-mail with your order details shortly.</p>"
   );
   basket::setOrderCompleteEmail(
"Hi %name,

Thank you for your order the order details are below. To pay for this order please either:

- Send a cheque for %total_cost to with reference %order_number..
- Visit the shop and quote the order %order_number..
- Transfer %total_cost using internet banking with reference %order_number..

Order will be processed as soon as payment is received. For order pick-ups please visit..

Order Details
-------------
%order_details

Thanks");
   basket::setOrderCompleteEmailSubject(
"Photography Order %order_number");

   module::set_version("basket", 5);

  }

  static function upgrade($version) {
    $db = Database::instance();
    if ($version == 1) {

      // fix for allowing decimel place in money
      $db->query("ALTER TABLE {products} CHANGE COLUMN `cost` `cost` DECIMAL(10,2) default 0;");
      $db->query("ALTER TABLE {item_products} CHANGE COLUMN `cost` `cost` DECIMAL(10,2) default -1;");

      // postage bands
      $db->query("ALTER TABLE {products} ADD COLUMN `postage_band_id` int(9) default 1");
      $db->query("CREATE TABLE IF NOT EXISTS {postage_bands} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` TEXT NOT NULL,
                 `flat_rate` DECIMAL(10,2) default 0,
                 `per_item` DECIMAL(10,2) default 0,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");
      postage_band::create("No Postage",0,0);

      module::set_version("basket", $version = 2);
    }

    if ($version == 2) {
      $db->query("CREATE TABLE IF NOT EXISTS {orders} (
                 `id` int(9) NOT NULL auto_increment,
                 `text` TEXT NOT NULL,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");
      basket::setPaymentDetails(
"<p>Use the following options to pay for this order.</p>
<p>Send a chequre to..</p>
<p>Visit the shop..</p>
<p>By using internet banking..</p>"
   );
      basket::setOrderPrefix("ORDER");
      basket::setOrderCompletePage(
"<p>Your order number is %order_number. To pay for this order please either:</p>
<p> - Send a cheque for %total_cost to with reference %order_number..</p>
<p> - Visit the shop and quote the order %order_number..</p>
<p> - Transfer %total_cost using internet banking with reference %order_number..</p>
<p>Order will be processed as soon as payment is received. You should receive an e-mail with your order details shortly.</p>"
   );
      basket::setOrderCompleteEmail(
"Hi %name,

Thank you for your order the order details are below. To pay for this order please either:

- Send a cheque for %total_cost to with reference %order_number..
- Visit the shop and quote the order %order_number..
- Transfer %total_cost using internet banking with reference %order_number..

Order will be processed as soon as payment is received. For order pick-ups please visit..

Order Details
-------------
%order_details

Thanks");
      basket::setOrderCompleteEmailSubject(
"Photography Order %order_number");

      module::set_version("basket", $version = 3);
    }

    if ($version ==3 ){
      $db->query("ALTER TABLE {orders} ADD COLUMN `status` int(9) DEFAULT 0;");

      $db->query("CREATE TABLE IF NOT EXISTS {ipn_messages} (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `date`  int(11) NOT NULL,
        `key` varchar(20) NOT NULL,
        `txn_id` varchar(20) NOT NULL,
        `status` varchar(20) NOT NULL,
        `success` bool default false,
        `text` text,
        PRIMARY KEY  (`id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
      module::set_version("basket", $version = 4);

    }

    if ($version==4){
      $db->query("ALTER TABLE {orders} ADD COLUMN `name` varchar(1024);");
      $db->query("ALTER TABLE {orders} ADD COLUMN `email` varchar(1024);");
      $db->query("ALTER TABLE {orders} ADD COLUMN `method` int(9) DEFAULT 0;");
      $db->query("ALTER TABLE {orders} ADD COLUMN `cost` DECIMAL(10,2) default 0");
      module::set_version("basket", $version = 5);
    }
  }

  static function uninstall(){
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {products}");
    $db->query("DROP TABLE IF EXISTS {product_overrides}");
    $db->query("DROP TABLE IF EXISTS {item_products}");
    $db->query("DROP TABLE IF EXISTS {postage_bands}");
    $db->query("DROP TABLE IF EXISTS {orders}");
    $db->query("DROP TABLE IF EXISTS {ipn_messages}");
  }
}
