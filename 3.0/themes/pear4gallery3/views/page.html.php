<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
if (isset($_GET['ajax'])) {
  if ($theme->page_subtype == "search") {
    $v = new View("thumbs.html");
    $v->children = $content->items;
    print $v;
    die(0);
  }
  echo new View("thumbs.html");
  die(0);
}
?>
<? if ($theme->page_subtype == "photo"):
  foreach (end($parents)->viewable()->children() as $i => $child)
    if(!($child->is_album() || $child->is_movie()))
      if($child->url() == $_SERVER['REQUEST_URI']) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?= $theme->html_attributes() ?> xml:lang="en" lang="en">
  <head>
    <title>Photo page</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="refresh" content="1;url=<?=end($parents)->url()?>#img=<?=$i?>&amp;viewMode=detail&amp;redirected=true" />
    <?= $theme->head() ?>
  </head>
  <body>Page moved <a href="<?=end($parents)->url()?>#img=<?=$i?>&amp;viewMode=detail&amp;redirected=true">here</a>.</body>
</html>
<?
        die(0);
      }?>
<? endif ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?= $theme->html_attributes() ?> xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
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
    <?= $theme->script("jquery-1.7.1.min.js") ?>
    <?= $theme->script("jquery.form.js") ?>
    <?= $theme->script("jquery-ui-1.8.17.custom.min.js") ?>
    <?= $theme->script("jquery.endless-scroll.js") ?>
    <?= $theme->script("jquery.getscrollbarwidth.js") ?>
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
    <?= $theme->css("ui-pear-theme/jquery-ui-1.8.17.custom.css") ?>
    <?= $theme->css("screen.css") ?>
    <?= $theme->css("imageflow.packed.css") ?>
    <?= $theme->css("pear.css") ?>

    <!-- LOOKING FOR YOUR JAVASCRIPT? It's all been combined into the link below -->
    <?= $theme->get_combined("script") ?>

    <!-- LOOKING FOR YOUR CSS? It's all been combined into the link below -->
    <?= $theme->get_combined("css") ?>
    <!--[if lte IE 8]>
    <link rel="stylesheet" type="text/css" href="<?= $theme->url("css/fix-ie.css") ?>"/>
    <![endif]-->

		<script type="text/javascript" src="<?= $theme->url("js/pear.js"); ?>"></script>
		<? /* Remove Comment if Google Analytics wanted
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
		End remove section */?>
	</head>

  <body <?= $theme->body_attributes() ?>>
     <?= new View("hoverView.html") ?>
		 <?= $theme->page_top() ?>
      <?= $theme->site_status() ?>
<? if (($theme->page_subtype == "login") or ($theme->page_subtype == "reauthenticate")): ?>
	<?= $content ?>
<? else: /*not login | reauthenticate */ ?>

<div id="gsNavBar" class="gcBorder1">
    <div class="lNavBar">
    <? if(!empty($parents)): ?>
      <? foreach ($parents as $parent): ?>
      <? if (!module::get_var("th_pear4gallery3", "show_breadcrumbs")) $parent = end($parents); ?>
        <button class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" onclick="window.location='<?= $parent->url() ?>' + getAlbumHash(0);"> <span class="ui-button-text"><?= html::purify(text::limit_chars($parent->title, module::get_var("gallery", "visible_title_length"))) ?></span> </button>
      <? if (!module::get_var("th_pear4gallery3", "show_breadcrumbs")) break; ?>
      <? endforeach ?>
    <? elseif (!($theme->item() && $theme->item()->id == item::root()->id)): ?>
        <button class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" onclick="window.location='<?= item::root()->url() ?>' + getAlbumHash(0);"> <span class="ui-button-text"><?= html::purify(text::limit_chars(item::root()->title, module::get_var("gallery", "visible_title_length"))) ?></span> </button>
    <? endif ?>
    </div>
<? if ($theme->item()): ?>
    <div class="pearTitle" title="<?= $theme->item()->description ?>"> <?= html::purify(text::limit_chars($theme->item()->title, 40)) ?> &nbsp;
      <? if (!module::get_var("th_pear4gallery3", "hide_item_count")): ?>
        <span class="count">(<?= count($theme->item()->children()) ?>)</span>
      <? endif ?>
    </div>
<? else: ?>
    <div class="pearTitle">
      <? if ($page_title): ?>
          <?= html::purify(text::limit_chars($page_title, 40)) ?> &nbsp;
      <? else: ?>
        <? if ($theme->tag()): ?>
          <?= t("Photos tagged with %tag_title", array("tag_title" => $theme->tag()->name)) ?>
        <? else: /* Not an item, not a tag, no page_title specified.  Help! */ ?>
          <?= html::purify(text::limit_chars(item::root()->title, 40)) ?> &nbsp;
        <? endif ?>
      <? endif ?>
    </div>
<? endif ?>
    <div class="rNavBar">
        <button class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" onclick="$('#g-header').slideToggle('normal', function(){$('#g-header').is(':hidden') ? $('#sidebarButton').text('Show Options') : $('#sidebarButton').text('Hide Options')});"> <span class="ui-button-text">Show Options</span> </button>
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
<?= $theme->messages() ?>

<?= $content ?>

<div id="footerWrapper">
	<div title="Change size of photos" id="sliderView" class="sliderView">
		<div title="View at smallest photo size" class="smaller" onclick="$('#imgSlider').slider('value', 0);"></div>
		<div title="View at largest photo size" class="larger" onclick="$('#imgSlider').slider('value', 250);"></div>
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
<? if ($theme->page_subtype != "movie"): ?>
		<div title="Display this album in a grid view" id="grid" class="grid viewSwitcher sel sel-with-viewSwitcher viewSwitcher-icon">
			<span class="vs-icon vs-icon-grid"></span>Grid
		</div>
		<div title="Display this album in a mosaic view" id="mosaic" class="viewSwitcher mosaic">
			<span class="vs-icon vs-icon-mosaic"></span>Mosaic
		</div>
		<div title="Display this album in a carousel view" id="carousel" class="carousel viewSwitcher">
			<span class="vs-icon vs-icon-carousel"></span>Carousel
		</div>
		<div title="Play a slideshow of this album" id="slideshow" class="viewSwitcher slideshow slideshow-with-viewSwitcher">
			<span class="vs-icon vs-icon-slideshow"></span>Slideshow
		</div>
        <div class="clear"></div>
<? endif ?>
  </div>
    <? if (!module::get_var("th_pear4gallery3", "hide_logo")): ?>
    <? if (module::get_var("gallery", "logo_path")) {
      $logo_url = url::file(module::get_var("th_pear4gallery3", "logo_path"));
    } else {
      $logo_url = $theme->url("icons/pear_logo_sml.png");
    } ?>
      <button id="logoButton" style="background-image: url('<?= $logo_url ?>') !important"></button>
    <? endif ?>
</div>
<? endif ?>
  </body>
</html>
