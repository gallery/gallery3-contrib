<?php defined("SYSPATH") or die("No direct script access.") ?>
<? $embedded_video = ORM::factory('embedded_video')->where('item_id', $item->id)->find();
   if ($embedded_video->loaded()) {
?>
<div id="g-item">
  <?= $theme->photo_top() ?>

  <?= $theme->paginator() ?>

  <div id="g-movie" class="ui-helper-clearfix">
    <?= $theme->resize_top($item) ?>
    <?= $embedded_video->embed_code ?>
    <?= $theme->resize_bottom($item) ?>
  </div>

  <div id="g-info">
    <h1><?= html::purify($item->title) ?></h1>
    <div><?= nl2br(html::purify($item->description)) ?></div>
  </div>

  <?= $theme->photo_bottom() ?>
</div>
<? } else {
  require('themes/' . $theme->name . 'views/photo.html.php');
   } ?>