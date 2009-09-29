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
<div class="gBlock">
  <? if (isset($basket->contents ) && count($basket->contents) > 0): ?>

  <? if (basket::isPaypal()): ?>
  <?= basket::generatePaypalForm($basket) ?>
  <script language="JavaScript">
  function co(){
    var d=document.paypal_form.submit();
  }</script>
  <a href="javascript:co();"
    class="right gButtonLink ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Pay with Credit Card or Paypal") ?></a>
  <a href="<?= url::site("basket/checkout") ?>"
    class="right gButtonLink ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Pay off line") ?></a>
  <? else: ?>
  <a href="<?= url::site("basket/checkout") ?>"
    class="right gButtonLink ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Proceed to Checkout") ?></a>
  <? endif; ?>
<? endif; ?>
  <h2>
    <?= t("Shopping Basket") ?>
  </h2>

  <div class="gBlockContent">
      <? if (isset($basket->contents ) && count($basket->contents) > 0): ?>

    <table id="gBasketList">
      <tr>
  <th><?= t("Picture") ?></th>
        <th><?= t("Product") ?></th>
        <th><?= t("Quantity") ?></th>
        <th><?= t("Cost") ?></th>
        <th><?= t("Actions") ?></th>

      </tr>
      <? $total=0;?>

      <? foreach ($basket->contents as $key => $prod_details): ?>
      <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">

        <td id="item-<?= $prod_details->item ?>" class="core-info ">
          <?  $item = $prod_details->getItem(); ?>
        <div id="basketThumb">
          <img src="<?= $item->thumb_url()?>" title="<?= $item->title?>" alt="<?= $item->title?>" />
        </div>
        </td>
        <td>
          <?= html::clean($prod_details->product_description()) ?>
        </td>
        <td>
          <?= html::clean($prod_details->quantity) ?>
        </td>
        <td>
          <? $total += $prod_details->cost?>
          <?= html::clean(basket::formatMoney($prod_details->cost)) ?>
        </td>
        <td class="gActions">
        <!-- a href="<?= url::site("admin/product_lines/edit_product_form/") ?>"
          open_text="<?= t("close") ?>"
          class="gPanelLink gButtonLink ui-state-default ui-corner-all ui-icon-left">
          <span class="ui-icon ui-icon-pencil"></span><span class="gButtonText"><?= t("edit") ?></span></a-->

        <a href="<?= url::site("basket/remove_item/$key") ?>"
          class="gButtonLink ui-state-default ui-corner-all ui-icon-left">
            <span class="ui-icon ui-icon-trash"></span><?= t("Remove") ?></a>
      </td>
  </tr>
      <? endforeach ?>
      <? $postage = $basket->postage_cost();?>
      <? if ($postage > 0):?>
      <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td></td><td></td><td>Postage and Packaging</td><td><?= html::clean(basket::formatMoney($postage))?></td><td></td>
      </tr>
      <? endif;?>
      <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td></td><td></td><td>Total Cost</td><td><?= html::clean(basket::formatMoney($total + $postage))?></td><td></td>
      </tr>

   </table>
      <? else: ?>
      Shopping Basket is Empty
      <? endif; ?>

  </div>

  <? if (isset($basket->contents ) && count($basket->contents) > 0): ?>

  <? if (basket::isPaypal()): ?>
  <a href="javascript:co();"
    class="right gButtonLink ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Pay with Credit Card or Paypal") ?></a>
  <a href="<?= url::site("basket/checkout") ?>"
    class="right gButtonLink ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Pay off line") ?></a>
  <? else: ?>
  <a href="<?= url::site("basket/checkout") ?>"
    class="right gButtonLink ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Proceed to Checkout") ?></a>
  <? endif; ?>
  <? endif; ?>
</div>