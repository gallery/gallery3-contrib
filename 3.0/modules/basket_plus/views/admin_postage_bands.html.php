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
<div class="gBlock">

  <a href="<?= url::site("admin/postage_bands/add_postage_band_form") ?>"
      class="g-dialog-link g-button right ui-icon-left ui-state-default ui-corner-all"
      title="<?= t("Create a new Postage Band") ?>">
    <span class="ui-icon ui-icon-circle-plus"></span>
    <?= t("Add a new Postage Band") ?>
  </a>

  <h2>
    <?= t("Postage Bands") ?>
  </h2>
	<p> <?= t("Use this page to configure the packing and shipping costs. In case of delivery via email, use the checkbox Via Download.") ?>
	</p>
  <div class="g-block-content">
    <table id="g-postage-admin-list">
      <tr>
  <th><?= t("Name") ?></th>
        <th><?= t("Flat Rate") ?></th>
        <th><?= t("Per Item") ?></th>
        <th><?= t("Via Download") ?></th>
        <th><?= t("Actions") ?></th>

      </tr>
      <? foreach ($postage_bands as $i => $postage_band): ?>
      <tr id="g-product-<?= $postage_band->id ?>" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td id="product-<?= $postage_band->id ?>" class="core-info "><?= html::clean($postage_band->name) ?></td>
				<td><?= basket_plus::formatMoneyForWeb($postage_band->flat_rate) ?></td>
				<td><?= basket_plus::formatMoneyForWeb($postage_band->per_item) ?></td>
        <td class="core-info "><input id="via_download" type="checkbox" disabled="disabled" 
								<? if ($postage_band->via_download):?>checked="checked"<? endif; ?>
				/></td>
				<td class="g-actions"><a href="<?= url::site("admin/postage_bands/edit_postage_band_form/$postage_band->id") ?>"
          open_text="<?= t("close") ?>"class="g-panel-link g-button ui-state-default ui-corner-all ui-icon-left">
          <span class="ui-icon ui-icon-pencil"></span><?= t("edit") ?></a>
					<a href="<?= url::site("admin/postage_bands/delete_postage_band_form/$postage_band->id") ?>"class="g-dialog-link g-button ui-state-default ui-corner-all ui-icon-left">
            <span class="ui-icon ui-icon-trash"></span><?= t("delete") ?></a>
				</td>
			</tr>
      <? endforeach ?>
   </table>
  </div>

</div>