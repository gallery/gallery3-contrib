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

  <a href="<?= url::site("admin/postage_bands/add_postage_band_form") ?>"
      class="gDialogLink gButtonLink right ui-icon-left ui-state-default ui-corner-all"
      title="<?= t("Create a new Postage Band") ?>">
    <span class="ui-icon ui-icon-circle-plus"></span>
    <?= t("Add a new Postage Band") ?>
  </a>

  <h2>
    <?= t("Postage Bands") ?>
  </h2>

  <div class="gBlockContent">
    <table id="gPostageAdminList">
      <tr>
  <th><?= t("Name") ?></th>
        <th><?= t("Flat Rate") ?></th>
        <th><?= t("Per Item") ?></th>
        <th><?= t("Actions") ?></th>

      </tr>
      <? foreach ($postage_bands as $i => $postage_band): ?>
      <tr id="gProduct-<?= $postage_band->id ?>" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td id="product-<?= $postage_band->id ?>" class="core-info ">
          <?= html::clean($postage_band->name) ?>
  </td>
  <td>
    <?= basket::formatMoney($postage_band->flat_rate) ?>
        </td>
  <td>
    <?= basket::formatMoney($postage_band->per_item) ?>
  </td>
    <td class="gActions">
      <a href="<?= url::site("admin/postage_bands/edit_postage_band_form/$postage_band->id") ?>"
          open_text="<?= t("close") ?>"
          class="gPanelLink gButtonLink ui-state-default ui-corner-all ui-icon-left">
          <span class="ui-icon ui-icon-pencil"></span><span class="gButtonText"><?= t("edit") ?></span></a>

      <a href="<?= url::site("admin/postage_bands/delete_postage_band_form/$postage_band->id") ?>"
          class="gDialogLink gButtonLink ui-state-default ui-corner-all ui-icon-left">
            <span class="ui-icon ui-icon-trash"></span><?= t("delete") ?></a>
      </td>

  </tr>
      <? endforeach ?>
   </table>
  </div>

</div>