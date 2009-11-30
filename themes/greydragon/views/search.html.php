<?php defined("SYSPATH") or die("No direct script access.") ?>

<div id="g-search-results">
  <h1><?= t("Search Results for \"%term\"", array("term" => $q)) ?> </h1>

  <? if (count($items)): ?>
  <?= $theme->pager() ?>
  <ul id="g-album-grid">
    <? foreach ($items as $item): ?>
      <? $item_class = "g-photo"; ?>
      <? if ($item->is_album()): ?>
        <? $item_class = "g-album"; ?>
      <? endif ?>
    <li class="g-item <?= $item_class ?>">
      <p class="g-thumbcrop"><a href="<?= $item->url() ?>">
        <?= $item->thumb_img() ?>
      </a></p>
      <h2><a href="<?= $item->url() ?>"><?= html::purify($item->title) ?></a></h2>
    </li>
    <? endforeach ?>
  </ul>
  <?= $theme->pager() ?>

  <? else: ?>
  <p>&nbsp;</p>
  <p><?= t("No results found for <b>%term</b>", array("term" => $q)) ?></p>

  <? endif; ?>
</div>

