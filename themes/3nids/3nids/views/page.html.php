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
          <?= t("Gallery :: %album_title", array("album_title" => $theme->item()->title)) ?>
          <? elseif ($theme->item()->is_photo()): ?>
          <?= t("Gallery :: %photo_title", array("photo_title" => $theme->item()->title)) ?>
          <? else: ?>
          <?= t("Gallery :: %movie_title", array("movie_title" => $theme->item()->title)) ?>
          <? endif ?>
        <? elseif ($theme->tag()): ?>
          <?= t("Gallery Tag :: %tag_title", array("tag_title" => $theme->tag()->name)) ?>
        <? else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
          <?= t("Gallery") ?>
        <? endif ?>
      <? endif ?>
    </title>
    <link rel="shortcut icon" href="<?= url::file("lib/images/favicon.ico") ?>" type="image/x-icon" />
    <?= $theme->css("yui/reset-fonts-grids.css") ?>
    <?= $theme->css("superfish/css/superfish.css") ?>
    <?= $theme->css("themeroller/ui.base.css") ?>
    <?= $theme->css("screen.css") ?>
    <?= $theme->css("jquery.fancybox.css") ?> 
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?= $theme->url("css/fix-ie.css") ?>"
          media="screen,print,projection" />
    <![endif]-->
    <? if ($theme->page_type == 'album'): ?>
      <? if ($thumb_proportion != 1): ?>
        <? $new_width = $thumb_proportion * 180 ?>
        <? $new_height = $thumb_proportion * 180 ?>
    <style type="text/css">
    #gContent #gAlbumGrid .gItem {
      width: <?= $new_width ?>px;
      height: <?= $new_height ?>px;
      /* <?= $thumb_proportion ?> */
    }
    </style>
      <? endif ?>
    <? endif ?>
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
    <?= $theme->script("gallery.form.js") ?>
    <?= $theme->script("superfish/js/superfish.js") ?>
    <?= $theme->script("jquery.localscroll.js") ?>
     <?= $theme->script("jquery.easing.js") ?>
    <?= $theme->script("jquery.fancybox.js") ?>
   <?= $theme->script("ui.init.js") ?>

    <? /* These are page specific, but if we put them before $theme->head() they get combined */ ?>
    <? if ($theme->page_type == "photo"): ?>
    <?= $theme->script("jquery.scrollTo.js") ?>
    <?= $theme->script("gallery.show_full_size.js") ?>
    <? elseif ($theme->page_type == "movie"): ?>
    <?= $theme->script("flowplayer.js") ?>
    <? endif ?>

    <?= $theme->head() ?>
  </head>

  <body <?= $theme->body_attributes() ?>>
  <script type="text/javascript">
	preload_image_object = new Image();
	image_url = new Array();
	var img_count = 0;
  </script>
    <?= $theme->page_top() ?>
    <div id="doc4" class="yui-t5 gView">
      <?= $theme->site_status() ?>
      <div id="gHeader">
        <div id="gBanner">
          <?= $theme->header_top() ?>
          <? if ($header_text = module::get_var("gallery", "header_text")): ?>
          <?= $header_text ?>
          <? else: ?>
          <a id="gLogo" href="<?= item::root()->url() ?>" title="<?= t("go back to the Gallery home")->for_html_attr() ?>">
            <img src="<?= url::file("lib/images/logo.png") ?>" />
          </a>
          <? endif ?>
          <div id="gSiteMenu">
	 <? if ($user->admin): ?> 
              <?= $theme->site_menu() ?>
	 <? endif ?>
          </div>
          <?= $theme->header_bottom() ?>
        </div>

        <? if (!empty($parents)): ?>
        <ul class="gBreadcrumbs">
          <? foreach ($parents as $parent): ?>
          <li>
            <!-- Adding ?show=<id> causes Gallery3 to display the page
                 containing that photo.  For now, we just do it for
                 the immediate parent so that when you go back up a
                 level you're on the right page. -->
            <a href="<?= $parent->url($parent == $theme->item()->parent() ?
                     "show={$theme->item()->id}" : null) ?>">
              <?= html::purify($parent->title) ?>
            </a>
          </li>
          <? endforeach ?>
          <li class="active"><?= html::purify($theme->item()->title) ?></li>
        </ul>
	<? elseif ($theme->tag()): ?>
	<ul class="gBreadcrumbs">
	  <li>
	     <a href="<?= url::site() ?>">
	      <?= t("Gallery") ?>
	    </a>
	 </li>
	  <li class="active"><?= html::purify($theme->tag()->name) ?></li>
	</ul>
	<? else: ?>
	<ul class="gBreadcrumbs">
	  <li>
	     <a href="<?= url::site() ?>">
	      <?= t("Gallery") ?>
	    </a>
	 </li>
	</ul>
	<? endif ?>
	
	<? if (module::is_active("tagsmap")): ?>
	  <ul class="gHeadMap">
	    <a href="<?= url::site("tagsmap/googlemap") ?>"><img src="<?= $theme->url("images/map.png") ?>"></a>
	  </ul>
	<? endif ?>
      </div>
      <div id="bd">
        <div id="yui-main">
          <div class="yui-b">
            <div id="gContent" class="yui-g">
              <?= $theme->messages() ?>
              <?= $content ?>
            </div>
          </div>
        </div>
        <div id="gSidebar" class="yui-b">
          <? if ($theme->page_type != "login"): ?>
          <?= new View("sidebar.html") ?>
          <? endif ?>
        </div>
      </div>
      <div id="gFooter">
        <?= $theme->footer() ?>
        <? if ($footer_text = module::get_var("gallery", "footer_text")): ?>
        <?= $footer_text ?>
        <? endif ?>

        <? if (module::get_var("gallery", "show_credits")): ?>
        <ul id="gCredits">
          <?= $theme->credits() ?>
        </ul>
        <? endif ?>
      </div>
    </div>
    <?= $theme->page_bottom() ?>
  <script type="text/javascript">
     if (img_count > 0){
    for(var i=0;i<=img_count;i++){
        preload_image_object.src = image_url[i];
     }
     }
  </script>
  </body>
</html>
