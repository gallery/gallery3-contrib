<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul>
  <? if ($item->owner): ?>
  <li>
    <strong class="caption"><?= t("Owner:") ?></strong>
    <? if ($item->owner->url): ?>
    <a href="<?= $item->owner->url ?>"><?= html::clean($item->owner->display_name()) ?></a>
    <? else: ?>
    <?= html::clean($item->owner->display_name()) ?>
    <? endif ?>
  </li>
  <? endif ?>
  <? if ($item->captured): ?>
  <li>
    <strong class="caption"><?= t("Captured:") ?></strong>
    <?= date("M j, Y H:i:s", $item->captured)?>
  </li>
  <? endif ?>
  <? if ($item->description): ?>
  <li class="g-description">
     <?= $theme->bb2html(html::purify($item->description), 1) ?>
  </li>
  <? endif ?>
</ul>
