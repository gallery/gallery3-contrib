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

<? if ($theme->page_type != 'basket'): ?>
  <? if (basket_plus::can_view_orders()): ?>
			<a href="<?= url::site("basket_plus/view_Orders") ?>"
				 title="<?= t("View Orders") ?>">View Orders</a>
  <? endif?>
  <? if (isset($basket) && isset($basket->contents) && ($basket->size() > 0)): ?>
		<div id="basket">
				<a href="<?= url::site("basket_plus/view_basket") ?>"
					 title="<?= t("View Basket") ?>">
					 <img src="<?= url::file("modules/basket/images/basket.png") ?>"><br/>
					 <?= $basket->size()?> items</a>
		</div>
  <? endif ?>
<? endif ?>
