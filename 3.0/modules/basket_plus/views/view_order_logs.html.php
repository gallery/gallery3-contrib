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
<div class="left" style="width:600px;float:left;font-size:12px;display:block;">
<h2><?= t("Order History") ?></h2>
  <table id="order_ovw" class="bp-table">
      <tr>
        <th><?= t("Order Number") ?></th>
        <th><?= t("Order Status") ?></th>
        <th><?= t("Event") ?></th>
        <th><?= t("Date/Time") ?></th>
      </tr>     
    <? foreach ($order_logs as $i => $order_log){
      ?>
      <tr>
        <td><?=basket_plus::getBasketVar(ORDER_PREFIX).$order_log->id?></td>
        <td><?=$order_log->status()?></td>
        <td><?=$order_log->event()?></td>
        <td><?=gallery::date_time($order_log->timestamp)?></td>
      </tr>     
    <?
    }
    ?>
  </table>
</div>
