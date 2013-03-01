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

//2011-08-20 added customerid
   $db->query("CREATE TABLE IF NOT EXISTS {orders} (
                 `id` int(9) NOT NULL auto_increment,
                 `customerid` int(9) NOT NULL,
		 `status` int(9) DEFAULT 0,
                 `name` varchar(512),
                 `email` varchar(256),
                 `cost` DECIMAL(10,2) default 0,
                 `method` int(9) DEFAULT 0,
                 `text` TEXT NOT NULL,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

//2011-08-20 added table
   $db->query("CREATE TABLE IF NOT EXISTS {customers} (
                `id` int(9) NOT NULL AUTO_INCREMENT,
                `title` varchar(32) DEFAULT NULL,
                `name` varchar(256) NOT NULL,
                `initials` varchar(64) DEFAULT NULL,
                `insertion` varchar(16) DEFAULT NULL,
                `street` varchar(128) DEFAULT NULL,
                `housenumber` varchar(32) DEFAULT NULL,
                `postalcode` varchar(16) DEFAULT NULL,
                `town` varchar(128) DEFAULT NULL,
                `email` varchar(128) NOT NULL,
                `phone` varchar(16) DEFAULT NULL,
                `childname` varchar(64) DEFAULT NULL,
                `childgroup` varchar(32) DEFAULT NULL,
                `deliverypref` tinyint(2) DEFAULT NULL,
                 PRIMARY KEY (`id`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");

//2011-05-01 added table
   $db->query("CREATE TABLE IF NOT EXISTS {order_logs} (
                 `id` int(9) NOT NULL,
                 `status` int(9) NOT NULL,
                 `event` int(9) NOT NULL,
                 `timestamp` int(9) NOT NULL)
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                 
  $db->query("CREATE TABLE IF NOT EXISTS `ipn_messages` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date`  int(11) NOT NULL,
                `key` varchar(20) NOT NULL,
                `txn_id` varchar(20) NOT NULL,
                `status` varchar(20) NOT NULL,
                `success` bool default false,
                `text` text,
                PRIMARY KEY  (`id`)
              ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

     /* name, fixed cost, per item cost */
/*     postage_band::create("Geen verzendkosten",0,0);
     postage_band::create("Standaard verzendkosten",2,0);
     postage_band::create("Verzending via e-mail (gratis)",0,0);
*/
     /* name, cost, descr, postageband id */
/*     product::create("13x18",3.5,"Afdruk 13x18 cm glanzend",2);
     product::create("2x 13x18",5,"ACTIE: Dubbele afdruk 13x18 cm glanzend",2);
     product::create("Origineel",7.5,"Originele fotobestand in hoge resolutie",3);
     product::create("8 Pasfoto's+9x13",4,"Vel met 8 pasfoto's + afdruk 9x13",2);
     product::create("16 Pasfoto's",4,"Vel met 16 pasfoto's glanzend",2);
     product::create("10x15",2.5,"Afdruk 10x15 cm glanzend",2);
     product::create("20x30",6,"Afdruk 20x30 cm glanzend",2);
     product::create("30x45",10,"Afdruk 30x45 cm glanzend",2);
     product::create("13x18 (korting)",1.75,"Afdruk 13x18 cm glanzend",2);
     product::create("20x30 (korting)",3,"Afdruk 20x30 cm glanzend",2);
     product::create("Origineel (korting)",2.5,"Originele fotobestand in hoge resolutie",3);
*/
     /* basket config settings */
     basket::setWebsite("kidsfotos.nl");
     basket::setWebshop("KidsFotos");
	 basket::setEmailAddress("KidsFotos.nl <bestelling@kidsfotos.nl>");
	 basket::set_side_bar_only("1");
     basket::setAllowPickup("1");
     basket::setPickupLocation("De Blauwe Walvis");
     basket::setOrderPrefix("2012-");
     basket::setOrderBankAccount("59.24.98.220");
     basket::setOrderAccountOwner("J. ten Kate, Utrecht");

     /* basket template settings */
     basket::setPaymentDetails("
<p>U kunt betalen via overmaking op de bankrekening van %webshop.</p>
<p>Instructies vindt u in de bevestingsmail van de bestelling.</p>
<br />
<h3>Algemene voorwaarden</h3>
<p>U kunt de Algemene voorwaarden via deze <a href=#/downloads/Algemene_voorwaarden_2011.pdf# target=#_blank#>link</a> bekijken.</p>");
     basket::setOrderCompletePage(
"<p>Hartelijk dank voor uw bestelling. Uw bestelnummer is </b>%order_number</b>.</p>
<br />
<p>%webshop heeft een bevestigingsmail verzonden met de gegevens van uw bestelling en de betalingsinformatie.
<p>Wij verwerken de bestelling zodra de betaling is ontvangen.
<br />
<p>Voor vragen of opmerkingen over uw bestelling kunt u contact opnemen via bestelling@%website</p>");

     basket::setOrderCompleteEmailSubject("Uw bestelling %order_number bij %webshop");
     basket::setOrderCompleteEmail("Beste %name,

Hartelijk dank voor uw bestelling. De bestelgegevens en betalingsinformatie vindt u hieronder. 

%order_details");

     basket::setOrderPaidEmailSubject("Update van uw bestelling %order_number bij %webshop: betaling ontvangen");
     basket::setOrderPaidEmail("Beste %name,

%webshop heeft uw betaling van %total_cost ontvangen en zal bestelling %order_number verwerken. 
U ontvangt een e-mail zodra de bestelling naar u wordt verzonden of klaarligt op het kinderdagverblijf. 

Voor vragen of opmerkingen over uw bestelling kunt u contact opnemen via bestelling@%website.");

     basket::setOrderLatePaymentEmailSubject("Uw bestelling %order_number bij %webshop: wacht op betaling");
     basket::setOrderLatePaymentEmail("Beste %name,

Enige tijd geleden heeft u bij %webshop bestelling %order_number geplaatst. Onderaan vindt u hiervan de details.
Uit onze administratie blijkt dat het bedrag van %total_cost nog niet is voldaan. Wij maken u erop attent dat wij pas na ontvangst van de betaling de bestelling verwerken.

Mocht deze herinnering uw betaling hebben gekruist, dan kunt u deze als niet verzonden beschouwen.

Voor vragen of opmerkingen over uw bestelling kunt u contact opnemen via bestelling@%website.");

     basket::setOrderDeliveredEmailSubject("Update van uw bestelling %order_number bij %webshop: bestelling verstuurd");
     basket::setOrderDeliveredEmail("Beste %name,

%webshop heeft uw bestelling %order_number %delivery_method. 
Nogmaals dank voor uw bestelling en veel plezier met de foto's!

Voor vragen of opmerkingen over uw bestelling kunt u contact opnemen via bestelling@%website.");

     basket::setOrderEmailClosing("Met vriendelijke groet,
%webshop.nl - Fotograaf Jeroen ten Kate");

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
	//
    if ($version==5){
      $db->query("ALTER TABLE {orders} ADD COLUMN `customerid` int(9) NOT NULL;");
      $db->query("ALTER TABLE {customers} ADD COLUMN `childname` varchar(64);");
      $db->query("ALTER TABLE {customers} ADD COLUMN `childgroup` varchar(32);");
      module::set_version("basket", $version = 6);
    }
  }

  static function uninstall(){
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {products}");
    $db->query("DROP TABLE IF EXISTS {product_overrides}");
    $db->query("DROP TABLE IF EXISTS {item_products}");
    $db->query("DROP TABLE IF EXISTS {postage_bands}");
    //$db->query("DROP TABLE IF EXISTS {orders}");
  }
}
