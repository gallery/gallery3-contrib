<?php defined("SYSPATH") or die("No direct script access.") ?>
<? // @todo Set hover on AlbumGrid list items ?>

<div id="gLatestUpdates">
  <h1><?= t("Latest Updates") ?></h1>
<? array("term" => p::clean($q)) ?>
 <? if (count($items)): ?>
  <ul id="gAlbumGrid">
    <? foreach ($items as $item): ?>
      <? $item_class = "gPhoto"; ?>
      <? if ($item->is_album()): ?>
        <? $item_class = "gAlbum"; ?>
      <? endif ?>
   <li class="gItem <?= $item_class ?>">
      <a href="<?= url::site("items/$item->id") ?>">
        <?= $item->thumb_img() ?>
        <p>
          <?= p::clean($item->title) ?>
        </p>
        <div>
          <?= p::clean($item->description) ?>
        </div>
      </a>
    </li>
    <? endforeach ?>
  </ul>
  <?= $theme->pager() ?>

  <? else: ?>
  <p><?= t("There are no items to display.") ?></p>

  <? endif; ?>
</div>
