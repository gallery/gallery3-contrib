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
class basket_plus_label_Core {
	//teksts to be translated in view 'admin_translates.html'
	const TEXT_LABEL_ENGLISH = "Label (English)";
	const TEXT_LABEL_TRANSLATED = "Label (translated)";

	//Fixed texts for order screens and order e-mail
  const TEXT_TOTAL_AMOUNT = "Total amount";
	const TEXT_ORDER_AMOUNT = "Order amount";
  const TEXT_OFFLINE_ATTENDING = "Attending";
  const TEXT_OFFLINE_REGARDING = "Regarding";
  const TEXT_OFFLINE_IN_NAME_OF = "In the name of";
  const TEXT_OFFLINE_PAYMENT = "You can pay by transferring the total amount to the bank account of";
	
	//Mail texts
  const TEXT_MAIL_ORDER_DETAILS = "ORDER DETAILS";
  const TEXT_MAIL_DELIVERY_TIME = "Delivery is approximately 10 business days after your payment is received by";
  const TEXT_MAIL_ORDER_FOR = "Order for";
  const TEXT_MAIL_ORDER_DATE = "Ordered on";
  const TEXT_MAIL_ORDER_COMMENT = "Comment on the order";
	const TEXT_MAIL_ORDER_CHILD = "Child name/Child group";
	const TEXT_PACKAGING_SHIPPING_COSTS = "Packaging and shipping costs";
	const TEXT_PACKAGING_POSTING = "Packaging and Posting";
	const TEXT_FREE_SHIPMENT = "Free Shipment";
	const TEXT_ORDERED_ON = "Ordered on";
	const TEXT_ORDER_FOR = "Order for";
	
	//Delivery
	const TEXT_DELIVERY_METHOD = "Delivery method";
	const TEXT_DELIVERY_MAIL = "Delivery at home address by mail";
	const TEXT_DELIVERY_EMAIL = "Delivery by e-mail";
	const TEXT_DELIVERY_PICKUP = "Pickup at";
	const TEXT_DELIVERED_MAIL = "sent by mail";
	const TEXT_DELIVERED_EMAIL = "sent by e-mail";
	const TEXT_DELIVERED_PICKUP = "available for pickup at";

	//Payment
	const TEXT_PAYMENT_METHOD = "Payment method";
	const TEXT_PAYMENT_OFFLINE = "Offline (bank transfer)";
	const TEXT_PAYMENT_PAYPAL = "PayPal";

	//Order details button labels
  const BUTTON_CONFIRM_PAYMENT = "Confirm Payment";
  const BUTTON_CONFIRM_DELIVERY = "Confirm Delivery";
	const BUTTON_CONFIRM_DELIVERY_WO_PAYMENT = "Confirm Delivery without Payment";
	const BUTTON_SEND_PAYMENT_REMINDER = "Send Payment reminder";
	const BUTTON_SEND_ORDER_COPY = "Send Order confirmation copy";
	const BUTTON_NOTIFY_DELAY = "Send Order delayed notification";
  const BUTTON_CANCEL_ORDER = "Cancel Order";
	
}