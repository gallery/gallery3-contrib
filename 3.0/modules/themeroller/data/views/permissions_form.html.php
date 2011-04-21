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
      <td> <?= t($permission->display_name) ?></td>
      <? foreach ($groups as $group): ?>
        <? $intent = access::group_intent($group, $permission->name, $item) ?>
        <? $allowed = access::group_can($group, $permission->name, $item) ?>
        <? $lock = access::locked_by($group, $permission->name, $item) ?>

        <? if ($lock): ?>
          <td class="g-denied">
            <a href="javascript:return false;"
               title="<?= t('denied and locked through parent album')->for_html_attr() ?>"
               class="ui-icon ui-icon-cancel g-passive" />
            <a href="javascript:show(<?= $lock->id ?>)"
               title="<?= t('denied and locked through parent album, click to go to parent album')->for_html_attr() ?>"
               class="ui-icon ui-icon-locked" />
          </td>
        <? else: ?>
          <? if ($intent === access::INHERIT): ?>
            <? if ($allowed): ?>
              <td class="g-allowed">
                <a href="javascript:set('allow',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                   title="<?= t('allowed through parent album, click to allow explicitly')->for_html_attr() ?>"
                   class="ui-icon ui-icon-check" />
                <a href="javascript:set('deny',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                   title="<?= t('click to deny')->for_html_attr() ?>"
                   class="ui-disabled ui-icon ui-icon-cancel" />
              </td>
            <? else: ?>
              <td class="g-denied">
                <a href="javascript:set('allow',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                  title="<?= t('click to allow')->for_html_attr() ?>"
                   class="ui-icon ui-icon-check" />
                <a href="javascript:set('deny',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                   title="<?= t('denied through parent album, click to deny explicitly')->for_html_attr() ?>"
                   class="ui-disabled ui-icon ui-icon-cancel" />
             </td>
            <? endif ?>

          <? elseif ($intent === access::DENY): ?>
            <td class="g-denied">
              <a href="javascript:set('allow',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                title="<?= t('click to allow')->for_html_attr() ?>"
                   class="ui-icon ui-icon-check" />
              <? if ($item->id == 1): ?>
              <a href="javascript:return false;"
                 title="<?= t('denied')->for_html_attr() ?>"
                 class="ui-icon ui-icon-cancel g-passive" />
               <? else: ?>
                <a href="javascript:set('reset',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                   title="<?= t('denied, click to reset')->for_html_attr() ?>"
                   class="ui-icon ui-icon-cancel" />
              <? endif ?>
            </td>
          <? elseif ($intent === access::ALLOW): ?>
            <td class="g-allowed">
              <? if ($item->id == 1): ?>
              <a href="javascript:return false;"
                 title="<?= t('allowed')->for_html_attr() ?>"
                 class="ui-icon ui-icon-check g-passive" />
              <? else: ?>
              <a href="javascript:set('reset',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                 title="<?= t('allowed, click to reset')->for_html_attr() ?>"
                 class="ui-icon ui-icon-check" />
              <? endif ?>
              <a href="javascript:set('deny',<?= $group->id ?>,<?= $permission->id ?>,<?= $item->id ?>)"
                 title="<?= t('click to deny')->for_html_attr() ?>"
                 class="ui-icon ui-icon-cancel" />
            </td>
          <? endif ?>
        <? endif ?>
      </td>
      <? endforeach ?>
    </tr>
    <? endforeach ?>
  </table>
</fieldset>
