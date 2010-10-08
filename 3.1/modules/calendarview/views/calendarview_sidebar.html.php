<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul>
  <? if ($day_count > 0): ?>
  <li><a href="<?=url::site("calendarview/day/" . date("Y", $date) . "/-1/" . date("n", $date) . "/" . date("j", $date)); ?>"><?=t("More from"); ?> <?=date("F", $date); ?> <?=date("j", $date); ?><?=date("S", $date); ?></a></li>
  <? endif ?>
  <? if ($month_count > 0): ?>
  <li><a href="<?=url::site("calendarview/month/" . date("Y", $date) . "/-1/" . date("n", $date)); ?>"><?=t("More from"); ?> <?=date("F", $date); ?></a></li>
  <? endif ?>
</ul>