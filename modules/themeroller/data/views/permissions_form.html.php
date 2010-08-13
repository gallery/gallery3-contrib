<?php defined("SYSPATH") or die("No direct script access.") ?>
<fieldset>
  <legend> <?= t('Edit Permissions') ?> </legend>
  <table>
    <tr>
      <th> </th>
      <? foreach ($groups as $group): ?>
      <th> <?= html::clean($group->name) ?> </th>
      <? endforeach ?>
    </tr>

    <? foreach ($permissions as $permission): ?>
    <tr>
  <td> <?= t($permission->display_name) ?>
 </td>
      <? foreach ($groups as $group): ?>
        <? $intent = access::group_intent($group, $permission->name, $item) ?>
        <? $allowed = access::group_can($group, $permission->name, $item) ?>
        <? $lock = access::locked_by($group, $permission->name, $item) ?>

        <? if ($lock): ?>
          <td class="g-denied">
            <img src="<?= url::file(gallery::find_file("images", "ico-denied.png")) ?>"
                 title="<?= t('denied and locked through parent album')->for_html_attr() ?>"
                 alt="<?= t('denied icon')->for_html_attr() ?>" />
            <a href="javascript:show(<?= $lock->id ?>)" title="<?= t('click to go to parent album')->for_html_attr() ?>">
              <img src="<?= url::file(gallery::find_file("images", "ico-lock.png")) ?>" alt="<?= t('locked icon')->for_html_attr() ?>" />
            </a>
          </td>
        <? else: ?>
          <? if ($intent === access::INHERIT): ?>
            <? if ($allowed): ?>
              <td class="g-resource-access">
                <a href="javascript:set('allow',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)" title="<?= t('allowed through parent album, click to deny')->for_html_attr() ?>" class="ui-icon ui-icon-check">
                </a>
              </td>
            <? else: ?>
              <td class="g-resource-denied">
                <a href="javascript:set('deny',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                  title="<?= t('denied through parent album')->for_html_attr() ?>"
                 class="ui-state-disabled ui-icon ui-icon-cancel"
                 onclick="javascript:return false;">
                </a>
              </td>
            <? endif ?>

          <? elseif ($intent === access::DENY): ?>
            <td class="g-resource-denied">
              <a href="javascript:set('allow',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                title="<?= t('denied, click to allow')->for_html_attr() ?>"
                 class="ui-icon ui-icon-cancel">
              </a>
            </td>
          <? elseif ($intent === access::ALLOW): ?>
            <td class="g-resource-access">
                <a href="javascript:set('deny',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                   class="ui-icon ui-icon-check"
                  title="<?= t('allowed, click to deny')->for_html_attr() ?>">
                   &nbsp;
                </a>
                     <span>
            </td>
          <? endif ?>
        <? endif ?>
      </td>
      <? endforeach ?>
    </tr>
    <? endforeach ?>
  </table>
</fieldset>
