<?php defined("SYSPATH") or die("No direct script access.") ?>
<? include('support/bbtohtml.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- Copyright (c) 2009 DragonSoft. All Rights Reserved -->

<?
   if (empty($_REQUEST['sb'])) {
     if (isset($_COOKIE['gd_sidebar'])) {
       $sidebarvisible = $_COOKIE['gd_sidebar'];
     } else {
       $sidebarvisible = module::get_var("th_greydragon", "sidebar_visible", "");
     }
   } else {
     // Sidebar position is kept for 360 days
     $sidebarvisible = $_REQUEST['sb'];
     setcookie("gd_sidebar", $sidebarvisible, time() + 31536000);
   }

   $sidebarallowed = module::get_var("th_greydragon", "sidebar_allowed", "");

   if ($sidebarallowed == "") { $sidebarallowed = "any"; };
   if ($sidebarvisible == "") { $sidebarvisible = "right"; };
   if ($sidebarallowed == "none") { $sidebarvisible = "none"; }
   if ($sidebarallowed == "right") { $sidebarvisible = "right"; }
   if ($sidebarallowed == "left") { $sidebarvisible = "left"; }
?>

<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>
    <? if ($page_title): ?>
<?=  $page_title ?>
<? else: ?>
<?   if ($theme->item()): ?>
<?     if ($theme->item()->is_album()): ?>
<?=      t("Browse Album :: %album_title", array("album_title" => $theme->item()->title)) ?>
<?     elseif ($theme->item()->is_photo()): ?>
<?=      t("Photo :: %photo_title", array("photo_title" => $theme->item()->title)) ?>
<?     else: ?>
<?=      t("Movie :: %movie_title", array("movie_title" => $theme->item()->title)) ?>
<?     endif ?>
<?   elseif ($theme->tag()): ?>
<?=    t("Browse Tag :: %tag_title", array("tag_title" => $theme->tag()->name)) ?>
<?   else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
<?=    t("Gallery") ?>
<?   endif ?>
<? endif ?></title>
  <meta name="robots" content="noindex, nofollow, noarchive" />
  <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet, noodp, noimageindex, notranslate" />
  <meta name="slurp" content="noindex, nofollow, noarchive, nosnippet, noodp, noydir" />
  <meta name="msnbot" content="noindex, nofollow, noarchive, nosnippet, noodp" />
  <meta name="teoma" content="noindex, nofollow, noarchive" />
  <link rel="shortcut icon" href="<?= $theme->url("images/favicon.ico") ?>" type="image/x-icon" />

  <?= $theme->script("jquery.js") ?>
  <?= $theme->script("jquery.form.js") ?>
  <?= $theme->script("jquery-ui.js") ?>

<?= $theme->head() ?>

  <link rel="stylesheet" href="<?= $theme->url("css/screen.css") ?>" type="text/css" media="screen,print,projection" />
  <link rel="stylesheet" href="<?= $theme->url("css/menus.css") ?>" type="text/css" media="screen,print,projection" />

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
    <? $logo_path = module::get_var("th_greydragon", "logo_path", url::file("lib/images/logo.png")); ?>
    <? // $theme->url("images/logo.png") ?>
    <img alt="<?= t("Gallery logo: Your photos on your web site")->for_html_attr() ?>" src="<?= $logo_path ?>" />
  </a>
<? endif ?>

<? if (!$user->guest): ?>
  <div id="g-site-menu">
  <?= $theme->site_menu("") ?>
  </div>
<? endif ?>
  <?= $theme->messages() ?>
<?= $theme->header_bottom() ?>
<? if (!empty($parents)): ?>
  <ul class="g-breadcrumbs">
    <? $i = 0 ?>
    <? foreach ($parents as $parent): ?>
    <li <? if ($i == 0) print " class=\"g-first\"" ?>>
      <a href="<?= $parent->url($parent == $theme->item()->parent() ?
         "show={$theme->item()->id}" : null) ?>">
        <?= html::purify($parent->title) ?>
      </a>
    </li>
    <? $i++ ?>
    <? endforeach ?>
    <li class="g-active <? if ($i == 0) print " g-first" ?>"><?= html::purify($theme->item()->title) ?></li>
  </ul>
