<?php defined("SYSPATH") or die("No direct script access.") ?>
<? // @todo Set hover on AlbumGrid list items for guest users ?>
<div class="galleryinfo">
  <h1><?= html::purify($item->title) ?></h1>
  <p class="desc"><?= nl2br(html::purify($item->description)) ?></p>
</div>

<ul class="slideset">
<? if (count($children)): ?>
  <!-- Albums first -->
  <? foreach ($children as $i => $child): ?>
    <? if( $child->is_album() ): ?>
    <li class="thumb"><em style="background-image: url('<?= $child->thumb_url() ?>');"><a href="<?= $child->url() ?>"><span><?= html::purify($child->title) ?></span></a></em></li>
    <? endif; ?>
  <? endforeach ?>

  <? foreach ($children as $i => $child): ?>
    <? if( !$child->is_album() ): ?>
    <li class="thumb"><em style="background-image: url('<?= $child->thumb_url() ?>');"><a href="<?= $child->url() ?>"><span><?= html::purify($child->title) ?></span></a></em></li>
    <? endif; ?>
  <? endforeach ?>
<? endif; ?>
</ul>

<div class="galleryinfo">
  <p>
    <em class="count"><?= /* @todo This message isn't easily localizable */
            t2("Photo %from_number of %count",
               "Photos %from_number - %to_number of %count",
               $children_count,
               array("from_number" => ($page - 1) * $page_size + 1,
                     "to_number" => min($page * $page_size, $children_count),
                     "count" => $children_count)) ?>
    </em>
    <? if ($page != 1): ?>
      <a href="<?= url::site(url::merge(array("page" => $page - 1))) ?>" accesskey="z">&laquo; <?= t("Previous") ?></a>
    <? endif; ?>
      &nbsp;
    <? if ($page != $max_pages): ?>
      <a id="next-page" href="<?= url::site(url::merge(array("page" => $page + 1))) ?>" accesskey="x"><?= t("Next") ?> &raquo;</a>
    <? endif; ?>
  </p>

  <? if( access::can("add", $item) || access::can("edit", $item) ): ?>
  <p><em class="count">Actions</em></p>
  <ul>
    <? if( access::can("add", $item) ): ?>
    <li><a class="g-dialog-link" href="<?= url::site("simple_uploader/app/$item->id") ?>"><?= t("Add photos") ?></a></li>
    <li><a class="g-dialog-link" href="<?= url::site("form/add/albums/$item->id?type=album") ?>"><?= t("Add an album") ?></a></li>
    <? endif; ?>
    <? if( access::can("edit", $item) ): ?>
    <li><a class="g-dialog-link" href="<?= url::site("form/edit/{$item->type}s/$item->id") ?>"><?= t("Edit album") ?></a></li>
    <li><a class="g-dialog-link" href="<?= url::site("move/browse/$item->id") ?>"><?= t("Move to another album") ?></a></li>
    <li><a class="g-dialog-link" href="<?= url::site("quick/form_delete/$item->id?csrf=$csrf&from_id=$theme_item->id") ?>"><?= t("Delete this album") ?></a></li>
    <? endif; ?>
    <? if( identity::active_user()->admin ): ?>
    <li><a class="g-dialog-link" href="<?= url::site("permissions/browse/$item->id") ?>"><?= t("Edit permissions") ?></a></li>
    <? endif; ?>
  </ul>
  <? endif; ?>
</div>