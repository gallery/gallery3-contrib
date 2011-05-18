<?php defined("SYSPATH") or die("No direct script access.") ?>
<? 
  // The g-info block was taken from album.html.php and $theme->album_top() was changed to $theme->dynamic_top().
  // $item->title and $item->description have been changed to $title and $description.
  //
  // The g-album-grid block was also taken from album.html.php.  The section for uploading new photos to an empty album
  // has been removed.  Also, $theme->context_menu has been removed as well (it was crashing the page).
?>
<div id="g-info">
  <?= $theme->dynamic_top() ?>
  <h1><?= html::purify($title) ?></h1>
  <div class="g-description"><?= nl2br(html::purify($description)) ?></div>
</div>

<? if (isset($filter_text) && (module::get_var("tag_albums", "tag_index_filter"))): ?>
<div id="g-tags-filter">
<br/ >
  <center><?= $filter_text; ?></center>
</div>
<? endif ?>

<ul id="g-album-grid" class="ui-helper-clearfix">
<? if (count($children)): ?>
  <? foreach ($children as $i => $child): ?>
    <? $item_class = "g-photo"; ?>
    <? if ($child->is_album()): ?>
      <? $item_class = "g-album"; ?>
    <? endif ?>
  <li id="g-item-id-<?= $child->id ?>" class="g-item <?= $item_class ?>">
    <?= $theme->thumb_top($child) ?>
    <a href="<?= $child->url() ?>">
      <? if ($child->has_thumb()): ?>
      <?= $child->thumb_img(array("class" => "g-thumbnail")) ?>
      <? endif ?>
    </a>
    <?= $theme->thumb_bottom($child) ?>
    <h2><span class="<?= $item_class ?>"></span>
      <a href="<?= $child->url() ?>"><?= html::purify($child->title) ?></a></h2>
    <ul class="g-metadata">
      <?= $theme->thumb_info($child) ?>
    </ul>
  </li>
  <? endforeach ?>
<? else: ?>
  <li><?= t("There aren't any photos here yet!") ?></li>
<? endif; ?>
</ul>
<?= $theme->dynamic_bottom() ?>

<?= $theme->paginator() ?>
