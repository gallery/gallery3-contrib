<?php defined("SYSPATH") or die("No direct script access.")
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
?>
<div class="basketbuttons">
<form action="javascript: so()" method="post" id="show_order" name="show_order">
<input type="hidden" name="csrf" value="<?= $csrf ?>"  />
  <label for="orderno" ><?= t("Order Number") ?></label>
  <input type="text" id="orderno" name="orderno" value="" class="textbox"  />
  <a href="javascript: so()" class="g-button ui-state-default ui-icon-right">
  <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Search") ?></a>
  <a style="display:none" id="print_button" href="" class="g-button ui-state-default ui-icon-right">
  <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Print") ?></a>
  <a style="display:none" id="print_button" href="" class="g-button ui-state-default ui-icon-right">
  <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("No products except..") ?></a>
</form>
</div>
<div class="left" style="width:600px;float:left;font-size:12px;display:block;">
  <table id="order_ovw" class="pretty-table">
      <tr>
        <th>Bestelnummer</th>
        <th>Naam</th>
        <th>Bestelstatus</th>
        <th>Verzending</th>
        <th>Bedrag</th>
      </tr>     
    <? foreach ($orders as $i => $order){
      ?>
      <tr class="order-status-<?=$order->status?>">
        <td class="order-status-<?=$order->status?>"><a href="javascript:loadOrder(<?=$order->id?>)"><?=basket::getOrderPrefix().$order->id?></a></td>
        <td><?=$order->name?></td>
        <? $id=$order->id;?>
        <?/*
        <td class="order-status-<?=$order->status?>"><a href="javascript:loadOrder(<?=$order->id?>)"><?=basket::getOrderPrefix().$order->id?>
        <td><a href="<?=url::site("basket/view_order_logs(".$id.")")?>"><?=$order->status()?></a></td>
        <td><?=$order->status()?></td>
        */?>
        <td class="order-status-<?=$order->status?>"><a href="javascript:loadOrderLog(<?=$order->id?>)" alt="toon order historie"><?=$order->status()?></a></td>
        <td class="order-status-<?=$order->status?>"><?=$order->payment_method()?></td>
        <td style="text-align:right;"><?=basket::formatMoneyForWeb($order->cost)?></td>
      </tr>     
    <?
    $total=$total+$order->cost;
    }
    ?>
      <tr class="order-status-<?=$order->status?>">
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align:right;"><b>Totaal</b></td>
        <td style="text-align:right;"><b><?=basket::formatMoneyForWeb($total)?></b></td>
      </tr>     
  </table>
</div>
<div class="scrollable" style="text-align:left;float:left;padding:0;font-size:12px;display:block;"><pre id="order_text"></pre>
</div>

<SCRIPT language="JavaScript">
var doc,printButton,orderText,csrf;
$(window).load(new function(){
  doc=document.show_order;
  printButton=$("#print_button");
  orderText=$("#order_text");
  csrf="?csrf="+doc.csrf.value
  });

function se(v){v.style.backgroundColor="#FAA";}

function re(v){v.style.backgroundColor="#FFF";}

function ci(v){if ((!v.value)||(v.value.length==0)){se(v);return false;}re(v);return true;}

function loadOrder(n){
  printButton.css({display:'none'});
  orderText.html("Loading...");
  orderText.load('<?=url::site("basket/show_order")?>/'+n+csrf,
    function (responseText, textStatus, XMLHttpRequest) {
      if (textStatus == "success") {
        doc.orderno.value=n;
        printButton.css({display:'inline-block'});
        printButton.attr({target: "_blank",href : '<?=url::site("basket/print_order")?>/'+n+csrf});
      }
      if (textStatus == "error") {
        orderText.html(responseText);
        printButton.css({display:'none'});
      }
    }
  );
}
//load 
function loadOrderLog(n){
  printButton.css({display:'none'});
  orderText.html("Loading...");
  orderText.load('<?=url::site("basket/show_order_logs")?>/'+n+csrf,
    function (responseText, textStatus, XMLHttpRequest) {
      if (textStatus == "success") {
        doc.orderno.value=n;
      }
      if (textStatus == "error") {
        orderText.html(responseText);
      }
    }
  );
}

function so(){
  printButton.css({display:'none'});
  if(ci(doc.orderno)){
    loadOrder(doc.orderno.value);
  }
}
</SCRIPT>
