<?php defined("SYSPATH") or die("No direct script access.") ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>
      <? if ($page_title): ?>
        <?= $page_title ?>
      <? else: ?>
        <? if ($theme->item()): ?>
          <? if ($theme->item()->is_album()): ?>
          <?= t("Browse Album :: %album_title", array("album_title" => $theme->item()->title)) ?>
          <? elseif ($theme->item()->is_photo()): ?>
          <?= t("Photo :: %photo_title", array("photo_title" => $theme->item()->title)) ?>
          <? else: ?>
          <?= t("Movie :: %movie_title", array("movie_title" => $theme->item()->title)) ?>
          <? endif ?>
        <? elseif ($theme->tag()): ?>
          <?= t("Browse Tag :: %tag_title", array("tag_title" => $theme->tag()->name)) ?>
        <? else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
          <?= t("Gallery") ?>
        <? endif ?>
      <? endif ?>
    </title>
    <link rel="shortcut icon" href="<?= url::file("lib/images/favicon.ico") ?>" type="image/x-icon" />

    <?= $theme->css("_DISABLED_yui/reset-fonts-grids.css") ?>
    <?= $theme->css("_DISABLED_superfish/css/superfish.css") ?>
    <?= $theme->css("_DISABLED_themeroller/ui.base.css") ?>
    <?= $theme->css("_DISABLED_gallery.common.css") ?>
    <?= $theme->script("jquery.js") ?>
    <?= $theme->script("jquery.form.js") ?>
    <?= $theme->script("jquery-ui.js") ?>
    <?= $theme->script("gallery.common.js") ?>
    <? /* MSG_CANCEL is required by gallery.dialog.js */ ?>
    <script type="text/javascript">
    var MSG_CANCEL = <?= t('Cancel')->for_js() ?>;
    </script>
    <?= $theme->script("gallery.ajax.js") ?>
    <?= $theme->script("gallery.dialog.js") ?>
    <?= $theme->script("superfish/js/superfish.js") ?>
    <?= $theme->script("jquery.localscroll.js") ?>
    <?= $theme->script("ui.init.js") ?>

    <? /* These are page specific, but if we put them before $theme->head() they get combined */ ?>
    <? if ($theme->page_subtype == "photo"): ?>
    <?= $theme->script("jquery.scrollTo.js") ?>
    <?= $theme->script("gallery.show_full_size.js") ?>
    <? elseif ($theme->page_subtype == "movie"): ?>
    <?= $theme->script("flowplayer.js") ?>
    <? endif ?>

    <?= $theme->css("photos.css") ?>
    <?= $theme->css("aus04.css") ?>
    <?= $theme->css("custom.css") ?>
    <?= $theme->css("custom.gallery3.css") ?>
    <?= $theme->css("custom.gallery3-dialog.css") ?>
    <?= $theme->script("_DISABLED_rememberMe.js") ?>
    <?= $theme->script("_DISABLED_comments.js") ?>
    <?= $theme->script("stopdesign.ui.init.js") ?>

    <?= $theme->head() ?>
  </head>

  <body>
    <div id="content">
      <?= $content ?>
    </div>

    <? if ($theme->item() && !empty($parents)): ?>
      <p id="path">
      <? $i = 0 ?>
      <? foreach ($parents as $parent): ?>
        <a href="<?= $parent->url($parent == $theme->item()->parent() ? "show={$theme->item()->id}" : null) ?>">
          <?= html::purify($parent->title) ?>
        </a> »
        <? $i++ ?>
      <? endforeach ?>
      <?= html::purify($theme->item()->title) ?>
      </p>
    <? endif ?>

    <div id="footer">
      <hr />
      <p></p>
      <ul id="credits">
        <li><a href="http://stopdesign.com/templates/photos/">Photo Templates</a> from Stopdesign.</li>
        <?= $theme->credits() ?>.
        <? if( identity::active_user()->admin ): ?>
        <li><a href="<?= url::site("admin") ?>"><?= t("Admin") ?></a></li>
        <? endif; ?>
      </ul>
    </div>
  </body>
</html>
