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
  function submitorder(){
    document.confirm.submit();
  }
	function back(){
		history.go(-1);
	}
</SCRIPT>

<?= $form ?>
<div class="g-Block">
<h2><?= t("Order Summary (Step 2 of 3)") ?></h2>
  <div class="g-block-content scrollables">
    <table id="g-basket-list" class="bp-table-small">
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
			  <? $width = $item->width;?>
			  <div id="basketThumb">
				<a href="<?= $item->resize_url()?>" class="preview-image">
				  <img src="<?= $item->thumb_url()?>" title="<?= $item->title?>" alt="<?= $item->title?>"/></a>
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
          <td style="text-align:right;"><?= basket_plus::formatMoneyForWeb($prod_details->product_cost) ?></td>
        </tr>
      <? endforeach ?>
      <? $postage = $basket->postage_cost();?>
      <? if ($postage > 0):?>
        <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
          <td></td>
					<td></td>
          <td <?=$basket->pickup?" text-decoration:line-through;":""; ?>><?= t("Packaging and Posting") ?></td>
          <td></td>
					<td style="text-align:right;<?=$basket->pickup?" text-decoration:line-through;":"";?>"><?= basket_plus::formatMoneyForWeb($postage)?></td>
        </tr>
      <? endif;?>
      <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td></td>
        <td></td>
        <td><b><?= t("Total Cost") ?></b></td>
        <td></td>
        <td style="text-align:right;"><b><?= $basket->pickup?basket_plus::formatMoneyForWeb($basket->product_cost()):basket_plus::formatMoneyForWeb($basket->product_cost() + $postage); ?></b></td>
      </tr>
    </table>
  </div>
  <table class="bp-table-noborder">
    <tr>
      <td>
				<? if ($basket->street <> ""):?>
					<h3><?= t("Name and Address") ?></h3>
				<? else :?>
					<h3><?= t("Name") ?></h3>
				<? endif;?>
				<?= basket_plus::createFullName($basket) ?><br/>
				<?= basket_plus::getAddressHtml($basket) ?>
				<? /*if ($basket->street <> ""):?>
					<?= $basket->street ?>&nbsp;<?= $basket->house ?>&nbsp;<?= $basket->suburb ?><br/>
					<?= $basket->postalcode ?>&nbsp;<?= $basket->town ?><br/>
				<? endif;?>
				<? if ($basket->province <> ""):?>
					<?= $basket->province?><br/>
				<? endif;?>
				<? if ($basket->country <> ""):?>
					<?= $basket->country?><br/>
				<? endif;*/?>
				<br/>
				<?= t("E-mail") ?>: <?= $basket->email ?><br/>
				<? if ($basket->phone <> ""):?>
					<?= t("Phone") ?>: <?= $basket->phone ?><br/>
				<? endif;?>
				<br/>
				<input type="checkbox" checked=checked disabled=disabled/> <?= t(" I agree with the General Terms")?>
      </td>
      <td>
				<? $postage = $basket->postage_cost();
					$pickup = $basket->pickup; 
					$delivery_method = basket_plus::getDeliveryMethod($pickup, $postage);
					$label_strong = true;?>
				<?= basket_plus::getDeliveryMethodHtml($delivery_method,$label_strong); ?>
				<? if ($basket->paypal):
					$payment_method = Bp_Order_Model::PAYMENT_PAYPAL;?>
				<? else :
					$payment_method = Bp_Order_Model::PAYMENT_OFFLINE;?>
				<? endif;?>
				<?= basket_plus::getPaymentMethodHtml($payment_method,$label_strong); ?>
				<br/>	
				<? if ($basket->order_ref1 <> ""):
						$user = identity::active_user();
						$user_basket = ORM::factory("bp_user_basket")->where("id", "=", $user->id)->find();
						$extra_order_info_lbl = $user_basket->extra_order_info_lbl;
						if ($extra_order_info_lbl == ""):
							$extra_order_info_lbl = t("Order reference");
						endif;?>
						<strong><?= t($extra_order_info_lbl)?></strong>:&nbsp;<?= $basket->order_ref1 ?><br/>
				<? endif;?>
				<? if ($basket->order_ref2 <> ""):
						$extra_order_info_lbl2 = $user_basket->extra_order_info_lbl2;
						if ($extra_order_info_lbl2 == ""):
							$extra_order_info_lbl2 = t("Order reference 2");
						endif;?>
						<strong><?= t($extra_order_info_lbl2)?></strong>:&nbsp;<?= $basket->order_ref2 ?><br/>
				<? endif;?>
				<? if ($basket->comments <> ""):?>
					<br/>
					<b><?= t(basket_plus_label::TEXT_MAIL_ORDER_COMMENT)?>:</b>&nbsp;<?= $basket->comments ?>
					<br/>
				<? endif;?>
      </td>
    </tr>
  </table>

  <div class="basketbuttons">
		<? if ($basket->paypal):
					$label_confirm = t("Confirm Order and Pay"); ?>
		<? else :
					$label_confirm = t("Confirm Order"); ?>
		<? endif;?>
		<? $label_back = t("Back to Checkout")	?>
    <a href="javascript: back()" class="left g-button ui-state-default ui-corner-all ui-icon-left">
      <span class="ui-icon ui-icon-arrow-1-w"></span><?= $label_back ?></a>
    <a href="javascript: submitorder()" class="right g-button ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= $label_confirm ?></a>
  </div>
</div>
