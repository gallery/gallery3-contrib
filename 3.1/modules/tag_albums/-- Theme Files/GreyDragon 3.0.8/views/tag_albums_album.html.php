<?php defined("SYSPATH") or die("No direct script access.") ?>
<? 
  // The g-info block was taken from album.html.php and $theme->album_top() was changed to $theme->dynamic_top().
  // $item->title and $item->description have been changed to $title and $description.
  //
  // The g-album-grid block was also taken from album.html.php.  The section for uploading new photos to an empty album
  // has been removed.  Also, $theme->context_menu has been removed as well (it was crashing the page).
?>

<div id="g-album-header">
  <?= $theme->dynamic_top() ?>
  <h1><?= $theme->bb2html(html::purify($title), 1) ?></h1>
</div>

<?= $theme->add_paginator("top"); ?>

<? if (($theme->album_descmode == "top") and ($description)): ?>
  <div id="g-info"><div class="g-description"><?= $theme->bb2html(html::purify($description), 1) ?></div></div>
<? endif; ?>

<? if (isset($filter_text) && (module::get_var("tag_albums", "tag_index_filter"))): ?>
<div id="g-tags-filter">
<br/ >
  <center><?= $filter_text; ?></center>
</div>
<? endif ?>

<div class="g-album-grid-container">
<ul id="g-album-grid" class="<?= $theme->get_grid_column_class(); ?>">
<? if (count($children)): ?>
  <? foreach ($children as $i => $child): ?>
<?
  // This code is based on grey dragon's get_thumb_element function.
    $thumb_item = $child;

    $is_portrait = ($thumb_item->thumb_height > $thumb_item->thumb_width);

    $item_class = $child->is_album() ? "g-album" : "g-photo";
    $content  = '<li id="g-item-id-' . $child->id . '" class="g-item ' . $item_class . ' ' . $theme->thumb_type;
    if ($child->is_album()):
    	$_thumb_descmode = $theme->thumb_descmode_a;
		else:
    	$_thumb_descmode = $theme->thumb_descmode;
		endif;

	  $content .= ($_thumb_descmode == "bottom")? " g-expanded" : " g-default";
    $content .= ($is_portrait)? " g-portrait" : " g-landscape";
    $content .= '">' . $theme->thumb_top($child);

    if ($theme->thumb_topalign):
      $_shift = "";
    else:
      if (($theme->crop_factor == 1) and (!$is_portrait)): 
        $_shift = 'style="margin-top: ' . intval(($theme->_thumb_size_y - $thumb_item->thumb_height) / 2) . 'px;"';
      else:
        if (($theme->crop_factor > 0) and ($is_portrait)): 
          $_shift = 'style="margin-top: -' . intval(($thumb_item->thumb_height - $theme->_thumb_size_y) / 2) . 'px;"';
        else:
          $_shift = "";
        endif;
      endif;
    endif;

    // $ss = 'z-index: 22; opacity: 1; -ms-transform: rotate(' . (-15 + rand(0, 31)) . 'deg);'; style="' . $ss . '"
    
    $content .= '<div class="g-thumbslide"><p class="g-thumbcrop">';
    $content .= '<a '. $_shift . ' class="g-thumblink" href="' . $child->url() . '">';
    if ($thumb_item->has_thumb()):
      $content .= $thumb_item->thumb_img();
    else:
      $content .= '<img title="No Image" alt="No Image" src="' . $theme->url("images/missing-img.png") . '"/>';
    endif;
    $content .= '</a></p>';

    if (($theme->thumb_metamode != "hide") and ($_thumb_descmode == "overlay_bottom")):
      $_thumb_metamode = "merged";
    else:
	    $_thumb_metamode = $theme->thumb_metamode;
    endif;

    if (($_thumb_descmode == "overlay") or ($_thumb_descmode == "overlay_top") or ($_thumb_descmode == "overlay_bottom")):
      $content .= '<ul class="g-description ';
      if ($_thumb_descmode == "overlay_top"):
        $content .= 'g-overlay-top';
      endif;
      if ($_thumb_descmode == "overlay_bottom"):
        $content .= 'g-overlay-bottom';
      endif;
      $content .= '"><li class="g-title">' . $theme->bb2html(html::purify($child->title), 2) . '</li>';
      if ($_thumb_metamode == "merged"): 
        $content .= $theme->thumb_info($child);
      endif;
      $content .= '</ul>';
    endif;

    if (($_thumb_metamode == "default") and ($_thumb_descmode != "overlay_bottom")): 
      $content .= '<ul class="g-metadata">' . $theme->thumb_info($child) . '</ul>';
    endif;

    if ($_thumb_descmode == "bottom"):
      $content .= '<ul class="g-description">';
      $content .= '<li class="g-title">' . $theme->bb2html(html::purify($child->title), 2) . '</li>';
      if ($_thumb_metamode == "merged"): 
        $content .= $theme->thumb_info($child);
      endif;
      $content .= '</ul>';
    endif;

	/*
    if ($addcontext):
      $_text = $this->context_menu($child, "#g-item-id-{$child->id} .g-thumbnail");
      $content .= (stripos($_text, '<li>'))? $_text : null;
    endif;
    */
	
    $content .= '</div>';
    $content .= $theme->thumb_bottom($child);
    $content .= '</li>';

    print $content;
  // End rWatcher Edit.
?>
  <? endforeach ?>
<? else: ?>
  <li><?= t("There aren't any photos here yet!") ?></li>
<? endif; ?>
</ul>
</div>
<?= $theme->dynamic_bottom() ?>

<? if (($theme->album_descmode == "bottom") and ($description)): ?>
  <div id="g-info"><div class="g-description"><?= $theme->bb2html(html::purify($description), 1) ?></div></div>
<? endif; ?>

<?= $theme->add_paginator("bottom"); ?>
