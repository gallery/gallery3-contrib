<?php defined("SYSPATH") or die("No direct script access.") ?>

<div id="g-info">
  <?= $theme->album_top() ?>
  <h1><?= html::purify($item->title) ?></h1>
  <div class="g-description"><?= ($item->description)? bb2html(html::purify($item->description), 1) : null; ?></div>
</div>
<?= $theme->paginator() ?>
<ul id="g-album-grid">
<? if (count($children)): ?>
  <? foreach ($children as $i => $child): ?>
    <? $item_class = "g-photo"; ?>
    <? if ($child->is_album()): ?>
      <? $item_class = "g-album"; ?>
    <? endif ?>

  <li id="g-item-id-<?= $child->id ?>" class="g-item <?= $item_class ?>">
    <?= $theme->thumb_top($child) ?>
    <p class="g-thumbcrop"><a href="<?= $child->url() ?>">
      <?= $child->thumb_img() ?>
    </a></p>
    <?= $theme->thumb_bottom($child) ?>
    <h2><a href="<?= $child->url() ?>"><?= html::purify($child->title) ?></a></h2>
    <? $_text = $theme->context_menu($child, "#g-item-id-{$child->id} .g-thumbnail") ?>
    <?= (stripos($_text, '<li>'))? $_text : null; ?>
    <? if (module::is_active("info")): ?>
    <ul class="g-metadata">
    <?= $theme->thumb_info($child); ?>
    </ul>
    <? endif ?>
  </li>
  <? endforeach ?>
<? else: ?>
  <? if ($user->admin || access::can("add", $item)): ?>
  <? $addurl = url::file("index.php/simple_uploader/app/$item->id") ?>
  <li><?= t("There aren't any photos here yet! <a %attrs>Add some</a>.",
            array("attrs" => html::mark_clean("href=\"$addurl\" class=\"g-dialog-link\""))) ?></li>
  <? else: ?>
  <li><?= t("There aren't any photos here yet!") ?></li>
  <? endif; ?>
<? endif; ?>
</ul>
<?= $theme->album_bottom() ?>
<?= $theme->paginator() ?>
