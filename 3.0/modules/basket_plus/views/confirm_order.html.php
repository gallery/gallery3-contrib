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
  function so(){
    document.confirm.submit();
  }
</SCRIPT>

<?= $form ?>
<div class="gBlock">
<h2><?= t("Order Summary (Step 2 of 3)") ?></h2>
  <div class="g-block-content scrollables">
    <table id="g-basket-list" class="pretty-table2">
      <tr>
        <th></th>
        <th><?= t("Photo") ?></th>
        <th><?= t("Product") ?></th>
        <th style="text-align:right;"><?= t("Quantity") ?></th>
        <th style="text-align:right;"><?= t("Cost") ?></th>
      </tr>
      <? foreach ($basket->contents as $key => $prod_details): ?>
        <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
	  <td id="item-<?= $prod_details->item ?>" class="core-info ">
	    <? $item = $prod_details->getItem(); ?>
	    <? $width = $item->width; /* added JtK */?>
	    <div id="basketThumb">
		  <a href="<?= $item->resize_url()?>" class="preview-image">
		    <img src="<?= $item->thumb_url()?>" title="<?= $item->title?>" alt="<?= $item->title?>
		  <? if ($width < module::get_var("gallery", "resize_size")):/* added JtK */?>
		    style="width=30px;"/></a>
		  <? else: ?>
		    style="width=45px;"/></a>
		  <? endif; ?>
	    </div>
	  </td>
          <td id="item-<?= $prod_details->item ?>" class="core-info ">
            <?  $item = $prod_details->getItem(); ?>
            <div>
              <?= html::clean($item->title) ?>
            </div>
          </td>
          <td><?= html::clean($prod_details->product_description()) ?></td>
          <td style="text-align:right;"><?= html::clean($prod_details->quantity) ?></td>
          <td style="text-align:right;"><?= basket::formatMoneyForWeb($prod_details->cost) ?></td>
        </tr>
      <? endforeach ?>
      <? $postage = $basket->postage_cost();?>
      <? if ($postage > 0):?>
        <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
          <td></td>
		  <td></td>
          <td <?=$basket->ispp()?"":"style=\"text-decoration:line-through\""; ?>><?= t("Packaging and Posting") ?></td>
          <td></td>
		  <td style="text-align:right;<?=$basket->ispp()?"":" text-decoration:line-through;";?>"><?= basket::formatMoneyForWeb($postage)?></td>
        </tr>
      <? endif;?>
      <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td></td>
        <td></td>
        <td><b><?= t("Total Cost") ?></b></td>
        <td></td>
        <td style="text-align:right;"><b><?= $basket->ispp()?basket::formatMoneyForWeb($basket->cost() + $postage):basket::formatMoneyForWeb($basket->cost()); ?></b></td>
          <? /*
        <td></td><td></td><td>Total Cost</td><td><?= $basket->ispp()?basket::formatMoneyForWeb($basket->cost() + $postage):basket::formatMoneyForWeb($basket->cost()); ?></td>
          */ ?>
      </tr>
    </table>
  </div>
  <table>
    <tr>
      <td>
	<? if ($basket->street <> ""):?>
		<h3><label><?= t("Name and Address") ?></label></h3>
	<? else :?>
		<h3><label><?= t("Name") ?></label></h3>
	<? endif;?>
	<?= basket::createFullName($basket) ?><br/>
	<? if ($basket->street <> ""):?>
		<?= $basket->street ?>&nbsp;<?= $basket->house ?><br/>
		<?= $basket->postcode ?>&nbsp;<?= $basket->town ?><br/>
	<? endif;?>
	<br/>
	<label><?= t("E-mail Address") ?>: </label><?= $basket->email ?><br/>
	<? if ($basket->phone <> ""):?>
		<label><?= t("Phone") ?>: </label><?= $basket->phone ?><br/>
	<? endif;?>
	<br/>
	<? $postage = $basket->postage_cost();
		$ppon = $basket->ispp();?>
	<label><?= t("Delivery")?>:</label>&nbsp;
	<? if (($ppon) && ($postage > 0)):?>
			<?= t("Delivery by mail") ?><br/>
	<? elseif ($postage > 0):?>
		<?  $pickup_location = basket::getPickupLocation(); ?>
		<?= t("Pickup at")." ".$pickup_location ?><br/>
	<? else:?>
		<?= t("Delivery by e-mail") ?><br/>
	<? endif;?>
	<br/>
	<label><input type="checkbox" checked=checked disabled=disabled/> <?= t(" I agree with the General Terms")?></label>
      </td>
      <td>
	<? if ($basket->childname <> ""):?>
		<br/>
		<label><?= t("Child's Name")?>:</label>&nbsp;<?= $basket->childname ?><br/>
		<label><?= t("Child's Group")?>:</label>&nbsp;<?= $basket->childgroup ?><br/>
	<? endif;?>
	<? if ($basket->comments <> ""):?>
		<br/>
		<b><label>Opmerking:</label></b> <?= $basket->comments ?>
		<br/>
	<? endif;?>
      </td>
    </tr>
  </table>

  <div class="basketbuttons">
    <? /* added BEGIN: to allow user to go back */?>
      <script language="JavaScript">
      function back(){
        history.go(-1);
      }
      </script>
    <a href="javascript:back();" class="left g-button ui-state-default ui-corner-all ui-icon-left">
      <span class="ui-icon ui-icon-arrow-1-w"></span><?= t("Back to Checkout") ?></a>
	<? /* added END */?>
    <? /*
    <a href="<?= url::site("basket/checkout") ?>" class="left g-button ui-state-default ui-corner-all ui-icon-left">
      <span class="ui-icon ui-icon-arrow-1-w"></span><?= t("Back to Checkout") ?></a>
	*/?>
    <a href="javascript: so()" class="right g-button ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Confirm Order") ?></a>
  </div>
</div>
