<?php defined("SYSPATH") or die("No direct script access.") ?>
<?= $theme->paginator() ?>
<?  $javaScript = ""; ?>
<? if (count($children)): ?>
  <? foreach ($children as $i => $child): ?>
  <? if ($i > 50) break; ?>
    <? $item_class = "g-photo"; ?>
    <? if ($child->is_album()): ?>
      <? $item_class = "g-album\" onclick=\"window.location='".$child->url()."/'+getAlbumHash((typeof skimimg === 'undefined') ? 0 : skimimg);"; ?>
    <? endif ?>
    <? $img_class = "g-thumbnail"; ?>
    <? if ($child->is_photo()): ?>
      <? $img_class = "g-thumbnail p-photo"; ?>
    <? elseif ($child->is_movie()): ?>
      <? $item_class = "g-video\" onclick=\"window.location='".$child->url()."';"; ?>
      <? $img_class = "g-thumbnail p-movie"; ?>
    <? endif ?>
  <div id="g-item-id-<?= $child->id ?>" class="g-item gallery-thumb <?= $item_class ?>" title="<?= $child->description?>">
    <?= $theme->thumb_top($child) ?>
    <? if ($child->is_album() || $child->is_movie()): ?>
        <div class="gallery-thumb-round"></div>
    <? endif ?>
<?= $theme->context_menu($child, "#g-item-id-{$child->id} .g-thumbnail") ?>
      <? if ($child->has_thumb()): ?>
      <img id="thumb_<?= $child->id ?>" alt="<?= $child->id ?>" class="<?= $img_class ?>" style="width: 200px; height; 200px;" src="<?= $theme->url("icons/empty_image.png") ?>"/>
 <? $javaScript .= "thumbImages['thumb_" . $child->id . "'] = '" . $child->thumb_url() . "';\n" ?>
        <?// = $child->thumb_img(array("class" => $img_class, "id" => "thumb_$child->id", "style" => "width: 200px; height 200px;")) ?>
      <? else: ?>
        <span style="display: block; width: 200px; height: 200px;"></span>
      <? endif ?>
    <? if ($child->is_movie()): ?>
      <span class="p-video"></span>
    <? endif ?>
<?// Begin skimming
if($child->is_album()):
  $granchildren = $child->viewable()->children();
$offset = 0;
$step = round(200/min(count($granchildren),module::get_var("th_pear4gallery3", "skimm_lim", "50")));
foreach ($granchildren as $i => $granchild):?>
      <? if(++$i > module::get_var("th_pear4gallery3", "skimm_lim", "50")) break; ?>
      <? if ($granchild->has_thumb()): ?>
      <?= $granchild->thumb_img(array("style" => "display: none;")) ?>
 <? $javaScript .= "thumbImages['area_" . $granchild->id ."'] = '" . $granchild->thumb_img(array("style" => "display: none;")) . "';\n" ?>
    <div class="skimm_div" style="height: 200px; width: <?=$step?>px; left: <?=$offset?>px; top: 0px;" onmouseover="$('#thumb_<?=$child->id?>').attr('src', '<?=$granchild->thumb_url()?>');skimimg=<?=$i-1?>;" id="area_<?=$granchild->id?>"></div>
      <? endif ?>
<?		$offset+=$step;
endforeach;
endif;
// End skimming // ?>
    <p class="giTitle <? if(!$child->is_album()) print 'center';?>"><?= html::purify(text::limit_chars($child->title, 20)) ?> </p>
    <? if($child->is_album() && !module::get_var("th_pear4gallery3", "hide_item_count")): ?><div class="giInfo"><?= count($granchildren)?> photos</div><? endif ?>
</div>
  <? endforeach ?>
<script  type="text/javascript">
<? $item_no = ($page*$page_size)-$page_size; ?>
<? foreach ($children as $i => $child): ?>
<? if(!($child->is_album() || $child->is_movie())): ?>
slideshowImages[<?= $item_no++ ?>] = (['<?= $child->resize_url() ?>', '<?= $child->id ?>', '<?= $child->resize_width ?>','<?= $child->resize_height ?>', '<?= htmlentities($child->title, ENT_QUOTES) ?>', '<? if (access::can("view_full", $child)) print "true" ?>', '<?= $child->url() ?>']);
<? endif ?>
<? endforeach ?>
<?= $javaScript ?>
</script>
<? else: ?>
  <? if ($user->admin || access::can("add", $item)): ?>
  <? $addurl = url::site("uploader/index/$item->id") ?>
  <li><?= t("There aren't any photos here yet! <a %attrs>Add some</a>.",
    array("attrs" => html::mark_clean("href=\"$addurl\" class=\"g-dialog-link\""))) ?></li>
  <? else: ?>
  <li><?= t("There aren't any photos here yet!") ?></li>
  <? endif ?>
<? endif ?>
