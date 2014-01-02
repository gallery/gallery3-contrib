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
?>
<div id="g-admin-configure">
	<h1> <?= t("Translate Basket Labels") ?> </h1>
	<p> <?= t("Use this page to translate the basket labels that can't be translated on individual pages.") ?>
	</p>
	<div class="left" ">
		<h2> <?= t("Order workflow") ?> </h2>
		<h3> <?= t("'View order' buttons") ?> </h3>
		<table id="order_ovw" class="bp-table">
				<tr><th width=50%><?= t(basket_plus_label::TEXT_LABEL_ENGLISH) ?></th><th><?= t(basket_plus_label::TEXT_LABEL_TRANSLATED) ?></th></tr>     
				<tr><td><?= basket_plus_label::BUTTON_CONFIRM_PAYMENT ?></td><td><?= t(basket_plus_label::BUTTON_CONFIRM_PAYMENT) ?></td>
				<tr><td><?= basket_plus_label::BUTTON_CONFIRM_DELIVERY ?></td><td><?= t(basket_plus_label::BUTTON_CONFIRM_DELIVERY) ?></td>
				<tr><td><?= basket_plus_label::BUTTON_CONFIRM_DELIVERY_WO_PAYMENT ?></td><td><?= t(basket_plus_label::BUTTON_CONFIRM_DELIVERY_WO_PAYMENT) ?></td>
				<tr><td><?= basket_plus_label::BUTTON_SEND_PAYMENT_REMINDER ?></td><td><?= t(basket_plus_label::BUTTON_SEND_PAYMENT_REMINDER) ?></td>
				<tr><td><?= basket_plus_label::BUTTON_SEND_ORDER_COPY ?></td><td><?= t(basket_plus_label::BUTTON_SEND_ORDER_COPY) ?></td>		
				<tr><td><?= basket_plus_label::BUTTON_NOTIFY_DELAY ?></td><td><?= t(basket_plus_label::BUTTON_NOTIFY_DELAY) ?></td>
				<tr><td><?= basket_plus_label::BUTTON_CANCEL_ORDER ?></td><td><?= t(basket_plus_label::BUTTON_CANCEL_ORDER) ?></td>
		</table>
		<h3> <?= t("Delivery") ?> </h3>
		<table id="order_ovw" class="bp-table">
				<tr><th width=50%><?= t(basket_plus_label::TEXT_LABEL_ENGLISH) ?></th><th><?= t(basket_plus_label::TEXT_LABEL_TRANSLATED) ?></th></tr>     
				<tr><td><?= basket_plus_label::TEXT_DELIVERY_METHOD ?></td><td><?= t(basket_plus_label::TEXT_DELIVERY_METHOD) ?></td>
				<tr><td><?= basket_plus_label::TEXT_DELIVERY_MAIL ?></td><td><?= t(basket_plus_label::TEXT_DELIVERY_MAIL) ?></td>
				<tr><td><?= basket_plus_label::TEXT_DELIVERY_EMAIL ?></td><td><?= t(basket_plus_label::TEXT_DELIVERY_EMAIL) ?></td>
				<tr><td><?= basket_plus_label::TEXT_DELIVERY_PICKUP ?></td><td><?= t(basket_plus_label::TEXT_DELIVERY_PICKUP) ?></td>
				<tr><td><?= basket_plus_label::TEXT_DELIVERED_MAIL ?></td><td><?= t(basket_plus_label::TEXT_DELIVERED_MAIL) ?></td>
				<tr><td><?= basket_plus_label::TEXT_DELIVERED_EMAIL ?></td><td><?= t(basket_plus_label::TEXT_DELIVERED_EMAIL) ?></td>
				<tr><td><?= basket_plus_label::TEXT_DELIVERED_PICKUP ?></td><td><?= t(basket_plus_label::TEXT_DELIVERED_PICKUP) ?></td>
		</table>
		<h3> <?= t("E-mail text fragments") ?> </h3>
		<table id="order_ovw" class="bp-table">
				<tr><th width=50%><?= t(basket_plus_label::TEXT_LABEL_ENGLISH) ?></th><th><?= t(basket_plus_label::TEXT_LABEL_TRANSLATED) ?></th></tr>     
				<tr><td><?= basket_plus_label::TEXT_OFFLINE_REGARDING ?></td><td><?= t(basket_plus_label::TEXT_OFFLINE_REGARDING) ?></td>
				<tr><td><?= basket_plus_label::TEXT_ORDER_AMOUNT ?></td><td><?= t(basket_plus_label::TEXT_ORDER_AMOUNT) ?></td>
				<tr><td><?= basket_plus_label::TEXT_TOTAL_AMOUNT ?></td><td><?= t(basket_plus_label::TEXT_TOTAL_AMOUNT) ?></td>
				<tr><td><?= basket_plus_label::TEXT_MAIL_ORDER_CHILD ?></td><td><?= t(basket_plus_label::TEXT_MAIL_ORDER_CHILD) ?></td>
				<tr><td><?= basket_plus_label::TEXT_MAIL_ORDER_COMMENT ?></td><td><?= t(basket_plus_label::TEXT_MAIL_ORDER_COMMENT) ?></td>
				<tr><td><?= basket_plus_label::TEXT_FREE_SHIPMENT ?></td><td><?= t(basket_plus_label::TEXT_FREE_SHIPMENT) ?></td>
				<tr><td><?= basket_plus_label::TEXT_DELIVERY_METHOD ?></td><td><?= t(basket_plus_label::TEXT_DELIVERY_METHOD) ?></td>
				<tr><td><?= basket_plus_label::TEXT_ORDER_FOR ?></td><td><?= t(basket_plus_label::TEXT_ORDER_FOR) ?></td>
				<tr><td><?= basket_plus_label::TEXT_ORDERED_ON ?></td><td><?= t(basket_plus_label::TEXT_ORDERED_ON) ?></td>
		</table>
		</div>
</div>