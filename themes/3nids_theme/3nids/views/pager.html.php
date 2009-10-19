<?php defined("SYSPATH") or die("No direct script access.") ?>
<? // See http://docs.kohanaphp.com/libraries/pagination ?>
<ul class="g-pager ui-helper-clearfix">
  <? /* @todo This message isn't easily localizable */
     $from_to_msg = t2("%current_page / %total_pages",
                       "%current_page / %total_pages",
                       $total_items,
                       array("total_pages" => $total_pages,
                             "current_page" => $current_page)) ?>
  <li>
  <? if ($first_page): ?>
    <a href="<?= str_replace('{page}', 1, $url) ?>" class="g-button ui-icon-left ui-state-default ui-corner-all">
      <span class="ui-icon ui-icon-seek-first"></span><?= t("first") ?></a>
  <? else: ?>
    <a class="g-button ui-icon-left ui-state-disabled ui-corner-all">
      <span class="ui-icon ui-icon-seek-first"></span><?= t("first") ?></a>
  <? endif ?>
  <? if ($previous_page): ?>
    <a href="<?= str_replace('{page}', $previous_page, $url) ?>" class="g-button ui-icon-left ui-state-default ui-corner-all">
      <span class="ui-icon ui-icon-seek-prev"></span><?= t("previous") ?></a>
  <? else: ?>
    <a class="g-button ui-icon-left ui-state-disabled ui-corner-all">
      <span class="ui-icon ui-icon-seek-prev"></span><?= t("previous") ?></a>
  <? endif ?>
  </li>
  <li class="g-info"><?= $from_to_msg ?></li>
  <li class="g-txt-right">
  <? if ($next_page): ?>
    <a href="<?= str_replace('{page}', $next_page, $url) ?>" class="g-button ui-icon-right ui-state-default ui-corner-all">
      <span class="ui-icon ui-icon-seek-next"></span><?= t("next") ?></a>
  <? else: ?>
    <a class="g-button ui-state-disabled ui-icon-right ui-corner-all">
      <span class="ui-icon ui-icon-seek-next"></span><?= t("next") ?></a>
  <? endif ?>
  <? if ($last_page): ?>
    <a href="<?= str_replace('{page}', $last_page, $url) ?>" class="g-button ui-icon-right ui-state-default ui-corner-all">
      <span class="ui-icon ui-icon-seek-end"></span><?= t("last") ?></a>
  <? else: ?>
    <a class="g-button ui-state-disabled ui-icon-right ui-corner-all">
      <span class="ui-icon ui-icon-seek-end"></span><?= t("last") ?></a>
  <? endif ?>
  </li>
</ul>
