<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-album-header">
  <div id="g-album-header-buttons">
    <?= $theme->dynamic_top() ?>
  </div>
  <h1><?= html::clean($title) ?></h1>
</div>
<? $children_all = $tag->items();
     $theme->pagination = new Pagination();
		$theme->pagination->initialize(array("query_string" => "page","total_items" => $children_count,"items_per_page" => $page_size,"style" => "classic"));
	$children_offset = ($theme->pagination->current_page -1) * $page_size ; ?>


<ul id="g-album-grid" class="ui-helper-clearfix">
<? for($i=0;$i<$children_offset;$i++): ?>
	<? $child = $children_all[$i] ?>
	<?= three_nids::fancylink($child,"header") ?>
<? endfor ?>

  <? foreach ($children as $i => $child): ?>

  <!--<li class="g-Item <?= $child->is_album() ? "g-album" : "" ?>">!-->
  <li id="g-item-id-<?= $child->id ?>" class="g-item g-album">
	<?= $theme->thumb_top($child) ?>
	<?= three_nids::fancylink($child,"dynamic") ?>
	<?= $theme->thumb_bottom($child) ?>
	<?= $theme->context_menu($child, "#g-ItemId-{$child->id} .g-Thumbnail") ?>
  </li>
  <? endforeach ?>
</ul>
<? for($i=$children_offset+$page_size;$i<$children_count;$i++): ?>
	 <? $child = $children_all[$i] ?>
	<?= three_nids::fancylink($child,"header") ?>
<? endfor ?>
<?= $theme->dynamic_bottom() ?>

<?= $theme->paginator() ?>
