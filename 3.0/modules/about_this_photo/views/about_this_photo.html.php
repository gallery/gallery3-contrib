<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul class="g-metadata">
  <li>
    <strong class="caption"><?= t("Date:") ?></strong>
    <?= $date ?>
  </li>
  <li>
    <strong class="caption"><?= t("Time:") ?></strong>
    <?= $time ?>
  </li>
  <li>
    <strong class=="caption"><?= t("Tags:") ?></strong>
    <? foreach ($tags as $tag): ?>
    <a href="<?= $tag->url() ?>"><?= html::clean($tag->name) ?></a>
    <? endforeach?>
  </li>
</ul>
