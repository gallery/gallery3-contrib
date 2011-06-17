<?php defined("SYSPATH") or die("No direct script access.") ?>
<? if ($theme->page_subtype == "photo"): 
	foreach (end($parents)->viewable()->children() as $i => $child)
		if(!($child->is_album() || $child->is_movie()))
   		if($child->url() == $_SERVER['REQUEST_URI']):?>
				<html><body>
					<script type="text/javascript">window.location = '<? echo end($parents)->url() . "#img=$i&viewMode=detail&redirected=true"?>';</script>
					</body></html>
					<? die(0) ?>
<? endif ?>
<? endif ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?= $theme->html_attributes() ?> xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <? $theme->start_combining("script,css") ?>
    <title>
      <? if ($page_title): ?>
        <?= $page_title ?>
      <? else: ?>
        <? if ($theme->item()): ?>
          <?= $theme->item()->title ?>
        <? elseif ($theme->tag()): ?>
          <?= t("Photos tagged with %tag_title", array("tag_title" => $theme->tag()->name)) ?>
        <? else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
          <?= item::root()->title ?>
        <? endif ?>
      <? endif ?>
    </title>
    <link rel="shortcut icon"
          href="<?= url::file(module::get_var("gallery", "favicon_url")) ?>"
          type="image/x-icon" />

    <? if ($theme->page_type == "collection"): ?>
      <? if ($thumb_proportion != 1): ?>
        <? $new_width = round($thumb_proportion * 213) ?>
        <? $new_height = round($thumb_proportion * 240) ?>
        <style type="text/css">
        .g-view #g-content #g-album-grid .g-item {
          width: <?= $new_width ?>px;
          height: <?= $new_height ?>px;
          /* <?= $thumb_proportion ?> */
        }
        </style>
      <? endif ?>
    <? endif ?>

    <?= $theme->script("json2-min.js") ?>
    <?= $theme->script("jquery.js") ?>
    <?= $theme->script("jquery.form.js") ?>
    <?= $theme->script("jquery-ui.js") ?>
    <?= $theme->script("jquery-ui-1.7.3.custom.min.js") ?>
    <?= $theme->script("gallery.common.js") ?>
    <? /* MSG_CANCEL is required by gallery.dialog.js */ ?>
    <script type="text/javascript">
    var MSG_CANCEL = <?= t('Cancel')->for_js() ?>;
    </script>
    <?= $theme->script("gallery.ajax.js") ?>
    <?= $theme->script("gallery.dialog.js") ?>
    <?= $theme->script("superfish/js/superfish.js") ?>
    <?= $theme->script("jquery.localscroll.js") ?>

    <? /* These are page specific but they get combined */ ?>
    <? if ($theme->page_subtype == "photo"): ?>
    <?= $theme->script("jquery.scrollTo.js") ?>
    <?= $theme->script("gallery.show_full_size.js") ?>
    <? elseif ($theme->page_subtype == "movie"): ?>
    <?= $theme->script("flowplayer.js") ?>
    <? endif ?>

    <?= $theme->head() ?>

    <? /* Theme specific CSS/JS goes last so that it can override module CSS/JS */ ?>
    <?= $theme->script("ui.init.js") ?>
    <?= $theme->script("jquery.parsequery.js") ?>
    <?= $theme->script("imageflow.packed.js") ?>
    <?= $theme->css("yui/reset-fonts-grids.css") ?>
    <?= $theme->css("superfish/css/superfish.css") ?>
    <?= $theme->css("themeroller/ui.base.css") ?>
    <?= $theme->css("screen.css") ?>
    <?= $theme->css("imageflow.packed.css") ?>
    <!--[if lte IE 8]>
    <link rel="stylesheet" type="text/css" href="<?= $theme->url("css/fix-ie.css") ?>"
          media="screen,print,projection" />
    <![endif]-->

    <!-- LOOKING FOR YOUR JAVASCRIPT? It's all been combined into the link below -->
    <?= $theme->get_combined("script") ?>

    <!-- LOOKING FOR YOUR CSS? It's all been combined into the link below -->
    <?= $theme->get_combined("css") ?>
		<link rel="stylesheet" type="text/css" href="<?= $theme->url("css/pear.css") ?>" media="screen,print,projection" />
		<link rel="stylesheet" type="text/css" href="<?= $theme->url("icons/pear.css") ?>" media="screen,print,projection" />
		<script type="text/javascript" src="<?= $theme->url("js/pear.js"); ?>"></script>
		<!-- Google analytics code -->
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<? $account = module::get_var("th_pear4gallery3", "ga_code"); if ((!isset($account)) or ($account == "")) print "UA-23621420-1"; else print $account;?>']);
			_gaq.push(['_setDomainName', 'none']);
			_gaq.push(['_setAllowLinker', true]);
			_gaq.push(['_trackPageview']);
			(function() {
			 var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			 ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			 var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			 })();
		</script>
	</head>

  <body <?= $theme->body_attributes() ?>>
     <?= new View("hoverView.html") ?>
		 <?= $theme->page_top() ?>
      <?= $theme->site_status() ?>
