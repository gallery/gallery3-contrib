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
<h1>IPN Messages for <?= $order->title()?></h1>
<a href="<?=url::site("basket_plus/view_orders");?>">Back to orders</a>
<div class="left" style="width:150px;float:left;font-size:10px;">

<ul>
<?
  foreach ($ipn_messages as $i => $ipn_message){
    ?><li><a href="javascript:ld(<?=$ipn_message->id?>)"><?= $ipn_message->date." ".$ipn_message->status ?></a></li><?
  }
?>
</ul>
</div>
<div class="scrollable" style="text-align:left;float:left;padding:0;font-size:12px;display:block;"><pre id="ipn_text"></pre>
</div>
<SCRIPT language="JavaScript">
var ot,csrf;
$(window).load(new function(){ot=$("#ipn_text");csrf="?csrf=<?= $csrf ?>"});
function ld(n){
ot.html("Loading...");
ot.load('<?=url::site("basket_plus/show_ipn")?>/'+n+csrf,
function (responseText, textStatus, XMLHttpRequest) {
if (textStatus == "error") {ot.html(responseText);}
});
}

</SCRIPT>
