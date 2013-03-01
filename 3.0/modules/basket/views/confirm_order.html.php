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
<SCRIPT language="JavaScript">
function so(){document.confirm.submit();}
</SCRIPT>
<?= $form ?>
<div class="gBlock">
<h2>Basket Summary</h2>
  <div class="g-block-content scrollables">
    <table id="g-basket-list">
      <tr>
        <th><?= t("Name") ?></th>
        <th><?= t("Product") ?></th>
        <th><?= t("Quantity") ?></th>
        <th><?= t("Cost") ?></th>
      </tr>
      <? foreach ($basket->contents as $key => $prod_details): ?>
      <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">

        <td id="item-<?= $prod_details->item ?>" class="core-info ">
          <?  $item = $prod_details->getItem(); ?>
        <div>
          <?= html::clean($item->title) ?>
        </div>
        </td>
        <td>
          <?= html::clean($prod_details->product_description()) ?>
        </td>
        <td>
          <?= html::clean($prod_details->quantity) ?>
        </td>
        <td>
          <?= basket::formatMoneyForWeb($prod_details->cost) ?>
        </td>
    </tr>
      <? endforeach ?>
      <? $postage = $basket->postage_cost();?>
      <? if ($postage > 0):?>
      <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td></td><td></td><td  <?=$basket->ispp()?"":"style=\"text-decoration:line-through\""; ?>>Postage and Packaging</td><td  <?=$basket->ispp()?"":"style=\"text-decoration:line-through\""; ?>><?= basket::formatMoneyForWeb($postage)?></td>
      </tr>
      <? endif;?>
      <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td></td><td></td><td>Total Cost</td><td><?= $basket->ispp()?basket::formatMoneyForWeb($basket->cost() + $postage):basket::formatMoneyForWeb($basket->cost()); ?></td>
      </tr>

   </table>
  </div>
  <table>
  <tr><td>
<h2>Delivery Address</h2>
<?= $basket->name ?><br/>
<?= $basket->house ?>,
<?= $basket->street ?><br/>
<?= $basket->suburb ?><br/>
<?= $basket->town ?><br/>
<?= $basket->postcode ?><br/>
</td>
<td>
<h2>Contact Details</h2>
E-mail : <?= $basket->email ?><br/>
Telephone : <?= $basket->phone ?>
</td></tr>
</table>
<div class="basketbuttons">
<a href="<?= url::site("basket/checkout") ?>" class="left g-button ui-state-default ui-corner-all ui-icon-left">
<span class="ui-icon ui-icon-arrow-1-w"></span><?= t("Back to Checkout") ?></a>
<a href="javascript: so()" class="right g-button ui-state-default ui-corner-all ui-icon-right">
<span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Confirm Order") ?></a>
</div>
</div>
