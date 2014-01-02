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

class basket_plus_installer
{
  static function install(){

		$db = Database::instance();
//===========================================================
// Create the basket tables							
//===========================================================
	//add table postage_bands
		$db->query("CREATE TABLE IF NOT EXISTS {bp_postage_bands} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` TEXT NOT NULL,
                 `flat_rate` DECIMAL(10,2) default 0,
                 `per_item` DECIMAL(10,2) default 0,
                 `via_download` BOOLEAN default false,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	//add table products
		$db->query("CREATE TABLE IF NOT EXISTS {bp_products} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` TEXT NOT NULL,
                 `cost` DECIMAL(10,2) default 0,
                 `description` varchar(1024),
                 `bp_postage_band_id` int(9) default 1,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	//add table product_overrides
		$db->query("CREATE TABLE IF NOT EXISTS {bp_product_overrides} (
                 `id` int(9) NOT NULL auto_increment,
                 `item_id` int(9) NOT NULL,
                 `none` BOOLEAN default false,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	//add table item_products
		$db->query("CREATE TABLE IF NOT EXISTS {bp_item_products} (
                 `id` int(9) NOT NULL auto_increment,
                 `product_override_id` int(9) NOT NULL,
                 `product_id` int(9) NOT NULL,
                 `include` BOOLEAN default false,
                 `cost` DECIMAL(10,2) default -1,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	//add table ipn_messages
		$db->query("CREATE TABLE IF NOT EXISTS {bp_ipn_messages} (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date`  int(11) NOT NULL,
                `key` varchar(20) NOT NULL,
                `txn_id` varchar(20) NOT NULL,
                `status` varchar(20) NOT NULL,
                `success` bool default false,
                `text` text,
                PRIMARY KEY  (`id`)
              ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

	//add table orders
	//@TODO: create 'order lines' and create reference from order
		$db->query("CREATE TABLE IF NOT EXISTS {bp_orders} (
                `id` int(9) NOT NULL auto_increment,
                `customerid` int(9) NOT NULL,
								`status` int(9) DEFAULT 0,
                `name` varchar(512),
                `email` varchar(256),
                `cost` DECIMAL(10,2) default 0,
                `payment_method` int(9) DEFAULT 0,
                `delivery_method` int(9) DEFAULT 0,
                `text` mediumtext NOT NULL,
								`internal_text` varchar(2048),
                PRIMARY KEY (`id`))
                ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	//add table customers
		$db->query("CREATE TABLE IF NOT EXISTS {bp_customers} (
                `id` int(9) NOT NULL AUTO_INCREMENT,
                `title` varchar(32) DEFAULT NULL,
                `name` varchar(256) NOT NULL,
                `initials` varchar(64) DEFAULT NULL,
                `insertion` varchar(16) DEFAULT NULL,
                `street` varchar(128) DEFAULT NULL,
                `housenumber` varchar(32) DEFAULT NULL,
                `postalcode` varchar(16) DEFAULT NULL,
                `suburb` varchar(128) DEFAULT NULL,
                `town` varchar(128) DEFAULT NULL,
                `province` varchar(128) DEFAULT NULL,
                `country` varchar(128) DEFAULT NULL,
                `email` varchar(128) NOT NULL,
                `phone` varchar(16) DEFAULT NULL,
                `order_ref1` varchar(64) DEFAULT NULL,
                `order_ref2` varchar(32) DEFAULT NULL,
                `deliverypref` tinyint(2) DEFAULT NULL,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	//add table order_logs
		$db->query("CREATE TABLE IF NOT EXISTS {bp_order_logs} (
                 `id` int(9) NOT NULL,
                 `status` int(9) NOT NULL,
                 `event` int(9) NOT NULL,
                 `timestamp` int(9) NOT NULL)
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                 
	//add table user_baskets
    $db->query("CREATE TABLE IF NOT EXISTS {bp_user_baskets} (
                  `id` int(9) NOT NULL,
									`is_group` BOOLEAN default false,
                  `pickup_location` varchar(64) default NULL,
                  `extra_order_info_lbl` varchar(32) default NULL,
                  `extra_order_info` varchar(32) default NULL,
                  `extra_order_info2` varchar(32) default NULL,
                  `extra_order_info_lbl2` varchar(32) default NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY(`id`))
                  DEFAULT CHARSET=utf8;");

	//add table email_templates
    $db->query("CREATE TABLE IF NOT EXISTS {bp_email_templates} (
                  `id` int(9) NOT NULL AUTO_INCREMENT,
                  `name` varchar(32) NOT NULL,
                  `email_text` varchar(2048) default NULL,
                  `email_html` mediumtext default NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY(`id`))
                  DEFAULT CHARSET=utf8;");

		//Set fixed basket variables
		basket_plus::setBasketVar("meta_tag",str_replace('$','"','<meta http-equiv=$Content-Type$ content=$text/html; charset=UTF-8$ />'));
		
		//Initialise basket variables in the default language
//error_reporting(E_ALL);
		$language = module::get_var("gallery","default_locale");
		$is_reset = true;
		basket_plus_installer_local::initBasketProducts($language,$is_reset);
		basket_plus_installer_local::initBasketMailTemplates($language,$is_reset);
		basket_plus_installer_local::initBasketVars($language,$is_reset);

		//do the module administration
    module::set_version('basket_plus', 1);
  }

//NOT SUPPORTED YET
  static function upgrade($version) {
    $db = Database::instance();
		$language = module::get_var("gallery","default_locale");
    if ($version == 1) {
    }
  }

//NOT SUPPORTED YET
  static function uninstall(){
    $db = Database::instance();
  }
}
