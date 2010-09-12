<?php defined("SYSPATH") or die("No direct script access.") ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<? $theme->load_sessioninfo(); ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<!-- Grey Dragon Theme - Copyright (c) 2009-2010 Serguei Dosyukov - All Rights Reserved -->
<?
  if (($theme->enable_pagecache) and ($theme->item())):
    // Page will expire in 60 seconds
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 60).'GMT');  
    header("Cache-Control: public");
    header("Cache-Control: post-check=3600, pre-check=43200", false);
    header("Content-Type: text/html; charset=UTF-8");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  endif;
?>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?= "  <title>"; ?>
<? if ($page_title): ?>
<?=  $page_title ?>
<? else: ?>
<?   if ($theme->item()): ?>
<?     if ($theme->item()->is_album()): ?>
<?=      t("Browse Album :: %album_title", array("album_title" => $theme->bb2html($theme->item()->title, 2))) ?>
<?     elseif ($theme->item()->is_photo()): ?>
<?=      t("Photo :: %photo_title", array("photo_title" => $theme->bb2html($theme->item()->title, 2))) ?>
<?     else: ?>
<?=      t("Movie :: %movie_title", array("movie_title" => $theme->bb2html($theme->item()->title, 2))) ?>
<?     endif ?>
<?   elseif ($theme->tag()): ?>
<?=    t("Browse Tag :: %tag_title", array("tag_title" => $theme->bb2html($theme->tag()->name, 2))) ?>
<?   else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
<?=    t("Gallery") ?>
<?   endif ?>
<? endif ?></title>
<? if (!$theme->disable_seosupport): ?>
  <?= '<meta name="robots" content="noindex, nofollow, noarchive" />' . "\n"; ?>
  <?= '<meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet, noodp, noimageindex, notranslate" />' . "\n"; ?>
  <?= '<meta name="slurp" content="noindex, nofollow, noarchive, nosnippet, noodp, noydir" />' . "\n"; ?>
  <?= '<meta name="msnbot" content="noindex, nofollow, noarchive, nosnippet, noodp" />' . "\n"; ?>
  <?= '<meta name="teoma" content="noindex, nofollow, noarchive" />' . "\n"; ?>
<? endif; ?>
<link rel="shortcut icon" href="<?= $theme->url("images/favicon.ico") ?>" type="image/x-icon" />
<?= $theme->script("jquery.js") ?>
<?= $theme->script("jquery.form.js") ?>
<?= $theme->script("jquery-ui.js") ?>
<? if ($theme->page_subtype == "movie"): ?>
<?= $theme->script("flowplayer.js") ?>
<? endif ?>
<?= $theme->script("gallery.ajax.js") ?>
<?= $theme->head() ?>
<link rel="stylesheet" href="<?= $theme->url("css/screen.css") ?>" type="text/css" media="screen,print,projection" />
<link rel="stylesheet" href="<?= $theme->url("css/colorpacks/" . $theme->color_pack . "/colors.css") ?>" type="text/css" media="screen,print,projection" />

<!--[if lte IE 7]>
  <link rel="stylesheet" href="<?= $theme->url("css/old_ie.css") ?>" type="text/css" media="screen,print,projection" />
<![endif]-->
<!--[if !IE]>
  <link rel="stylesheet" href="<?= $theme->url("css/layout_non_ie.css") ?>" type="text/css" media="screen,print,projection" />
<![endif]-->

<script type="text/javascript" src="<?= $theme->url("js/ui.support.js") ?>"></script>
</head>
<body>
<?= $theme->page_top() ?>
<div id="g-header">
  <?= $theme->header_top() ?>
<? if ($header_text = module::get_var("gallery", "header_text")): ?>
  <?= $header_text ?>
<? else: ?>
  <a id="g-logo" href="<?= item::root()->url() ?>" title="<?= t("go back to the Gallery home")->for_html_attr() ?>">
    <img alt="<?= t("Gallery logo: Your photos on your web site")->for_html_attr() ?>" src="<?= $theme->logopath ?>" />
  </a>
<? endif ?>

<? if ((!$user->guest) or ($theme->show_guest_menu)): ?>
  <div id="g-site-menu" class="<?= ($theme->mainmenu_position == "top")? "top" : "default"; ?>">
  <?= $theme->site_menu() ?>
  </div>
<? endif ?>
  <?= $theme->messages() ?>
<?= $theme->header_bottom() ?>

<? if ($theme->loginmenu_position == "header"): ?>
  <?= $theme->user_menu() ?>
<? endif ?>

<? if ($theme->show_breadcrumbs): ?>
  <?= $theme->breadcrumb_menu($theme, $parents); ?>
<? endif; ?>
</div>
<div id="g-main">
  <div id="g-main-in">
    <?= $theme->sidebar_menu($url) ?>
    <div id="g-view-menu" class="g-buttonset<?= ($theme->sidebarallowed!="any")? " g-buttonset-shift" : null; ?>">
    <? if ($page_subtype == "album"):?>
      <?= $theme->album_menu() ?>
    <? elseif ($page_subtype == "photo") : ?>
      <?= $theme->photo_menu() ?>
    <? elseif ($page_subtype == "movie") : ?>
      <?= $theme->movie_menu() ?>
    <? elseif ($page_subtype == "tag") : ?>
      <?= $theme->tag_menu() ?>
    <? endif ?>
    </div>

  <? if ($theme->sidebarvisible=="left"): ?>
    <?= '<div id="g-column-left">' ?>
  <? elseif ($theme->sidebarvisible=="none"): ?>
  <? else: ?>
    <?= '<div id="g-column-right">' ?>
  <? endif ?>

  <? if (($theme->page_subtype != "login") and ($theme->page_subtype != "reauthenticate") and ($theme->sidebarvisible != "none")): ?>
  <?= new View("sidebar.html") ?>
  <? endif ?>
  <?= ($theme->sidebarvisible != "none")? "</div>" : null ?>

    <? if ($theme->sidebarvisible == "left"): ?>
      <?= '<div id="g-column-centerright">' ?>
    <? elseif ($theme->sidebarvisible == "none"): ?>
      <?= '<div id="g-column-centerfull">' ?>
    <? else: ?>
      <?= '<div id="g-column-centerleft">' ?>
    <? endif ?>
      <?= $content ?>
    </div> 
  </div>
</div>
<div id="g-footer">
<?= $theme->footer() ?>
<? if ($footer_text = module::get_var("gallery", "footer_text")): ?>
<?=  $footer_text ?>
<? endif ?>

<? if (module::get_var("gallery", "show_credits")): ?>
  <ul id="g-credits"><?= $theme->credits() ?></ul>
<? endif ?>
  <ul id="g-footer-rightside"><li><?= $theme->copyright ?></li></ul>
<? if ($theme->loginmenu_position == "default"): ?>
  <?= $theme->user_menu() ?>
<? endif ?>
</div>
<?= $theme->page_bottom() ?>
</body>
</html>
