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
<?if ($theme->page_subtype != "login"){
		if (basket_plus::can_view_orders()){
			 ?><a class="g-button ui-icon-left ui-state-default ui-corner-all ui-state-hover" 
			 href="<?= url::site("basket_plus/view_Orders") ?>" title="<?= t("View Orders") ?>">
			 <span class="ui-icon ui-icon-clipboard"></span><?= t("View Orders")?></a><?
		}
	}
	if ($theme->page_subtype == "photo" or $theme->page_subtype == "album"){
		if ($item = $theme->item()){
			if ($item->is_photo() && bp_product::isForSale($theme->item()->id)){
				?><p>
				<a class="g-dialog-link g-button ui-icon-left ui-state-default ui-corner-all ui-state-hover" href="<?= url::site("basket_plus/add_to_basket_ajax/$item->id") ?>"
				title="<?= t("Add To Basket")?>"><span class="ui-icon ui-icon-plusthick"></span><?= t("Add To Basket") ?></a></p>
				<?
			}
		}
		if (isset($basket) && isset($basket->contents) && ($basket->size() > 0)) {
			?>
			<div id="sidebar-basket">
				<table id="gBasketList">
					<tr>
						<th><?= t("Product") ?></th>
						<th><?= t("Cost") ?></th>
						<th></th>
					</tr><?

					$total = $basket->product_cost();
					$postage=0;
					foreach ($basket->contents as $key => $prod_details){
						?><tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
								<td id="item-<?= $prod_details->item ?>" class="core-info"><?
									$item = $prod_details->getItem();
									$width = $item->width;
									?><img src="<?= $item->thumb_url()?>" title="<?= $item->title?>" alt="<?= $item->title?>" 
									<? if ($width < module::get_var("gallery", "resize_size")):?>
										style="max-width:60px;"/><br/>
									<? else: ?>
										style="max-width:90px;"/><br/>
									<? endif; ?>
									<?= html::clean($prod_details->quantity) ?> x <?= html::clean($prod_details->product_name())/*= html::clean($prod_details->product_description())*/ ?></td>
								<td><?= basket_plus::formatMoneyForWeb($prod_details->product_cost); ?></td>
								<td class="g-actions"><a href="<?= url::site("basket_plus/remove_item/$key") ?>" 
									class="g-button2"><span class="ui-icon ui-icon-trash"></span></a></td>
							</tr>
						<?
					}
					?>
					<tr class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
						<td><b><?= t("Total") ?></b></td>
						<td id="total"><b><?= $basket->pickup?basket_plus::formatMoneyForWeb($total + $postage):basket_plus::formatMoneyForWeb($total)?></b></td>
						<td></td>
					</tr>
				</table>
			</div><br/>
			<p><a class="g-button right ui-icon-left ui-state-default ui-corner-all ui-state-hover" 
				href="<?= url::site("basket_plus/view_basket") ?>" title="<?= t("Checkout") ?>">
				<span class="ui-icon ui-icon-cart"></span><?= t("Checkout") ?></a></p><?
		}
		else {?>
			<div id="sidebar-basket">
				<?= t("Shopping Basket is Empty") ?>
			</div><?
		}
	}
