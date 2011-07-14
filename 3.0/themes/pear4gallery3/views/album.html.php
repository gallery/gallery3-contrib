<?php defined("SYSPATH") or die("No direct script access.") ?>
<? /* Placeholder for infromation in mosaic view. */ ?>
<script type="text/javascript">
$(function() {
	bodyLoad( "<?=module::get_var("th_pear4gallery3", "mainmenu_view")?>", 
	"<?=module::get_var("th_pear4gallery3", "background")?>");
 });
</script>
<table id="mosaicTable" style="width: 100%; margin: -2px -2px 0px 0px; overflow: hidden"> 
<tr>
<td  class="left" style="	width: 65%; vertical-align: middle; padding: 0px;">
<div id="gsImageView" class="gbBlock gcBorder1" style="padding: 0px !important; text-align: center;"> 
	<div style="padding: 0px; width: 0px; margin-top: 0px; opacity: 0.999999; display: none;" id="mosaicDetail"> 
		<div id="photo"> <img id="mosaicImg" src="" alt="Main image"/> </div> 
		<div class="gsContentDetail" style="width: 100%;"> 
			<div class="gbBlock gcBorder1" id="imageTitle"> </div>
		</div>
	</div>
</div> 
</td>
<td class="right" style="margin: 0px; padding: 0px; width: 35%; vertical-align: top;">
<div class="gallery-album" id="mosaicGridContainer" style="display: block;">
<? if (count($children)): ?>
  <? foreach ($children as $i => $child): ?>
    <? $item_class = "g-photo"; ?>
    <? if ($child->is_album()): ?>
      <? $item_class = "g-album\" onclick=\"window.location='".$child->url()."/'+getAlbumHash(skimimg);"; ?>
    <? endif ?>
    <? $img_class = "g-thumbnail"; ?>
    <? if ($child->is_photo()): ?>
      <? $img_class = "g-thumbnail p-photo"; ?>
    <? endif ?>
  <div id="g-thumb-id-<?= $child->id ?>" class="g-item gallery-thumb <?= $item_class ?>" title="<?= $child->description?>">
    <?= $theme->thumb_top($child) ?>
    <? if ($child->is_album()): ?>
		<div class="gallery-thumb-round" style="height: 200px; width: 200px;"></div>
    <? endif ?>
      <? if ($child->has_thumb()): ?>
		<?= $child->thumb_img(array("class" => $img_class, "id" => "thumb_$child->id", "style" => "width: 200px; height 200px;")) ?>
      <? endif ?>
<?// Begin skimming 
if($child->is_album()):
	$granchildren = $child->viewable()->children();
	$offset = 0;
	$step = round(200/min(count($granchildren),50));
	foreach ($granchildren as $i => $granchild):?>
      <? if(++$i > 50) break; ?>
      <? if ($granchild->has_thumb()): ?>
      <?= $granchild->thumb_img(array("style" => "display: none;")) ?>
	<div class="skimm_div" style="height: 200px; width: <?=$step?>px; left: <?=$offset?>px; top: 0px;" onmouseover="$('#thumb_<?=$child->id?>').attr('src', '<?=$granchild->thumb_url()?>');skimimg=<?=$i?>;" id="area_<?=$granchild->id?>"></div>
      <? endif ?>
<?		$offset+=$step;
endforeach; 
endif; 
// End skimming // ?>
	<p class="giTitle <? if(!$child->is_album()) print 'center';?>"><?= html::purify(text::limit_chars($child->title, 20)) ?> </p>
	<? if($child->is_album()): ?><div class="giInfo"><?= count($granchildren)?> photos</div><? endif ?>
</div>
   <?/* <?= $theme->thumb_bottom($child) ?>
    <?= $theme->context_menu($child, "#g-item-id-{$child->id} .g-thumbnail") ?>
    <h2><span class="<?= $item_class ?>"></span>
      <a href="<?= $child->url() ?>"><?= html::purify($child->title) ?></a></h2>
    <div class="g-metadata">
      <ol><?= $theme->thumb_info($child) ?></ol>
    </div>
  </div>*/?>
  <? endforeach ?>
<script type="text/javascript">
  var slideshowImages = new Array();
<? foreach ($children as $i => $child): ?>
<? if(!($child->is_album() || $child->is_movie())): ?>
    slideshowImages.push(['<?= $child->resize_url() ?>', '<?= url::site("exif/show/$child->id") ?>', '<?= $child->width ?>','<?= $child->height ?>', '<?= $child->title ?>', '<?= $child->file_url() ?>', '<?= $child->url() ?>']);
	<? endif ?>
<? endforeach ?>
</script>
<? else: ?>
  <? if ($user->admin || access::can("add", $item)): ?>
  <? $addurl = url::site("uploader/index/$item->id") ?>
  <li><?= t("There aren't any photos here yet! <a %attrs>Add some</a>.",
            array("attrs" => html::mark_clean("href=\"$addurl\" class=\"g-dialog-link\""))) ?></li>
  <? else: ?>
  <li><?= t("There aren't any photos here yet!") ?></li>
  <? endif; ?>
<? endif; ?>
</div>
</td></tr></table>
<?= $theme->album_bottom() ?>

<?= $theme->paginator() ?>
<div id="pearImageFlow" class="imageflow" style="display: none;"> </div>
