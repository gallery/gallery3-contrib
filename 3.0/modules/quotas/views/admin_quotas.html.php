<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-block">
  <h1> <?= t("User quotas") ?> </h1>

  <div class="g-block-content">

    <div id="g-user-admin" class="g-block">

      <h2> <?= t("Users") ?> </h2>

      <div class="g-block-content">
        <table id="g-user-admin-list">
          <tr>
            <th><?= t("Username") ?></th>
            <th><?= t("Full name") ?></th>
            <th><?= t("Fullsize") ?></th>
            <th><?= t("Resize") ?></th>
            <th><?= t("Thumbs") ?></th>
            <th><?= t("Total") ?></th>
            <th><?= t("Limit") ?></th>
          </tr>

          <? foreach ($users as $i => $user): ?>
          <? $record = ORM::factory("users_space_usage")->where("owner_id", "=", $user->id)->find(); ?>
          <tr id="g-user-<?= $user->id ?>" class="<?= text::alternate("g-odd", "g-even") ?> g-user <?= $user->admin ? "g-admin" : "" ?>">
            <td id="g-user-<?= $user->id ?>" class="g-core-info">
              <img src="<?= $user->avatar_url(20, $theme->url("images/avatar.jpg", true)) ?>"
                   alt="<?= html::clean_attribute($user->name) ?>"
                   width="20"
                   height="20" />
              <?= html::clean($user->name) ?>
            </td>
            <td>
              <?= html::clean($user->full_name) ?>
            </td>
            <td>
              <?=$record->partial_usage_string("fullsize"); ?>
            </td>
            <td>
              <?=$record->partial_usage_string("resize"); ?>
            </td>
            <td>
              <?=$record->partial_usage_string("thumb"); ?>
            </td>
            <td>
              <?=$record->total_usage_string(); ?>
            </td>
            <td>
              <?=$record->get_usage_limit_string(); ?>
            </td>
          </tr>
          <? endforeach ?>
        </table>

        <div class="g-paginator">
          <?= $theme->paginator() ?>
        </div>

      </div>
    </div>

    <div id="g-group-admin" class="g-block ui-helper-clearfix">
      <h2> <?= t("Groups") ?> </h2>
        <table id="g-group-admin-list">
          <tr>
            <th><?= t("Group") ?></th>
            <th><?= t("Limit") ?></th>
            <th><?= t("Actions") ?></th>
          </tr>
          <? foreach ($groups as $i => $group): ?>
          <? $record = ORM::factory("groups_quota")->where("group_id", "=", $group->id)->find(); ?>
          <tr id="g-group-<?= $group->id ?>" class="<?= text::alternate("g-odd", "g-even") ?> g-user ">
            <td id="g-group-<?= $group->id ?>" class="g-core-info">
              <?= html::clean($group->name) ?>
            </td>
            <td>
              <?= number_format($record->storage_limit / 1024 / 1024, 2); ?> MB
            </td>
            <td>
              <a href="<?= url::site("admin/quotas/form_group_quota/$group->id") ?>"
                  open_text="<?= t("Close") ?>"
                  class="g-panel-link g-button ui-state-default ui-corner-all ui-icon-left">
                <span class="ui-icon ui-icon-pencil"></span><span class="g-button-text"><?= t("Set limit") ?></span></a>
            </td>
          </tr>
          <? endforeach ?>
        </table>
    </div>

  </div>
  <div class="g-block-content">

    <div id="g-quota-settings-admin" class="g-block">
      <h2> <?= t("Settings") ?> </h2>
      <?= $quota_options ?>
    </div>
  </div>
</div>
