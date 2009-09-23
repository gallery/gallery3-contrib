<?php defined("SYSPATH") or die("No direct script access.") ?>
<? // @todo Set hover on AlbumGrid list items ?>

<? list($children_count_true, $children_all) = search::search($q,1000,0);
     $theme->pagination = new Pagination();
		$theme->pagination->initialize(array("query_string" => "page","total_items" => $children_count_true,"items_per_page" => $page_size,"style" => "classic"));
	$children_offset = ($theme->pagination->current_page -1) * $page_size ; ?>

<div id="gSearchResults">
  <h2><?= t("Results for <b>%term</b>", array("term" => $q)) ?></h2>

  <? if (count($items)): ?>
  <ul id="gAlbumGrid">
   <? for($i=0;$i<$children_offset;$i++): ?>
	  <? $child = $children_all[$i] ?>
	  <? if ($child->is_photo()): ?>
		<? $fancymodule = ""; ?>
		<? if (module::is_active("exif")){$fancymodule .= "exif::" . url::site("exif/show/{$child->id}") . ";;";} ?>
		<? if (module::is_active("comment") && module::is_active("comment_3nids")){$fancymodule .= "comment::" . url::site("comments_3nids?item_id={$child->id}") . ";;comment_count::" . comment_3nids::count($child) . ";;" ;} ?>
		<a href="<?=$child->file_url()?>" rel="fancygroup" class="fancyclass" title="<?= $child->parent()->title ?>, <?=$child->parent()->description?>" name="<?=$fancymodule  ?>"></a>
	  <? endif	 ?>
<? endfor ?>
   <? foreach ($items as $item): ?>
  <li id="gItemId-<?= $item->id ?>" class="gItem gAlbum">
    <?= $theme->thumb_top($item) ?>
    <? if (!($item->is_album())): ?>
    <? if ($item->is_photo()): ?>
		<? $fancymodule = ""; ?>
		<? if (module::is_active("exif")){$fancymodule .= "exif::" . url::site("exif/show/{$item->id}") . ";;";} ?>
		<? if (module::is_active("comment") && module::is_active("comment_3nids")){$fancymodule .= "comment::" . url::site("comments_3nids?item_id={$item->id}") . ";;comment_count::" . comment_3nids::count($item) . ";;" ;} ?>
		<a href="<?=$item->file_url()?>" rel="fancygroup" class="fancyclass" title="<?= $item->parent()->title ?>, <?=$item->parent()->description?>" name="<?=$fancymodule  ?>">
    <? else: ?>
      <a href="<?= $item->url() ?>">
    <? endif ?>
	      <img id="gPhotoId-<?= $item->id ?>" class="gThumbnail"
		   alt="photo" src="<?= $item->thumb_url() ?>"
		   width="<?= $item->thumb_width ?>"
		   height="<?= $item->thumb_height ?>" />
       </a>
    <a href="<?= $item->parent()->url() ?>?show=<?= $item->id?>"><h2><span></span><?= $item->parent()->title ?></h2></a>
     <? if ($user->admin): ?>
	<a href="<?=$item->url()?>">view</a>
     <? endif ?>
   <? else: ?>
           <a href="<?= $item->url() ?>">
	      <img id="gPhotoId-<?= $item->id ?>" class="gThumbnail"
		   alt="photo" src="<?= $item->thumb_url() ?>"
		   width="<?= $item->thumb_width ?>"
		   height="<?= $item->thumb_height ?>" />
                <h2><span></span><?= html::clean($item->title) ?></h2>
	 </a>   
 <? endif ?>
<?= $theme->thumb_bottom($item) ?>
<?= $theme->context_menu($item, "#gItemId-{$item->id} .gThumbnail") ?>
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
</ul>
  <?= $theme->pager() ?>

  <? else: ?>
  <p>
    <?= t("No results found for <b>%term</b>", array("term" => $q)) ?>
  </p>

  <? endif; ?>
</div>
