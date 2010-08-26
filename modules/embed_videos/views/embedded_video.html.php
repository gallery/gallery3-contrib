<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-item">
  <?= $theme->photo_top() ?>

  <?= $theme->paginator() ?>

  <div id="g-movie" class="ui-helper-clearfix">
    <?= $theme->resize_top($item) ?>
    <?= $embedded_video ?>
    <?= $theme->resize_bottom($item) ?>
  </div>

  <div id="g-info">
    <h1><?= html::purify($item->title) ?></h1>
    <div><?= nl2br(html::purify($item->description)) ?></div>
  </div>

  <?= $theme->photo_bottom() ?>
</div>
