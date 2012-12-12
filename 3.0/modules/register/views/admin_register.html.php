<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
  $("#g-active-pending-users").ready(function() {
    $(":radio[name='policy']").click(function (event) {
      if ($(this).val() == "admin_only") {
        $(":checkbox[name=email_verification]").attr("disabled", "disabled");
      } else {
        $(":checkbox[name=email_verification]").removeAttr("disabled");
      }
      if ($(this).val() !== "admin_approval") {
        $(":checkbox[name=admin_notify]").attr("disabled", "disabled");
      } else {
        $(":checkbox[name=admin_notify]").removeAttr("disabled");
      }

    });
  });
</script>
<div id="g-admin-register" class="g-block">
  <h1><?= t("User registration administration") ?></h1>
  <div id="g-registration-admin" class="g-block-content">
  <?= form::open($action, array("method" => "post"), $hidden) ?>
    <fieldset>
      <legend><?= t("Confirmation policy") ?></legend>
      <ul>
          <? foreach ($policy_list as $policy => $text): ?>
        <li>
          <?= form::radio("policy", $policy, $policy == $form["policy"]) ?>
          <?= form::label("policy", $text) ?>
        </li>
          <? endforeach ?>
        <li>
          <?= form::checkbox("email_verification", "true", !empty($form["email_verification"]), $disable_email) ?>
          <?= form::label("email_verification", t("Require e-mail verification when a visitor creates an account")) ?>
        </li>
        <li>
          <?= form::checkbox("admin_notify", "true", !empty($form["admin_notify"]), $disable_admin_notify) ?>
          <?= form::label("admin_notify", t("Send a pending user registration notification email to the site 'reply to' email address")) ?>
        </li>
        <li>
          <?= form::input("subject_prefix", $form["subject_prefix"]) ?>
          <?= form::label("subject_prefix", t("Email subject line prefix, with trailing spaces as needed (e.g. '[Gallery3] ')")) ?>
        </li>
        <li>
          <? if (!empty($group_list)): ?>
          <label for="group" class="g-left"> <?= t("Default group: ") ?></label>
          <?= form::dropdown(array("name" => "group"), $group_list, $form["group"]) ?></label>
          <? else: ?>
          <?= form::hidden("group", $form["group"]) ?></label>
          <? endif ?>
        </li>
        <li>
          <?= form::submit(array("id" => "g-registration-admin", "name" => "save", "class" => "submit", "style" => "clear:both!important"), t("Update")) ?>
        </li>
      </ul>
    </fieldset>
  </form>
  </div>
  <? if (count($pending)): ?>
    <div id="g-activate-pending-users">
      <?= form::open($activate, array("method" => "post"), $hidden) ?>
        <fieldset>
          <legend><?= t("Pending registrations") ?></legend>
          <table>
          <caption>
            <?= t("To delete an unconfirmed registration, first activate it, then delete it from Users/Groups.") ?>
          </caption>
            <tr>
              <th><?= t("Activate") ?></th>
              <th><?= t("State") ?></th>
              <th><?= t("User name") ?></th>
              <th><?= t("Full name") ?></th>
              <th><?= t("Email") ?></th>
              <th><?= t("Registered") ?></th>
            </tr>
            <? foreach ($pending as $user): ?>
            <tr class="<?= text::alternate("g-odd", "g-even") ?>">
              <td>
                <? if ($user->state != 2): ?>
                <?= form::checkbox("activate[]", $user->id) ?>
                <? else: ?>
                &nbsp;
                <? endif ?>
              </td>
              <td><?= register::format_registration_state($user->state) ?></td>
              <td><?= t($user->name) ?></td>
              <td><?= t($user->full_name) ?></td>
              <td><a href="mailto:<?= t($user->email) ?>"><?= t($user->email) ?></a></td>
              <td><?= t(gallery::date_time($user->request_date)) ?></td>
            </tr>
            <? endforeach ?>
          </table>
          <?= form::submit(array("id" => "g-registration-activate", "name" => "activate_users", "class" => "submit"), t("Activate selected")) ?>
        </fieldset>
      </form>
    </div>
  <? endif ?>
</div>
