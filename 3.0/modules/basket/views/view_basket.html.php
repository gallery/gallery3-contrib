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
var b,d;
function previewImage(element)
{
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
<div class="basketbuttons">
  <? if (isset($basket->contents ) && count($basket->contents) > 0): ?>

  <script language="JavaScript">

  $(document).ready(function(){
    $("#pickup").click(function(){
      if (this.checked)
      {
        window.location = "<?= url::site("basket/view_basket/nopp") ?>";
      }
      else
      {
          window.location = "<?= url::site("basket/view_basket/ppon") ?>";
      }
    });
  })
  </script>

  <a href="<?= url::site("basket/checkout") ?>"
    class="right g-button ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Proceed to Checkout") ?></a>
<? endif; ?>
</div>
  <div class="g-block-content scrollable">
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
        <a href="<?= $item->resize_url()?>" class="preview-image">
          <img src="<?= $item->thumb_url()?>" title="<?= $item->title?>" alt="<?= $item->title?>" />
          </a>
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
          <?= basket::formatMoneyForWeb($prod_details->cost); ?>
        </td>
        <td class="g-actions">

        <a href="<?= url::site("basket/remove_item/$key") ?>"
          class="g-button ui-state-default ui-corner-all ui-icon-left">
            <span class="ui-icon ui-icon-trash"></span><?= t("Remove") ?></a>
      </td>
  </tr>
      <? endforeach ?>
      <? $postage = $basket->postage_cost();?>
      <? if ($postage > 0):?>
      <tr class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td></td><td></td><td <?=$basket->ispp()?"":"style=\"text-decoration:line-through\""; ?>>Postage and Packaging</td><td <?=$basket->ispp()?"":"style=\"text-decoration:line-through\""; ?>><?= basket::formatMoneyForWeb($postage)?></td><td>
        </td>
      </tr>
      <? if (basket::isAllowPickup()):?>
      <tr class="<?= text::alternate("gOddRow", "gEvenRow") ?>"><td colspan="5"><input id="pickup" type="checkbox" <?=$basket->ispp()?"":"checked"; ?>/> Select if you wish to pick up the photos.</td></tr>
      <? endif;?>
      <? endif;?>
      <tr class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td></td><td></td><td>Total Cost</td><td id="total"><?= $basket->ispp()?basket::formatMoneyForWeb($total + $postage):basket::formatMoneyForWeb($total)?></td><td></td>
      </tr>

   </table>

      <? else: ?>
      Shopping Basket is Empty
      <? endif; ?>

  </div>

<div class="basketbuttons">
  <? if (isset($basket->contents ) && count($basket->contents) > 0): ?>

  <a href="<?= url::site("basket/checkout") ?>"
    class="right g-button ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Proceed to Checkout") ?></a>
  <? endif; ?>
  </div>
</div>