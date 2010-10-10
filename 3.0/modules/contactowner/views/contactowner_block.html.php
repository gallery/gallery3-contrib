<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul id="g-contact-owner">
  <? if (!empty($ownerLink)): ?>
  <li style="clear: both">
    <?= $ownerLink ?>
  </li>
  <? endif  ?>

  <? if (!empty($userLink)): ?>
  <li style="clear: both">
    <?= ($userLink); ?>
  </li>
  <? endif ?>
</ul>