<? if (($theme->page_subtype == "login") or ($theme->page_subtype == "reauthenticate")): ?>
	<?= $content ?>
<? else: /*not login | reauthenticate */ ?>
<div class="pear">

<div id="gsNavBar" class="gcBorder1">
	<div class="lNavBar">
	<? if ($theme->item()): ?>
		<? if(!empty($parents)): ?>
		<? $parent = end($parents) ?>
		<button class="large push large-with-push" onclick="window.location='<?= $parent->url($parent->id == $theme->item()->parent_id ? "show={$theme->item()->id}" : null) ?>';// + '#viewMode=' + viewMode;"> <div class="outer"> <div class="label"> <?= html::purify(text::limit_chars($parent->title, module::get_var("gallery", "visible_title_length"))) ?></div> </div></button>
		<? endif ?>
	</div>
	<div class="pearTitle" title="<?= $theme->item()->description ?>"> <?= html::purify(text::limit_chars($theme->item()->title, 40)) ?> &nbsp;
		<span class="count">(<?= count($theme->item()->children()) ?>)</span>
	</div>
	<? endif ?>
	<div class="rNavBar">
		<button class="large push large-with-push" onclick="$('#g-header').slideToggle('normal', function(){$('#g-header').is(':hidden') ? $('#sidebarButton').text('Show Options') : $('#sidebarButton').text('Hide Options')});//);toggleSidebar('ContentAlbum','sidebar'); return false;"> <div class="outer"> <div class="label" id="sidebarButton">Show Options</div></div></button>
	</div>
</div>

<div id="g-header" class="ui-helper-clearfix" style="display: none;">
	<div id="g-banner">
		<?= $theme->user_menu() ?>
		<?= $theme->header_top() ?>

		<!-- hide the menu until after the page has loaded, to minimize menu flicker -->
		<div id="g-site-menu" style="visibility: hidden">
			<?= $theme->site_menu($theme->item() ? "#g-item-id-{$theme->item()->id}" : "") ?>
		</div>
		<script type="text/javascript"> $(document).ready(function() { $("#g-site-menu").css("visibility", "visible"); }) </script>

		<?= $theme->header_bottom() ?>
	</div>
</div>

<?= $content ?>
		
<div id="footerWrapper">
	<div title="Change size of photos" id="sliderView" class="sliderView">
		<div class="sliderRightCap"></div>
		<div title="View at smallest photo size" class="smaller" onclick="$('#slider').slider('value', 0);"></div>
		<div title="View at largest photo size" class="larger" onclick="$('#slider').slider('value', 250);"></div>
		<div id="imgSlider" class="track">
		</div>
	</div>

	<div style="" class="" id="colorPicker">
		<div class="label">Color:</div>
		<div title="View this album with a black background" id="black" class="swatch" onclick="swatchSkin('black');return false;"> </div>
		<div title="View this album with a dark gray background" id="dkgrey" class="swatch" onclick="swatchSkin('dkgrey');return false;"> </div>
		<div title="View this album with a light gray background" id="ltgrey" class="swatch" onclick="swatchSkin('ltgrey');return false;"> </div>
		<div title="View this album with a white background" id="white" class="swatch" onclick="swatchSkin('white');return false;"> </div>
	</div>

	<div class="" style="" id="viewControls">
		<div title="Display this album in a grid view" id="grid" class="grid viewSwitcher sel sel-with-viewSwitcher" onclick="switchToGrid();">
			<div class="label">Grid</div>
		</div>
		<div title="Display this album in a mosaic view" id="mosaic" class="viewSwitcher mosaic" onclick="switchToMosaic();">
			<!-- <div style="margin-top:-2px;margin-left:-4px;"> -->
			<div class="label">Mosaic</div>
		</div>
		<div title="Display this album in a carousel view" id="carousel" class="carousel viewSwitcher" onclick="startImageFlow();">
			<!-- <div style="margin-top:-2px;"> -->
			<div class="label">Carousel</div>
		</div>
		<div title="Play a slideshow of this album" id="slideshow" class="viewSwitcher slideshow slideshow-with-viewSwitcher">
			<!-- <div style="margin-top:-2px;margin-left:-2px;"> -->
			<div class="label">Slideshow</div>
		</div>
		<div class="clear"></div>
	</div>
	<? if (!module::get_var("th_pear4gallery3", "hide_logo")): ?><button id="logoButton"></button><?endif?>
</div>
</div> <? /*class="pear"*/ ?>
<? endif ?>
  </body>
</html>
