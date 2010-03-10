<?php defined("SYSPATH") or die("No direct script access.") ?>
<? date_default_timezone_set('America/New_York'); ?>
<ul class="g-metadata">
  <? if ($item->owner): ?>
  <li>
    <strong class="caption"><?= t("Owner:") ?></strong>
    <? if ($item->owner->url): ?>
    <a href="<?= $item->owner->url ?>"><?= html::clean($item->owner->display_name()) ?></a>
    <? else: ?>
    <?= html::clean($item->owner->display_name()) ?>
    <? endif ?>
  </li>
  <? endif ?>

  <? if ($item->is_album()): ?>
  <li>
    <strong class="caption"><?= t("Date:") ?></strong>
    <?= date("F j, Y", $item->created)?>
  </li>
  <? endif ?>
  <? if ($item->captured): ?>
  <li>
    <strong class="caption"><?= t("Date:") ?></strong>
    <?= date("F j, Y h:i:s a T", $item->captured)?>
  </li>
  <? endif ?>

  <? if (!$item->is_album()): ?>
  <li>
    <strong class="caption"><?= t("File name:") ?></strong>
    <?= html::clean($item->name) ?>
  </li>
  <? endif ?>

  <? if (module::is_active("tag")): ?>
  <? $tags = ORM::factory("tag")
     ->join("items_tags", "tags.id", "items_tags.tag_id")
     ->where("items_tags.item_id", "=", $item->id)
     ->find_all();
  ?>
  <? if (count($tags)): ?>
  <li>
    <strong class="caption"><?= t("Tags:") ?></strong>
    <? $not_first = 0; ?>
    <? foreach ($tags as $tag): ?>
    <?= ($not_first++) ? "," : "" ?>
    <a href="<?= $tag->url() ?>"><?= html::clean($tag->name) ?></a>
    <? endforeach ?>
  </li>
  <? endif ?>
  <? endif ?>
</ul>
