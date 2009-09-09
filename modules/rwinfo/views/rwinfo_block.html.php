<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul class="gMetadata">
  <? if ($item->owner): ?>
  <li>
    <strong class="caption"><?= t("Owner:") ?></strong>
    <? if ($item->owner->url): ?>
    <a href="<?= $item->owner->url ?>"><?= html::clean($item->owner->display_name()) ?></a>
    <? else: ?>
    <?= html::clean($item->owner->display_name()) ?>
    <? endif ?>
  </li>
  <? if ($item->is_album()): ?>
  <li>
    <strong class="caption"><?= t("Date:") ?></strong>
    <?= date("M j, Y", $item->created)?>
  </li>
  <? endif ?>
  <? endif ?>


  <? if (!$item->is_album()): ?>
  <li>
    <strong class="caption"><?= t("File name:") ?></strong>
    <?= html::clean($item->name) ?>
  </li>
  <? endif ?>
  <? if ($item->captured): ?>
  <li>
    <strong class="caption"><?= t("Date:") ?></strong>
    <?= date("M j, Y H:i:s", $item->captured)?>
  </li>
  <? endif ?>

  <? if (module::is_active("tag")): ?>
  <?
    $tagsItem = ORM::factory("tag")
      ->join("items_tags", "tags.id", "items_tags.tag_id")
      ->where("items_tags.item_id", $item->id)
      ->find_all();
  ?>
    <? if (count($tagsItem) > 0): ?>
      <li>
      <strong class="caption"><?= t("Tags:") ?></strong>
      <? for ($counter=0; $counter<count($tagsItem); $counter++) { ?>
        <? if ($counter < count($tagsItem)-1) { ?>
          <a href="<?= url::site("tags/$tagsItem[$counter]") ?>"><?= html::clean($tagsItem[$counter]->name) ?></a>,
        <? } else {?>
          <a href="<?= url::site("tags/$tagsItem[$counter]") ?>"><?= html::clean($tagsItem[$counter]->name) ?></a>
        <? } ?>
      <? } ?>
      </li>
    <? endif ?>
  <? endif ?>

</ul>
