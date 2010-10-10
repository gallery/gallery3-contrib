
<h2><?= $order->title()?></h2>
Payment is <?= $order->payment_method()?><?

 if ($order->status==Order_Model::WAITING_PAYMENT){
    ?><br/><a href="<?= url::site("basket/confirm_order_payment/".$order->id)."?csrf=$csrf";?>">Confirm Order Payment</a> <?
}

 if ($order->status==Order_Model::PAYMENT_CONFIRMED){
    ?><br/><a href="<?= url::site("basket/confirm_order_delivery/".$order->id)."?csrf=$csrf";?>">Confirm Order Delivery</a> <?
}
if ($order->method==Order_Model::PAYMENT_PAYPAL){
    ?><br/><a href="<?= url::site("basket/view_ipn/".$order->id);?>">View Paypal IPN Messages</a><?
}


?><br/>
<?= str_replace(array("\r\n", "\n", "\r"),"<br/>",$order->text);?>