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
<?/* <h2><? t("Thank you for your order (Step 3 of 3)") ?></h2> */?>
<div class="g-block">
  <div id="b-complete">
  <h2><?= t("Order submitted (Step 3 of 3)") ?></h2><?
  if ($order->status == Bp_Order_Model::WAITING_PAYMENT){?>
		<?= basket_plus::replaceStringsAll(basket_plus::getBasketVar(ORDER_COMPLETE_PAGE),$order);
	}?><?
  if ($order->status == Bp_Order_Model::PAYMENT_CONFIRMED){?>
		<?= basket_plus::replaceStringsAll(basket_plus::getBasketVar(ORDER_PAID_COMPLETE_PAGE),$order);
	}?>
  </div>
</div>