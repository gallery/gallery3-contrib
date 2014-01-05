<?php defined("SYSPATH") or die("No direct script access.")
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
<h2><?= $order->title()?></h2>
<div class="basketbuttons">	<?
	if ($order->status == Bp_Order_Model::WAITING_PAYMENT){?>
	<table>
		<tr>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/confirm_order_payment/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_CONFIRM_PAYMENT) ?></a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/remind_order_payment/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_SEND_PAYMENT_REMINDER) ?></a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/send_order_copy/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_SEND_ORDER_COPY) ?></a></td>
		</tr>
		<tr>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/confirm_order_delivery/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_CONFIRM_DELIVERY_WO_PAYMENT) ?></a></td> 
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/confirm_order_cancelled/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_CANCEL_ORDER) ?></a></td> 
		</tr>
	</table>
	 <?
	}
	if ($order->status == Bp_Order_Model::DELIVERED_NOTPAID){?>
	<table>
		<tr>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/confirm_order_payment/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_CONFIRM_PAYMENT) ?></a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/remind_order_payment/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_SEND_PAYMENT_REMINDER) ?></a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/send_order_copy/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_SEND_ORDER_COPY) ?></a></td>
		</tr>
	</table>
	 <?
	}
	if ($order->status == Bp_Order_Model::PAYMENT_CONFIRMED){?>
	<table>
		<tr>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/confirm_order_delivery/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_CONFIRM_DELIVERY) ?></a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket_plus/notify_order_delayed/".$order->id)."?csrf=$csrf";?>"><?= t(basket_plus_label::BUTTON_NOTIFY_DELAY) ?></a></td>
		</tr>
	</table>
 <?
	}
/*	
//NOT USED
	if ($order->payment_method == Bp_Order_Model::PAYMENT_PAYPAL){?>
    <br/><a href="<?= url::site("basket_plus/view_ipn/".$order->id);?>"><?= t("View Paypal IPN Messages")?></a>
 <?	
	}*/ ?>
</div>
<?= t("Payment and Delivery method")?>:&nbsp;<?= $order->payment_method()?>&nbsp;-&nbsp;<?= $order->delivery_method()?> </br>
<?= str_replace(array("\r\n", "\n", "\r"),"<br/>",$order->internal_text);?>
