<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-user-home-detail">
<ul>
  <li id="g-user_home-<?= $item->id ?>">
    <a href="<?= $item->url() ?>">
      <?= html::purify($item->title) ?>
    </a>
  </li>
</ul>
</div>
