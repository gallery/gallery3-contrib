<?php defined("SYSPATH") or die("No direct script access.") ?>

<div id="g-item">
  <?= $theme->photo_top() ?>

  <div id="g-info">
    <h1><?= html::purify($item->title) ?></h1>
    <div class="g-hideitem"><?= bb2html(html::purify($item->description), 1) ?></div>
  </div>
  <?= $theme->paginator() ?>
  <? // = new View("pager_photo.html") ?>

  <div id="g-photo">
    <?= $theme->resize_top($item) ?>
    <? if (access::can("view_full", $item)): ?>
       <a href="<?= $item->file_url() ?>" rel="shadowbox;player=img" title="<?= html::purify($item->title) ?>">
    <? endif ?>
    <?= $item->resize_img(array("id" => "g-photo-id-{$item->id}", "class" => "g-resize")) ?>
    <? if (access::can("view_full", $item)): ?>
      </a>
    <? endif ?>
    <?= $theme->resize_bottom($item) ?>
  </div>
  
  <?= $theme->paginator() ?>
  <?// = new View("pager_photo.html") ?>
  <?= $theme->photo_bottom() ?>
</div>
