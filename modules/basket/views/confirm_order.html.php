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
<SCRIPT language="JavaScript">
function so(){document.confirm.submit();}
</SCRIPT>
<?= $form ?>
<div class="g-block">
<h2>Basket Summary</h2>
  <div class="g-block-content">
    <table id="g-basket-list">
      <tr>
        <th><?= t("Name") ?></th>
        <th><?= t("Product") ?></th>
        <th><?= t("Quantity") ?></th>
        <th><?= t("Cost") ?></th>
      </tr>
      <? foreach ($basket->contents as $key => $prod_details): ?>
      <tr id="" class="<?= text::alternate("g-odd", "g-even") ?>">
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
          <?= html::clean(basket::formatMoney($prod_details->cost)) ?>
        </td>
      </tr>
      <? endforeach ?>
      <? $postage = $basket->postage_cost();?>
      <? if ($postage > 0):?>
      <tr id="" class="<?= text::alternate("g-odd", "g-even") ?>">
        <td></td><td></td><td>Postage and Packaging</td><td><?= html::clean(basket::formatMoney($postage))?></td><td></td>
      </tr>
      <? endif;?>
      <tr id="" class="<?= text::alternate("g-odd", "g-even") ?>">
        <td></td><td></td><td>Total Cost</td><td><?= html::clean(basket::formatMoney($basket->cost() + $postage))?></td>
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
<a href="<?= url::site("basket/checkout") ?>" class="g-left g-button ui-state-default ui-corner-all ui-icon-left">
<span class="ui-icon ui-icon-arrow-1-w"></span><?= t("Back to Checkout") ?></a>
<a href="javascript: so()" class="g-right g-button ui-state-default ui-corner-all ui-icon-right">
<span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Confirm Order") ?></a>

</div>
