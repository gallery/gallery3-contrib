<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Grey Dragon Theme - a custom theme for Gallery 3
 * This theme was designed and built by Serguei Dosyukov, whose blog you will find at http://blog.dragonsoft.us
 * Copyright (C) 2009-2011 Serguei Dosyukov
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to
 * the Free Software Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<? $theme->load_sessioninfo(); ?>
<!-- <?= $theme->themename ?> v.<?= $theme->themeversion ?> (ColorPack: <?= $theme->colorpack ?>) - Copyright (c) 2009-2011 Serguei Dosyukov - All Rights Reserved -->
<html xmlns="http://www.w3.org/1999/xhtml" <?= $theme->html_attributes() ?> xml:lang="en" lang="en" <?= ($theme->is_rtl)? "dir=rtl" : null; ?> >
<?
  $item = $theme->item();
  if (($theme->enable_pagecache) and (isset($item))):
    // Page will expire in 60 seconds
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 60).'GMT');  
    header("Cache-Control: public");
    header("Cache-Control: post-check=3600, pre-check=43200", false);
    header("Content-Type: text/html; charset=UTF-8");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  endif;
?>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<? $theme->start_combining("script,css") ?>
<? if ($page_title): ?>
<?   $_title = $page_title ?> 
<? else: ?>
<?   if ($theme->item()): ?>
<?     $_title = $theme->bb2html($theme->item()->title, 2); ?>
<?   elseif ($theme->tag()): ?>
<?     $_title = t("Photos tagged with %tag_title", array("tag_title" => $theme->bb2html($theme->tag()->name, 2))) ?>
<?   else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
<?     $_title = $theme->bb2html(item::root()->title, 2); ?>
<?   endif ?>
<? endif ?>
<title><?= $_title ?></title>
<? if ($theme->disable_seosupport): ?>
<meta name="robots" content="noindex, nofollow, noarchive" />
<meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet, noodp, noimageindex, notranslate" />
<meta name="slurp" content="noindex, nofollow, noarchive, nosnippet, noodp, noydir" />
<meta name="msnbot" content="noindex, nofollow, noarchive, nosnippet, noodp" />
<meta name="teoma" content="noindex, nofollow, noarchive" />
<? endif; ?>
<? if ($theme->blendpagetrans): ?>
<meta http-equiv="Page-Enter" content="blendTrans(Duration=0.5)" />
<meta http-equiv="Page-Exit" content="blendTrans(Duration=0.5)" />
<? endif; ?>
<!-- Internet Explorer 9 Meta tags : Start -->
<meta name="application-name" content="<?= $_title; ?>" />
<meta name="msapplication-tooltip" content="<?= t("Start"); ?> <?= $_title; ?>" />
<meta name="msapplication-starturl" content="<?= item::root()->url() ?>" />
<? if ($theme->allow_root_page): ?>
<meta name="msapplication-task" content="name=<?= t("Gallery") ?>: <?= t("Root Page") ?>; action-uri=<?= item::root()->url(); ?>?root=yes; icon-uri=favicon.ico" />
<meta name="msapplication-task" content="name=<?= t("Gallery") ?>: <?= t("Root Album") ?>; action-uri=<?= item::root()->url(); ?>?root=no; icon-uri=favicon.ico" />
<? else: ?>
<meta name="msapplication-task" content="name=<?= t("Gallery") ?>: <?= t("Root Album") ?>; action-uri=<?= item::root()->url(); ?>; icon-uri=favicon.ico" />
<? endif; ?>
<? if (identity::active_user()->admin): ?>
<meta name="msapplication-task-separator" content="gallery3-greydragon" />
<meta name="msapplication-task" content="name=<?= t("Admin") ?>: <?= t("Dashboard") ?>; action-uri=<?= url::site("admin"); ?>; icon-uri=favicon.ico" />
<? endif; ?>
<!-- Internet Explorer 9 Meta tags : End -->

<link rel="shortcut icon" href="<?= $theme->favicon ?>" type="image/x-icon" />
<? if ($theme->appletouchicon): ?>
<link rel="apple-touch-icon" href="<?= $theme->appletouchicon; ?>"/>
<? endif; ?>
<?= $theme->script("json2-min.js") ?>
<?= $theme->script("jquery.js") ?>
<?= $theme->script("jquery.form.js") ?>
<?= $theme->script("jquery-ui.js") ?>
<?= $theme->script("gallery.common.js") ?>
<? /* MSG_CANCEL is required by gallery.dialog.js */ ?>
<script type="text/javascript">
  var MSG_CANCEL = <?= t('Cancel')->for_js() ?>;
