
<h2><?= $order->title()?></h2>
<div class="basketbuttons">	<?
	if ($order->status==Order_Model::WAITING_PAYMENT){?>
	<table>
		<tr>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/confirm_order_payment/".$order->id)."?csrf=$csrf";?>">Bevestig betaling</a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/remind_order_payment/".$order->id)."?csrf=$csrf";?>">Stuur betalingsherinnering</a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/send_order_copy/".$order->id)."?csrf=$csrf";?>">Stuur kopie bestelling</a></td>
		</tr>
		<tr>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/confirm_order_delivery/".$order->id)."?csrf=$csrf";?>">Bevestig verzending zonder betaling</a></td> 
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/confirm_order_cancelled/".$order->id)."?csrf=$csrf";?>">Annuleer bestelling</a></td> 
		</tr>
	</table>
	 <?
	}
	if ($order->status==Order_Model::DELIVERED_NOTPAID){?>
	<table>
		<tr>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/confirm_order_payment/".$order->id)."?csrf=$csrf";?>">Bevestig betaling</a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/remind_order_payment/".$order->id)."?csrf=$csrf";?>">Stuur betalingsherinnering</a></td>
			<td><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/send_order_copy/".$order->id)."?csrf=$csrf";?>">Stuur kopie bestelling</a></td>
		</		</tr>
	</table>
	 <?
	}
	if ($order->status==Order_Model::PAYMENT_CONFIRMED){
    ?><br/><a class="g-button ui-state-default ui-icon-right" href="<?= url::site("basket/confirm_order_delivery/".$order->id)."?csrf=$csrf";?>">Bevestig verzending</a> <?
	}
	if ($order->method==Order_Model::PAYMENT_PAYPAL){
    ?><br/><a href="<?= url::site("basket/view_ipn/".$order->id);?>">View Paypal IPN Messages</a>
 <?	} ?>
</div>
Betaal- en verzendwijze: <?= $order->payment_method()?></br>
<?= str_replace(array("\r\n", "\n", "\r"),"<br/>",$order->text);?>