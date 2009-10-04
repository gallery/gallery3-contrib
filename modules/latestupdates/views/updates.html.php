<?php defined("SYSPATH") or die("No direct script access.") ?>
<? // @todo Set hover on AlbumGrid list items ?>

<div id="g-latest-updates">
  <h1><?= t("Latest Updates") ?></h1>
<? array("term" => html::clean($q)) ?>
 <? if (count($items)): ?>
  <ul id="g-album-grid">
    <? foreach ($items as $item): ?>
      <? $item_class = "g-photo"; ?>
      <? if ($item->is_album()): ?>
        <? $item_class = "g-album"; ?>
      <? endif ?>
   <li class="g-item <?= $item_class ?>">
      <a href="<?= url::site("items/$item->id") ?>">
        <?= $item->thumb_img() ?>
        <p>
          <?= html::clean($item->title) ?>
        </p>
        <div>
          <?= html::clean($item->description) ?>
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