</script>
<?= $theme->script("gallery.ajax.js"); ?>
<?= $theme->script("gallery.dialog.js"); ?>

<? /* These are page specific but they get combined */ ?>
<? if ($theme->page_subtype == "photo"): ?>
<?=  $theme->script("jquery.scrollTo.js"); ?>
<? elseif ($theme->page_subtype == "movie"): ?>
<?=  $theme->script("flowplayer.js") ?>
<? endif ?>

<?= $theme->head() ?>

<? /* Theme specific CSS/JS goes last so that it can override module CSS/JS */ ?>
<?= $theme->script("animation.js"); ?>
<?= $theme->script("ui.support.js"); ?>

<?= $theme->theme_css_inject(); ?>

<!-- LOOKING FOR YOUR CSS? It's all been combined into the link below -->
<?= $theme->get_combined("css"); ?>

<?= $theme->css_link("colorpacks/" . $theme->colorpack . "/colors.css", FALSE); ?>
<?= $theme->css_link("framepacks/" . $theme->framepack . "/frame.css",  FALSE); ?>
<? if ($theme->custom_css_path != ""): ?>
<?=  $theme->css_link($theme->custom_css_path, TRUE); ?>
<? endif; ?>
<!-- LOOKING FOR YOUR JAVASCRIPT? It's all been combined into the link below -->
<?= $theme->get_combined("script") ?>

<!--[if IE 6]>
  <link rel="stylesheet" href="<?= $theme->url("css/old_ie.css") ?>" type="text/css" media="screen,print,projection" />
<![endif]-->
<? if ($theme->thumb_inpage): ?>
<style type="text/css"> #g-column-bottom #g-thumbnav-block, #g-column-top #g-thumbnav-block { display: none; } </style>
<? endif; ?>
</head>
<? if ($theme->item()): ?>
<?   $item = $theme->item(); ?>
<? else: ?>
<?   $item = item::root(); ?>
<? endif; ?>                             
<body <?= $theme->body_attributes() ?><?= ($theme->show_root_page)? ' id="g-rootpage"' : null; ?> <?= ($theme->is_rtl)? "class=\"rtl\"" : null; ?> >
<?= $theme->page_top() ?>
<?= $theme->site_status() ?>
<? if (((!$user->guest) or ($theme->show_guest_menu)) and ($theme->mainmenu_position == "bar")): ?>
  <style type="text/css">	html { margin-top: 30px !important; }	</style>
  <div id="g-site-menu" class="g-<?= $theme->mainmenu_position; ?>">
  <?= $theme->site_menu($theme->item() ? "#g-item-id-{$theme->item()->id}" : "") ?>
  </div>
<? endif ?>
<div id="g-header">
<?= $theme->header_top() ?>
<? if ($header_text = module::get_var("gallery", "header_text")): ?>
<span id="g-header-text"><?=  $theme->bb2html($header_text, 1) ?></span>
<? else: ?>
  <a id="g-logo" href="<?= item::root()->url() ?><?= ($theme->allow_root_page)? "?root=yes" : null; ?>" title="<?= t("go back to the Gallery home")->for_html_attr() ?>">
    <img alt="<?= t("Gallery logo: Your photos on your web site")->for_html_attr() ?>" src="<?= $theme->logopath ?>" />
  </a>
<? endif ?>

<? if (((!$user->guest) or ($theme->show_guest_menu)) and ($theme->mainmenu_position != "bar")): ?>
  <div id="g-site-menu" class="g-<?= $theme->mainmenu_position; ?>">
  <?= $theme->site_menu($theme->item() ? "#g-item-id-{$theme->item()->id}" : "") ?>
  </div>
<? endif ?>

<?= $theme->messages() ?>
<?= $theme->header_bottom() ?>

<? if ($theme->loginmenu_position == "header"): ?>
<?=  $theme->user_menu() ?>
<? endif ?>
<?
  // Custom rWatcher code, based on the breadcrumb menu function.
  // The rest of this file is the original page.html.php file from the Grey Dragon theme.
