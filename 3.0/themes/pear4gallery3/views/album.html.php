<?php defined("SYSPATH") or die("No direct script access.") ?>
<? /* Placeholder for infromation in mosaic view. */ ?>
<script type="text/javascript">
var slideshowImages = new Array();
var thumbImages = new Array();
<?
$defaultView = module::get_var("th_pear4gallery3", "mainmenu_view", "grid");
try {
  $result = ORM::factory("pear_album_view")->where("album_id", "=", $item->id)->find();
  if($result->loaded()) {
    $defaultView = $result->view_mode;
  }
} catch (Exception $e) {
  unset($e);
}
?>
$(window).load(function () {
  pearInit( {
    sitePath: "<?= url::site("/") ?>",
    defaultView: "<?=$defaultView?>",
    defaultBg: "<?=module::get_var("th_pear4gallery3", "background", "black")?>",
    mosaicEffect: "<? $mosaic_effect = module::get_var("th_pear4gallery3", "mosaic_effect", "blind"); if ($mosaic_effect == "none") print ""; else print $mosaic_effect; ?>",
    slideshowTimeout: <?=module::get_var("th_pear4gallery3", "slideshow_time", "5000")?> });
  sidebarInit('<?=module::get_var("th_pear4gallery3", "sidebar_view")?>');
});
</script>
<div id="loading"></div>
<div id="mosaicTable">
  <div id="mosaicDetail">
    <div id="mosaicHover" class="hoverViewTopMenu">
        <div id="detail_download" title="Download this photo" class="controller half" onclick="window.open(pear.sitePath + 'pear/download/' + slideshowImages[pear.currentImg][1])"> </div>
        <div id="detail_info" title="Show more information about this photo" class="controller half info_detail g-dialog-link"> </div>
        <? if(module::is_active("comment")): ?>
        <div id="detail_comment" title="Comments" class="detail controller half comments_detail g-dialog-link"></div>
        <? endif ?>
    </div>
    <div id="mosaicDetailContainer">
      <img id="mosaicImg" src="" alt="Main image"/>
        <div class="gsContentDetail" style="width: 100%;">
            <div class="gbBlock gcBorder1" id="imageTitle"> </div>
        </div>
    </div>
  </div>
  <div id="gridContainer" class="gallery-album">
    <?= new View("thumbs.html") ?>
  </div>
  <div id="pearFlow"><div id="pearImageFlow" class="imageflow"></div></div>
</div>
<? if (module::get_var("th_pear4gallery3", "sidebar_view") != ''): ?>
  <div id="sidebarContainer">
    <span id="toggleSidebar" class="ui-icon ui-icon-plusthick ui-state-default ui-helper-clearfix ui-widget ui-corner-all" title="Toggle Sidebar"></span>
    <div id="sidebar">
    <? if ($theme->page_subtype != "login"): ?>
      <?= new View("sidebar.html") ?>
    <? endif ?>
    </div>
  </div>
<? endif ?>
<? if(($theme->item())): ?>
<?= $theme->album_bottom() ?>
<? endif ?>

