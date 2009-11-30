<?php defined("SYSPATH") or die("No direct script access.") ?>

<span class="g-metadata">
  <? if ($item->description): ?>
     <?= bb2html(html::purify($item->description), 1) ?>
  <? else: ?>
     &nbsp;
  <? endif ?>
</span>