?>
<? if (empty($breadcrumbs)): ?>
<?= $theme->breadcrumb_menu($theme, null); ?>
<? else: ?>
<?
    $breadcrumb_content = "";
    if ($this->breadcrumbs_position == "hide"):
    else:
      $limit_title_length = module::get_var("gallery", "visible_title_length", 999);

      $breadcrumb_content .= '<ul class="g-breadcrumbs g-' . module::get_var("th_greydragon", "breadcrumbs_position", "default") . '">';
      $i = 0;
        foreach ($breadcrumbs as $breadcrumb):
		  if ($breadcrumb->url):
          $breadcrumb_content .= '<li ' . (($i == 0)? " class=\"g-first\"" : null) . '>';
          $breadcrumb_content .= '<a href="' . $breadcrumb->url . '">';
          $breadcrumb_content .= text::limit_chars($theme->bb2html(html::purify($breadcrumb->title), 2), $limit_title_length);
          $breadcrumb_content .= '</a></li>';
		  else:
          $breadcrumb_content .= '<li class="g-active ' . (($i == 0)? " g-first" : null) . '">' . text::limit_chars($theme->bb2html(html::purify($breadcrumb->title), 2), $limit_title_length) . '</li>';
		  endif;
          $i++;
        endforeach;
      $breadcrumb_content .= '</ul>';
    endif;

    print $breadcrumb_content;

  // End Edit.
?>
<? endif; ?>
<?= $theme->custom_header(); ?>
</div>
<? if (($theme->page_subtype != "login") and ($theme->page_subtype != "reauthenticate") and ($theme->sidebarvisible == "top")): ?>
<div id="g-column-top">
  <?= new View("sidebar.html") ?>
</div>
<? endif; ?>
<div id="g-main">
  <div id="g-main-in">
<?  if (!$theme->show_root_page): ?>
    <?= $theme->sidebar_menu($item->url()) ?>
    <div id="g-view-menu" class="g-buttonset<?= ($theme->sidebarallowed != "any")? " g-buttonset-shift" : null; ?>">
<?    if ($page_subtype == "album"): ?>
      <?= $theme->album_menu() ?>
<?    elseif ($page_subtype == "photo") : ?>
      <?= $theme->photo_menu() ?>
<?    elseif ($page_subtype == "movie") : ?>
      <?= $theme->movie_menu() ?>
<?    elseif ($page_subtype == "tag") : ?>
      <?= $theme->tag_menu() ?>
<?    endif ?>
    </div>
<?  endif; ?>
<? switch ($theme->sidebarvisible):
     case "left":
       echo '<div id="g-column-left">';
       $closediv = TRUE;
       break;
     case "none":
     case "top":
     case "bottom":
       if (($theme->thumb_inpage) and ($page_subtype == "photo")):
         echo '<div id="g-column-right">';
         $closediv = TRUE;
       else:
         $closediv = FALSE;
       endif;
       break;
     default:
       echo '<div id="g-column-right">';
       $closediv = TRUE;
       break;
   endswitch; ?>
<? if (($theme->page_subtype != "login") and ($theme->page_subtype != "reauthenticate")): ?>
<?   if (($theme->sidebarvisible == "none") or ($theme->sidebarvisible == "bottom") or ($theme->sidebarvisible == "top")): ?>
<?     if (($theme->thumb_inpage) and ($page_subtype == "photo")): ?>
<?=      '<div class="g-toolbar"><h1>&nbsp;</h1></div>'; ?>
<?=      $theme->get_block_html("thumbnav"); ?>
<?     endif; ?>
<?   else: ?>
<?=    new View("sidebar.html") ?>
<?   endif; ?>
<? endif ?>
<?= ($closediv)? "</div>" : null; ?>

<? switch ($theme->sidebarvisible):
     case "left":
       echo '<div id="g-column-centerright">';
       break;
     case "none":
     case "top":
     case "bottom":
       if (($theme->thumb_inpage) and ($page_subtype == "photo")):
         echo '<div id="g-column-centerleft">';
       else:
         echo '<div id="g-column-centerfull">';
       endif;
       break;
     default:
       echo '<div id="g-column-centerleft">';
       break;
   endswitch;

   if ($theme->show_root_page):
     echo new View("rootpage.html");
   else:
     echo $content;
   endif; ?>
    </div> 
  </div>
</div>
<? if (($theme->page_subtype != "login") and ($theme->page_subtype != "reauthenticate") and ($theme->sidebarvisible == "bottom")): ?>
<div id="g-column-bottom">
  <?= new View("sidebar.html") ?>
</div>
<? endif; ?>
<div id="g-footer">
<?= $theme->footer() ?>
<? if ($footer_text = module::get_var("gallery", "footer_text")): ?>
<span id="g-footer-text"><?=  $theme->bb2html($footer_text, 1) ?></span>
<? endif ?>
  <?= $theme->credits() ?>
  <ul id="g-footer-rightside"><li><?= $theme->copyright ?></li></ul>
<? if ($theme->loginmenu_position == "default"): ?>
  <?= $theme->user_menu() ?>
<? endif ?>
<?= $theme->custom_footer(); ?>
</div>
<?= $theme->page_bottom() ?>
</body>
</html>
