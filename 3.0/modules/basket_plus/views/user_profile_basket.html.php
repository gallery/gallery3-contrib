<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-user-basket-detail">
<ul>
  <li id="g-user_basket-<?= $item->id ?>">
    <a href="<?= $item->url() ?>">
      <?= html::purify($item->title) ?>
    </a>
  </li>
</ul>
</div>
