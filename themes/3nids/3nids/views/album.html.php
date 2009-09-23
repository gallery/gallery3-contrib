<?php defined("SYSPATH") or die("No direct script access.") ?>
<? // @todo Set hover on AlbumGrid list items for guest users ?>
<div id="gInfo">
  <?= $theme->album_top() ?>
  <h1><?= html::purify($item->title) ?></h1>
  <div class="gDescription"><?= nl2br(html::purify($item->description)) ?></div>
</div>
<? $children_all = $item->viewable()->children();
     $theme->pagination = new Pagination();
		$theme->pagination->initialize(array("query_string" => "page","total_items" => $children_count,"items_per_page" => $page_size,"style" => "classic"));
	$children_offset = ($theme->pagination->current_page -1) * $page_size ; ?>
<ul id="gAlbumGrid">
<? if (count($children)): ?>
<? for($i=0;$i<$children_offset;$i++): ?>
	  <? $child = $children_all[$i] ?>
	  <? if ($child->is_photo()): ?>
			<? $fancymodule = ""; ?>
			<? if (module::is_active("exif")){$fancymodule .= "exif::" . url::site("exif/show/{$child->id}") . ";;";} ?>
			<? if (module::is_active("comment") && module::is_active("comment_3nids")){$fancymodule .= "comment::" . url::site("comments_3nids?item_id={$child->id}") . ";;comment_count::" . comment_3nids::count($child) . ";;" ;} ?>
			<a href="<?=$child->file_url()?>" rel="fancygroup" class="fancyclass" title="<?= $child->parent()->title ?>, <?=$child->parent()->description?>" name="<?=$fancymodule  ?>"></a>
	  <? endif	 ?>
<? endfor ?>
  <? foreach ($children as $i => $child): ?>
    <? $item_class = "gPhoto"; ?>
    <? if ($child->is_album()): ?>
      <? $item_class = "gAlbum"; ?>
    <? endif ?>
  <li id="gItemId-<?= $child->id ?>" class="gItem <?= $item_class ?>">
    <?= $theme->thumb_top($child) ?>
    <? if ($child->is_photo()): ?>
		<? $fancymodule = ""; ?>
		<? if (module::is_active("exif")){$fancymodule .= "exif::" . url::site("exif/show/{$child->id}") . ";;";} ?>
		<? if (module::is_active("comment") && module::is_active("comment_3nids")){$fancymodule .= "comment::" . url::site("comments_3nids?item_id={$child->id}") . ";;comment_count::" . comment_3nids::count($child) . ";;" ;} ?>
		<a href="<?=$child->file_url()?>" rel="fancygroup" class="fancyclass" title="<?= $child->parent()->title ?>, <?=$child->parent()->description?>" name="<?=$fancymodule  ?>">
         <?= $child->thumb_img(array("class" => "gThumbnail")) ?></a>
         <? if ($user->admin): ?>
	  <br><a href="<?=$child->url()?>">view/edit</a>
         <? endif ?>
    <? else: ?>
      <a href="<?= $child->url() ?>">
        <?= $child->thumb_img(array("class" => "gThumbnail")) ?>
       <h2><span></span><?= html::clean($child->title) ?></h2>
      </a>
    <? endif ?>
    <?= $theme->thumb_bottom($child) ?>
    <?= $theme->context_menu($child, "#gItemId-{$child->id} .gThumbnail") ?>
    <? if ($child->is_photo() && module::is_active("comment") && module::is_active("comment_3nids")) :?>
	<ul class="gMetadata">
		<li><a href="<?=url::site("comments_3nids?item_id={$child->id}")?>" class="iframe fancyclass"><?=comment_3nids::count($child) ?> <?=t("comments")?></a></li>   
	</ul>
     <? endif ?>
  </li>
  <? endforeach ?>
	<? for($i=$children_offset+$page_size;$i<$children_count;$i++): ?>
		  <? $child = $children_all[$i] ?>
		  <? if ($child->is_photo()): ?>
			<? $fancymodule = ""; ?>
			<? if (module::is_active("exif")){$fancymodule .= "exif::" . url::site("exif/show/{$child->id}") . ";;";} ?>
			<? if (module::is_active("comment") && module::is_active("comment_3nids")){$fancymodule .= "comment::" . url::site("comments_3nids?item_id={$child->id}") . ";;comment_count::" . comment_3nids::count($child) . ";;" ;} ?>
			<a href="<?=$child->file_url()?>" rel="fancygroup" class="fancyclass" title="<?= $child->parent()->title ?>, <?=$child->parent()->description?>" name="<?=$fancymodule  ?>"></a>
		  <? endif	 ?>
	<? endfor ?>
<? else: ?>
  <? if ($user->admin || access::can("add", $item)): ?>
  <? $addurl = url::file("index.php/simple_uploader/app/$item->id") ?>
  <li><?= t("There aren't any photos here yet! <a %attrs>Add some</a>.",
            array("attrs" => html::mark_clean("href=\"$addurl\" class=\"gDialogLink\""))) ?></li>
  <? else: ?>
  <li><?= t("There aren't any photos here yet!") ?></li>
  <? endif; ?>
<? endif; ?>
</ul>
<?= $theme->album_bottom() ?>

<?= $theme->pager() ?>
