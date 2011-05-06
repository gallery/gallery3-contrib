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
<div id="g-item">
  <?= $theme->photo_top() ?>

  <?= $theme->paginator() ?>

  <div id="g-movie" class="ui-helper-clearfix">
    <?= $theme->resize_top($item) ?>
    <?= $item->movie_img(array("class" => "g-movie", "id" => "g-item-id-{$item->id}")) ?>
    <?= $theme->resize_bottom($item) ?>
  </div>

  <div id="g-info">
    <h1><?= html::purify($item->title) ?></h1>
    <div><?= nl2br(html::purify($item->description)) ?></div>
  </div>

  <?= $theme->photo_bottom() ?>
</div>
