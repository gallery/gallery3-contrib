<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul>
  <li><a href="<?=url::site("calendarview/day/" . date("Y", $date) . "/-1/" . date("n", $date) . "/" . date("j", $date)); ?>"><?=t("More from"); ?> <?=date("F", $date); ?> <?=date("j", $date); ?><?=date("S", $date); ?></a></li>
  <li><a href="<?=url::site("calendarview/month/" . date("Y", $date) . "/-1/" . date("n", $date)); ?>"><?=t("More from"); ?> <?=date("F", $date); ?></a></li>
</ul>