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
<div class="basketbuttons">
<form action="javascript: so()" method="post" id="show_order" name="show_order">
<input type="hidden" name="csrf" value="<?= $csrf ?>"  />
  <label for="orderno" >Order Number</label>
  <input type="text" id="orderno" name="orderno" value="" class="textbox"  />
  <a href="javascript: so()" class="g-button ui-state-default ui-icon-right">
  <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Search") ?></a>
  <a style="display:none" id="print_button" href="" class="g-button ui-state-default ui-icon-right">
  <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Print") ?></a>
</form>
</div>
<div class="left" style="width:150px;float:left;font-size:10px;display:block;">
<ul>
<?
  foreach ($orders as $i => $order){
    ?><li class="order-status-<?=$order->status?>"><a href="javascript:ld(<?=$order->id?>)"><?= $order->title()?></a></li><?
  }
?>
</ul>
</div>
<div class="scrollable" style="text-align:left;float:left;padding:0;font-size:12px;display:block;"><pre id="order_text"></pre>
</div>
<SCRIPT language="JavaScript">
var d,pb,ot,csrf;
$(window).load(new function(){d=document.show_order;pb=$("#print_button");ot=$("#order_text");csrf="?csrf="+d.csrf.value});

function se(v){v.style.backgroundColor="#FAA";}

function re(v){v.style.backgroundColor="#FFF";}

function ci(v){if ((!v.value)||(v.value.length==0)){se(v);return false;}re(v);return true;}

function ld(n){
pb.css({display:'none'});
ot.html("Loading...");
ot.load('<?=url::site("basket/show_order")?>/'+n+csrf,
function (responseText, textStatus, XMLHttpRequest) {
if (textStatus == "success") {d.orderno.value=n;pb.css({display:'inline-block'});
pb.attr({target: "_blank",href : '<?=url::site("basket/print_order")?>/'+n+csrf});}
if (textStatus == "error") {ot.html(responseText);pb.css({display:'none'});}
});
}

function so(){
pb.css({display:'none'});
if(ci(d.orderno)){ld(d.orderno.value);}}
</SCRIPT>
