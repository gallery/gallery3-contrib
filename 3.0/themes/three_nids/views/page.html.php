<?php defined("SYSPATH") or die("No direct script access.") ?>
<? /* Don't show the extra chrome for photo and movie pages */ ?>
<? if ($page_subtype == "photo" || $page_subtype == "movie"): ?>
<?= $content ?>
<? return ?>
<? endif ?>

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
          <?= t("Gallery :: %tag_title", array("tag_title" => $theme->tag()->name)) ?>
        <? else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
          <?= t("Gallery") ?>
        <? endif ?>
      <? endif ?>
    </title>
    <link rel="shortcut icon" href="<?= url::file("lib/images/favicon.ico") ?>" type="image/x-icon" />
    <?= $theme->css("yui/reset-fonts-grids.css") ?>
    <?= $theme->css("superfish/css/superfish.css") ?>
    <?= $theme->css("themeroller/ui.base.css") ?>
    <?= $theme->css("jquery.fancybox.css") ?>
    <?= $theme->css("screen.css") ?>
    <?= $theme->css("three_nids.css") ?>
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?= $theme->url("css/fix-ie.css") ?>"
          media="screen,print,projection" />
    <![endif]-->
    <? if ($theme->page_type == 'collection'): ?>
      <? if ($thumb_proportion != 1): ?>
        <? $new_width = $thumb_proportion * 180 ?>
        <? $new_height = $thumb_proportion * 230 ?>
    <style type="text/css">
     /*#g-content #g-album-grid .g-item {
      width: <?= $new_width ?>px;
      height: <?= $new_height ?>px;*/
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
    <?= $theme->script("superfish/js/superfish.js") ?>
    <?= $theme->script("jquery.localscroll.js") ?>
     <?= $theme->script("jquery.easing.js") ?>
    <?= $theme->script("jquery.fancybox.js") ?>
    <?= $theme->script("ui.init.js") ?>

    <? /* These are page specific, but if we put them before $theme->head() they get combined */ ?>
    <? if ($theme->page_subtype == "photo"): ?>
    <?= $theme->script("jquery.scrollTo.js") ?>
    <?= $theme->script("gallery.show_full_size.js") ?>
    <? elseif ($theme->page_subtype == "movie"): ?>
    <?= $theme->script("flowplayer.js") ?>
    <? endif ?>

    <?= $theme->head() ?>
  </head>

  <body <?= $theme->body_attributes() ?>>
    <?= $theme->page_top() ?>
    <div id="doc4" class="yui-t5 g-view">
      <?= $theme->site_status() ?>
      <div id="g-header" class="ui-helper-clearfix">
        <div id="g-banner">
	<?= $theme->user_menu() ?>
          <?= $theme->header_top() ?>
          <? if ($header_text = module::get_var("gallery", "header_text")): ?>
          <?= $header_text ?>
          <? else: ?>
          <a id="g-logo" href="<?= item::root()->url() ?>" title="<?= t("go back to the Gallery home")->for_html_attr() ?>">
            <img alt="<?= t("Gallery logo: Your photos on your web site")->for_html_attr() ?>" src="<?= url::file("lib/images/logo.png") ?>" />
          </a>
          <? endif ?>
          <div id="g-site-menu">
	 <? if ($user->admin): ?>
           <?= $theme->site_menu("#g-item-img") ?>
	 <? endif ?>
          </div>
          <?= $theme->header_bottom() ?>
        </div>

        <? if (!empty($parents)): ?>
        <ul class="g-breadcrumbs">
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
	<ul class="g-breadcrumbs">
	  <li>
	     <a href="<?= url::site() ?>">
	      <?= t("Gallery") ?>
	    </a>
	 </li>
	  <li class="active"><?= html::purify($theme->tag()->name) ?></li>
	</ul>
	<? else: ?>
	<ul class="g-breadcrumbs">
	  <li>
	     <a href="<?= url::site() ?>">
	      <?= t("Gallery") ?>
	    </a>
	 </li>
	</ul>
	<? endif ?>

	<? if (module::is_active("tagsmap")): ?>
	  <ul class="g-map-head">
	    <a href="<?= url::site("tagsmap/googlemap") ?>"><img src="<?= $theme->url("images/map.png") ?>"></a>
	  </ul>
	<? endif ?>
      </div>
      <div id="bd">
        <div id="yui-main">
          <div class="yui-b">
            <div id="g-content" class="yui-g">
              <?= $theme->messages() ?>
              <?= $content ?>
            </div>
          </div>
        </div>
        <div id="g-sidebar" class="yui-b">
          <? if ($theme->page_subtype != "login"): ?>
          <?= new View("sidebar.html") ?>
          <? endif ?>
        </div>
      </div>
      <div id="g-footer">
        <?= $theme->footer() ?>
        <? if ($footer_text = module::get_var("gallery", "footer_text")): ?>
        <?= $footer_text ?>
        <? endif ?>

        <? if (module::get_var("gallery", "show_credits")): ?>
        <ul id="g-credits">
          <?= $theme->credits() ?>
        </ul>
        <? endif ?>
      </div>
    </div>
    <?= $theme->page_bottom() ?>
  </body>
</html>
