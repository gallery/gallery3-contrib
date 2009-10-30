<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>
#g-registration-admin li input[type=radio],
#g-registration-admin li input[type=checkbox] {
  float: left;
  margin-right: .5em;
}

#g-registration-admin li label {
  float: left;
}

#g-registration-admin li h3 {
  clear: both;
}
</style>
<script type="text/javascript">
    $("#g-active-pending-users").ready(function() {
                                                     $
                                                   });
</script>
<div id="g-admin-register">
  <div id="g-registration-admin">
  <h2><?= t("Registration adminstration") ?></h2>
  <?= form::open($action, array("method" => "post"), $hidden) ?>
  <?= form::open_fieldset() ?>
    <ul>
      <li>
        <h3><?= t("Confirmation policy") ?></h3>
      </li>
        <? foreach ($policy_list as $policy => $text): ?>
      <li>
        <?= form::radio("policy", $policy, $policy == $form["policy"]) ?>
        <?= form::label("policy", $text) ?>
      </li>
        <? endforeach ?>
      <li>
        <?= form::checkbox("email_verification", "true", !empty($form["email_verification"]), $no_admin) ?>
        <?= form::label("email_verification", t("Require e-mail verification when a visitor creates an account")) ?>
      </li>
      <li>
        <h3><?= t("Default group") ?></h3>
      </li>
      <li>
        <?= form::dropdown(array("name" => "group"), $group_list, $form["default_group"]) ?>
      </li>
        <li>
        <?= form::submit(array("id" => "g-registration-admin", "name" => "save", "class" => "submit", "style" => "clear:both!important"), t("Update")) ?>
      </li>
    </ul>
  <?= form::close_fieldset() ?>
  <?= form::close() ?>
  </div>
  <? if (count($pending)): ?>
    <div id="g-activate-pending-users" style="margin-top: .5em">
      <?= form::open($activate, array("method" => "post"), $hidden) ?>
      <?= form::open_fieldset() ?>
    <ul>
      <li>
        <h3><?= t("Pending account activations") ?></h3>
      </li>
      <li>
        <table>
          <thead>
            <tr>
              <td><?= t("Activate") ?></td>
              <td><?= t("Confirmed") ?></td>
              <td><?= t("User name") ?></td>
              <td><?= t("Full name") ?></td>
              <td><?= t("Email") ?></td>
            </tr>
          </thead>
          <? foreach ($pending as $user): ?>
          <tr>
            <td><?= form::checkbox("activate[]", $user->id) ?>
            <td><?= form::checkbox("confirmed[$user->id]", "checked", $user->confirmed, "disabled") ?></td>
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
      <?= form::close_fieldset() ?>
      <?= form::close() ?>
    </div>
  <? endif ?>
</div>
