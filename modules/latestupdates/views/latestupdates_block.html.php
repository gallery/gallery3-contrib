<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul id="gUpdates">
<? foreach($updateLinks as $title => $url): ?>
  <li style="clear: both;">
    <a href="<?= $url ?>">
      <?= $title ?>
    </a>
  </li>
<? endforeach ?>
</ul>
