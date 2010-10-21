<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul id="g-update-list">
<? foreach($update_links as $title => $url): ?>
  <li style="clear: both;">
    <a href="<?= $url ?>">
      <?= t($title) ?>
    </a>
  </li>
<? endforeach ?>
</ul>
