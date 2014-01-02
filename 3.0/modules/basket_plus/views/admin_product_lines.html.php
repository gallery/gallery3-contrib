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
<div class="g-block">

  <a href="<?= url::site("admin/product_lines/add_product_form") ?>"
      class="g-dialog-link g-button right ui-icon-left ui-state-default ui-corner-all"
      title="<?= t("Create a new Product") ?>">
    <span class="ui-icon ui-icon-circle-plus"></span>
    <?= t("Add a new Product") ?>
  </a>

  <h2><?= t("Product Lines") ?></h2>

  <div class="g-block-content">
    <table id="g-product-admin-list">
      <tr>
        <th><?= t("Name") ?></th>
        <th><?= t("Cost") ?></th>
        <th><?= t("Description") ?></th>
        <th><?= t("Postage Band") ?></th>
        <th><?= t("Actions") ?></th>
      </tr>
    <? foreach ($products as $i => $product): ?>
      <tr id="gProduct-<?= $product->id ?>" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td id="product-<?= $product->id ?>" class="core-info ">
          <?= html::clean($product->name) ?></td>
        <td><?= basket_plus::formatMoneyForWeb($product->cost) ?></td>
        <td><?= html::clean($product->description) ?></td>
        <td><?= html::clean($product->bp_postage_band->name) ?></td>
        <td class="g-actions"><a href="<?= url::site("admin/product_lines/edit_product_form/$product->id") ?>"
          open_text="<?= t("close") ?>"
          class="g-panel-link g-button ui-state-default ui-corner-all ui-icon-left">
          <span class="ui-icon ui-icon-pencil"></span><span class="gButtonText"><?= t("edit") ?></span></a>
          <a href="<?= url::site("admin/product_lines/delete_product_form/$product->id") ?>"
            class="g-dialog-link g-button ui-state-default ui-corner-all ui-icon-left">
            <span class="ui-icon ui-icon-trash"></span><?= t("delete") ?></a>
        </td>
      </tr>
    <? endforeach ?>
    </table>
  </div>
</div>