<? endif ?>
</div>
<div id="g-main">
  <div id="g-main-in">
    <? if ($sidebarallowed == "any"): ?>
    <ul id="g-viewformat">
    <? if (($sidebarallowed == "left") or ($sidebarallowed == "any")): ?>
      <? $iscurrent = ($sidebarvisible == "left"); ?>
      <? $url = "" ?>
      <li><?= ($iscurrent) ? null : '<a title="Sidebar Left" href="' . $url . '?sb=left">'; ?><span class="g-viewthumb-left <?= ($iscurrent)? "g-viewthumb-current" : null; ?>">Sidebar Left</span><?= ($iscurrent)? null : "</a>"; ?></li>
    <? endif ?>
    <? if ($sidebarallowed == "any"): ?>
      <? $iscurrent = ($sidebarvisible == "none"); ?>
      <li><?= ($iscurrent) ? null : '<a title="No Sidebar" href="' . $url . '?sb=none">'; ?><span class="g-viewthumb-full <?= ($iscurrent)? "g-viewthumb-current" : null; ?>">No Sidebar</span><?= ($iscurrent)? null : "</a>"; ?></li>
    <? endif ?>
    <? if (($sidebarallowed == "right") or ($sidebarallowed == "any")): ?>
      <? $iscurrent = ($sidebarvisible == "right"); ?>
      <li><?= ($iscurrent) ? null : '<a title="Sidebar Right" href="' . $url . '?sb=right">'; ?><span class="g-viewthumb-right <?= ($iscurrent)? "g-viewthumb-current" : null; ?>">Sidebar Right</span><?= ($iscurrent)? null : "</a>"; ?></li>
    <? endif ?>
    </ul>
    <? endif ?>

    <div id="g-view-menu" class="g-buttonset">
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

<? if ($sidebarvisible=="left"): ?>
<?= '<div id="g-column-left">' ?>
<? elseif ($sidebarvisible=="none"): ?>
<? else: ?>
<?= '<div id="g-column-right">' ?>
<? endif ?>

<? if (($theme->page_subtype != "login") && ($sidebarvisible != "none")): ?>
<?= new View("sidebar.html") ?>
<? endif ?>
<?= ($sidebarvisible != "none")? "</div>" : null ?>

<? if ($sidebarvisible == "left"): ?>
<?= '<div id="g-column-centerright">' ?>
<? elseif ($sidebarvisible == "none"): ?>
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
  <ul id="g-credits">
    <?= $theme->credits() ?>
    <?php
      $theme_id = module::get_var("gallery", "active_site_theme");
      $ini = parse_ini_file(THEMEPATH . "$theme_id/theme.info");
      print "\n    <li>" . $ini["name"] . "</li>";
      print "\n    <li>&copy;" . $ini["author"] . "</li>";
    ?>

  </ul>
<? endif ?>
<? $copyright = module::get_var("th_greydragon", "copyright"); ?>
  <div id="g-footer-rightside"><?= ($copyright) ? $copyright : null; ?><br /><br />
<? // <a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10-blue" alt="Valid XHTML 1.0 Transitional" height="15" width="44" /></a> ?>
  </div>
  <?= $theme->user_menu() ?>
</div>
<?= $theme->page_bottom() ?>

<? // <!--start player-->
// <embed src="/music/collection.m3u" hidden="true" autostart="true" loop="true"></embed>
// <noembed><bgsound src="/music/collection.m3u"></noembed>
// <!--end player-->
//<object type="application/x-shockwave-flash" data="http://photo.dragonsoft.us/music/xspf_player/xspf_player.swf?playlist_url=http://photo.dragonsoft.us/music/collection.xspf&autoplay=true&repeat_playlist=true&player_title=KICK&playlist_size=3" width="400" height="151">
//<param name="movie" value="http://photo.dragonsoft.us/music/xspf_player/xspf_player.swf?playlist_url=http://photo.dragonsoft.us/music/collection.xspf&autoplay=true&repeat_playlist=true&player_title=KICK&playlist_size=3" />
//</object>
?>
</body>
</html>
