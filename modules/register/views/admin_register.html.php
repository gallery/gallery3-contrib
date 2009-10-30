<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-register">
  <div id="g-registration-admin" style="float: left">
  <h2><?= t("Registration adminstration") ?></h2>
  <?= form::open($action, array("method" => "post"), $hidden) ?>
     <ul>
      <li>
        <h3><?= t("Confirmation policy") ?></h3>
        <p><?= t("The Gallery3 can accept new user registrations instantly, require the user to click a confirmation link in an email that is sent by the module, or require account activation by a site administrator.") ?><p/>
          <?= form::label("policy", t("Choose policy")) ?>
          <?= form::dropdown(array("name" => "policy"), $policy_list, $form["policy"]) ?>
      </li>
      <li>
        <h3><?= t("Default Group") ?></h3>
        <?= form::label("group", t("Set default group")) ?>
        <?= form::dropdown(array("name" => "group"), $group_list, $form["default_group"]) ?>
      </li>
      <li>
        <h3><?= t("Send email policy") ?></h3>
      </li>
      <li>
      <? if (empty($no_admin)): ?>
        <?= form::label("email_admin", t("Email administrator for all new registrations")) ?>
        <?= form::checkbox("email_admin", "true", !empty($form["email_admin"])) ?>
      </li>
      <li>
        <?= form::label("email_user", t("Send confirmation email on account activation")) ?>
        <?= form::checkbox("email_user", "true", !empty($form["email_user"])) ?>
     <? else: ?>
        <span <? if (!empty($errors["email_admin"])): ?> class="g-error"<? endif ?>>
          <?= form::hidden(array("name" => "email_admin", "value" => "")) ?>
          <?= form::hidden(array("name" => "email_user", "value" => "")) ?>
          <p class="g-error"><?= t("Unable to set email policies as the administrator email has not been set.") ?></p>
        </span>
      <?endif ?>
      </li>
       <li>
        <?= form::submit(array("id" => "g-registration-admin", "name" => "save", "class" => "submit", "style" => "clear:both!important"), t("Update")) ?>
      </li>
    </ul>
  <?= form::close() ?>
  </div>
  <? if (count($pending)): ?>
    <div id="g-activate-pending-users" style="float: left; margin-top: .5em">
      <h2><?= t("Pending account activations") ?></h2>
      <?= form::open($activate, array("method" => "post"), $hidden) ?>
     <ul>
      <li>
        <table>
          <thead>
            <tr>
              <td><?= t("Activate") ?></td>
              <td><?= t("User name") ?></td>
              <td><?= t("Full name") ?></td>
              <td><?= t("Email") ?></td>
            </tr>
          </thead>
          <? foreach ($pending as $user): ?>
          <tr>
            <td><?= form::checkbox("activate[]", $user->id) ?>
            <td><?= t($user->name) ?></td>
            <td><?= t($user->full_name) ?></td>
            <td><?= t($user->email) ?></td>
          </tr>
          <? endforeach ?>
        </table>
      </li>
       <li>
        <?= form::submit(array("id" => "g-registration-activate", "name" => "activate_users", "class" => "submit", "style" => "clear:both!important"), t("Activate")) ?>
      </li>
    </ul>
      <?= form::close() ?>
    </div>
  <? endif ?>
</div>
