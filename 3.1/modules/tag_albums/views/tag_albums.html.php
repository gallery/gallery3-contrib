<?php defined("SYSPATH") or die("No direct script access.") ?>
      <div id="g-header" class="ui-helper-clearfix">
        <? // The following code was modifed to allow module-defined breadcrumbs.
           // Everything else in this file is a copy of the default page.html.php file.
        ?>
        <? if (!empty($breadcrumbs)): ?>
        <ul class="g-breadcrumbs">
          <? $i = 0 ?>
          <? foreach ($breadcrumbs as $breadcrumb): ?>
          <li<? if ($i == 0) print " class=\"g-first\"" ?>>
            <!-- Adding ?show=<id> causes Gallery3 to display the page
                 containing that photo.  For now, we just do it for
                 the immediate parent so that when you go back up a
                 level you're on the right page. -->
            <? if ($breadcrumb->url) : ?>
              <a href="<?= $breadcrumb->url ?>"><?= html::purify($breadcrumb->title) ?></a>
            <? else : ?>
              <?= html::purify($breadcrumb->title) ?>
            <? endif ?>
          </li>
          <? $i++ ?>
          <? endforeach ?>
        </ul>
        <? endif ?>
        <? // End modified code ?>
</div>

<div id="g-info">
    <?= $theme->dynamic_top() ?>
  <h1><?= html::purify($title) ?></h1>
  <div class="g-description"><?= nl2br(html::purify($description)) ?></div>
</div>



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
