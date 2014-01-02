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
<div class="g-block">

  <a href="<?= url::site("admin/email_templates/add_email_template_form") ?>"
      class="g-dialog-link g-button right ui-icon-left ui-state-default ui-corner-all"
      title="<?= t("Create a new Email template") ?>">
    <span class="ui-icon ui-icon-circle-plus"></span>
    <?= t("Add a new Email template") ?>
  </a>

  <h2><?= t("Email templates") ?></h2>
<p> <?= t("Use this page to configure the email templates used with the order process.<br>
By using variables, the emails can be personalised. Please read the documentation for more information about variables you can use in the email templates. Default templates are available in the directory '!install'.<br>
You can style the html emails with the CSS code in setting 'Email Template Style' (under menu Templates).") ?>
</p>

  <div class="g-block-content">
    <table id="g-email_template-admin-list">
      <tr>
        <th><?= t("Name") ?></th>
        <th><?= t("Actions") ?></th>
      </tr>
    <? foreach ($email_templates as $i => $email_template): ?>
      <tr id="gEmailTemplate-<?= $email_template->id ?>" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
        <td id="email_template-<?= $email_template->id ?>" class="core-info ">
          <?= html::clean($email_template->name) ?></td>
        <td class="g-actions"><a href="<?= url::site("admin/email_templates/edit_email_template_form/$email_template->id") ?>"
          open_text="<?= t("close") ?>"
          class="g-panel-link g-button ui-state-default ui-corner-all ui-icon-left">
          <span class="ui-icon ui-icon-pencil"></span><span class="gButtonText"><?= t("edit") ?></span></a>
          <a href="<?= url::site("admin/email_templates/delete_email_template_form/$email_template->id") ?>"
            class="g-dialog-link g-button ui-state-default ui-corner-all ui-icon-left">
            <span class="ui-icon ui-icon-trash"></span><?= t("delete") ?></a>
        </td>
      </tr>
    <? endforeach ?>
    </table>
  </div>
</div>