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
<script>
	function back(){
		history.go(-1);
	}
	var b,d;
	function previewImage(element){
		var ele=$(element),i=$(document.createElement('div')),img=$(new Image());
		img.load(function(){
			var o=$('<div></div>').appendTo(document.body).addClass('ui-widget-overlay').css({width:d.width(),height:d.height()});
			i.css({'position':'absolute',left:(b.width()/2)-(this.width / 2),top:(b.height()/2)-(this.height/2)}).click(function(){i.remove();o.remove();}).append(img);

			$("body").append(i);
		}).attr('src',ele.attr('href'));
		return false;
	}
	$(window).load(function(){
		b=$("body");d=$("document");
		$("#gBasketList").find(".preview-image").bind("click",function(){return previewImage(this)});
	});
	
</script>
<div class="g-block">
<h2><?= t("Basket Contents") ?></h2>
  <div class="basketbuttons">
    <? if (isset($basket->contents ) && count($basket->contents) > 0): ?>
      <script language="JavaScript">
      $(document).ready(function(){
        $("#pickup").click(function(){
          if (this.checked){
						window.location = "<?= url::site("basket_plus/view_basket/pickup") ?>";
          }
          else{
            window.location = "<?= url::site("basket_plus/view_basket/nopickup") ?>";
          }
        });
      })
      </script>
    <? endif; ?>
  </div>
  <div class="g-block-content scrollable">
    <? if (isset($basket->contents ) && count($basket->contents) > 0): ?>
      <table id="gBasketList" class="bp-table-small">
        <tr>
          <th></th>
          <th><?= t("Product") ?></th>
          <th style="text-align:right;"><?= t("Quantity") ?></th>
          <th style="text-align:right;"><?= t("Cost") ?></th>
          <th><?= t("Actions") ?></th>
        </tr>
        <? $total=0;?>

        <? foreach ($basket->contents as $key => $prod_details): ?>
          <tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
            <td id="item-<?= $prod_details->item ?>" class="core-info ">
              <? $item = $prod_details->getItem(); ?>
              <? $width = $item->width; ?>
              <div id="basketThumb">
                <a href="<?= $item->resize_url()?>" class="preview-image">
                  <img src="<?= $item->thumb_url()?>" title="<?= $item->title?>" alt="<?= $item->title?>"
                <? if ($width < module::get_var("gallery", "resize_size")):
                ?> style="width=60px;"/></a><?
                 else: 
                ?> style="width=90px;"/></a><?
                 endif; ?>
              </div>
            </td>
            <td><?= html::clean($prod_details->product_description()) ?></td>
            <td style="text-align:right;"><?= html::clean($prod_details->quantity) ?></td>
            <td style="text-align:right;"><?= basket_plus::formatMoneyForWeb($prod_details->product_cost); ?></td>
            <td style="text-align:center;"><a href="<?= url::site("basket_plus/remove_item/$key") ?>" class="g-button2">
              <span class="ui-icon ui-icon-trash" title="<?= t("Remove") ?>"></span></a>
            </td>
          </tr>
        <? endforeach ?>
        <? /* line with postage: show only when postage > 0; when pickup is selected, use 'line-through' formatting */ ?>
        <? $postage = $basket->postage_cost();?>
        <? $total = $basket->product_cost();?>
        <? if ($postage > 0):?>
          <tr class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
            <td></td>
            <td <?=$basket->pickup?"style=\"text-decoration:line-through\"":""; ?>><?= t("Postage and Packaging") ?></td>
            <td></td>
						<td style="text-align:right;<?=$basket->pickup?" text-decoration:line-through;":"";?>"><?= basket_plus::formatMoneyForWeb($postage)?></td>
            <td></td>
          </tr>
        <? endif;?>
        <? /* line with total:  show total incl postage when postage > 0;*/ ?>
        <tr class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
          <td></td>
          <td><b><?= t("Total Cost") ?></b></td>
          <td></td>
          <td style="text-align:right;" id="total"><b><?= $basket->pickup?basket_plus::formatMoneyForWeb($total):basket_plus::formatMoneyForWeb($total + $postage)?></b></td>
          <td></td>
        </tr>
        <? /* line with pickup choice: show only when postage > 0; when pickup is changed, trigger script (see top) */ ?>
        <? if ($postage > 0):?>
          <? if (basket_plus::getBasketVar(ALLOW_PICKUP)):?>
            <tr class="bp-table-noborder">
              <td colspan="5"><input id="pickup" type="checkbox" <?=$basket->pickup?"checked":""; ?>/><?= t(" Select if you wish to pick up the photos.") ?></td>
            </tr>
          <? endif;?>
        <? endif;?>
      </table>
    <? else: ?>
      <?= t("Shopping Basket is Empty") ?>
    <? endif; ?>
  </div>

  <div class="basketbuttons">
    <? if (isset($basket->contents ) && count($basket->contents) > 0): ?>
      <a href="javascript:back();"
        class="left g-button ui-state-default ui-corner-all ui-icon-left">
        <span class="ui-icon ui-icon-arrow-1-w"></span><?= t("Back to Photos") ?></a>
      <a href="<?= url::site("basket_plus/checkout") ?>"
        class="right g-button ui-state-default ui-corner-all ui-icon-right">
          <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Proceed to Checkout") ?></a>
    <? endif; ?>
  </div>
</div>