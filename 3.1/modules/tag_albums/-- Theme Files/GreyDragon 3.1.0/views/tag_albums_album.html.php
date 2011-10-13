<?php defined("SYSPATH") or die("No direct script access."); ?>
<? 
  // Used album.html.php as starting point.
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
<?
  if (count($children)):
		$siblings = $all_siblings;
		if (($theme->disablephotopage) && (count($siblings) > count($children))):
			$j = 0;
			foreach ($siblings as $i => $sibling):
				//if ($sibling->rand_key == $children[$j]->rand_key):
                if ($sibling->id == $children[$j]->item_id):
					//echo $theme->get_thumb_element($sibling, !$theme->hidecontextmenu);
                    echo rw_get_thumb_element($children[$j], $theme);
					if ($j + 1 < count($children)):
						$j++;
					endif;
				else:
					echo $theme->get_thumb_link($sibling);
					//echo rw_get_thumb_link($sibling, $theme);
				endif;
			endforeach;
		else:         
			foreach ($children as $i => $child):
				//echo $theme->get_thumb_element($child, !$theme->hidecontextmenu);
                echo rw_get_thumb_element($child, $theme);
			endforeach;
		endif;
	else: ?>
  <li><?= t("There aren't any photos here yet!") ?></li>
<? endif; ?>
</ul>
</div>

<?= $theme->dynamic_bottom() ?>

<? if (($theme->album_descmode == "bottom") and ($description)): ?>
  <div id="g-info"><div class="g-description"><?= $theme->bb2html(html::purify($description), 1) ?></div></div>
<? endif; ?>

<?= $theme->add_paginator("bottom"); ?>

<?
  function rw_get_thumb_link($item, $theme) {
    // This code is based on grey dragon's get_thumb_link function.
    // Change all $this to $theme
		if ($item->is_album()):
		  return "";
		endif;

		/*
	  if (access::can("view_full", $item)):
			$direct_link = $item->file_url();
		else:
			$direct_link = $item->resize_url();
		endif;*/
						$direct_link = $child->full_or_resize_url();

    return '<a title="' . $theme->bb2html(html::purify($item->title), 2) . '" style="display: none;" class="g-sb-preview" rel="g-preview" href="' . $direct_link . '">&nbsp;</a>';
  }

  function rw_get_thumb_element($child, $theme) {
    // This code is based on grey dragon's get_thumb_element function.
    // Change all $item to $child
    // Change all $this to $theme
    $thumb_item = $child;
    if ($theme->thumb_random):
      if ($child->is_album() && ($rnd = item::random_query()->where("parent_id", "=", $child->id)->find()) && $rnd->loaded()):
        $thumb_item = $rnd;
      endif;
    endif;

    $item_class = $child->is_album() ? "g-album" : "g-photo";
    $content = '<li id="g-item-id-' . $child->id . '" class="g-item ' . $item_class . ' ' . $theme->thumb_type;
    if ($child->is_album()):
    	$_thumb_descmode = $theme->thumb_descmode_a;
		else:
    	$_thumb_descmode = $theme->thumb_descmode;
		endif;

    $content .= ($_thumb_descmode == "bottom")? " g-expanded" : " g-default";

    if ($thumb_item->has_thumb()):
      $is_portrait = ($thumb_item->thumb_height > $thumb_item->thumb_width);
      $_shift = "";
      switch ($theme->thumb_imgalign):
        case "center":
          if (($theme->crop_factor == 1) and (!$is_portrait)): 
            $_shift = 'style="margin-top: ' . intval(($theme->_thumb_size_y - $thumb_item->thumb_height) / 2) . 'px;"';
          elseif ($theme->crop_factor > 0): 
            $_shift = 'style="margin-top: -' . intval(($thumb_item->thumb_height - $theme->_thumb_size_y) / 2) . 'px;"';
          endif;
          break;
        case "bottom":
          if (($theme->crop_factor == 1) and (!$is_portrait)): 
            $_shift = 'style="margin-top: ' . intval($theme->_thumb_size_y - $thumb_item->thumb_height) . 'px;"';
          elseif ($theme->crop_factor > 0): 
            $_shift = 'style="margin-top: -' . intval($thumb_item->thumb_height - $theme->_thumb_size_y) . 'px;"';
          endif;
          break;
        case "fit":
          break;
        case "top":
        default:
          break;
      endswitch;
    else:
      $is_portrait = FALSE;
      $_shift = 'style="margin-top: 0px;"';
    endif;

    $content .= ($is_portrait)? " g-portrait" : " g-landscape";
    $content .= '">' . $theme->thumb_top($child);

    $content .= '<div class="g-thumbslide">';
		$thumb_content = '<p class="g-thumbcrop">';

		$use_direct_link = (($theme->disablephotopage) && (!$child->is_album())); 
		$class_name = "g-thumblink";
		if ($use_direct_link):
			$class_name .= ' g-sb-preview" rel="g-preview';
		  //if (access::can("view_full", $child)):
				//$direct_link = $child->file_url();
			//else:
				$direct_link = $child->full_or_resize_url();
			//endif;
		else:
			$direct_link = $child->url();
		endif;

    if ($use_direct_link && module::is_active("exif") && module::info("exif")): 
      $thumb_content .= '<a class="g-meta-exif-link g-dialog-link" href="' . url::site("exif/show/{$child->id}") . '" title="' . t("Photo details")->for_html_attr() . '">&nbsp;</a>';
    endif;

    $thumb_content .= '<a title="' . $theme->bb2html(html::purify($child->title), 2) . '" '. $_shift . ' class="' . $class_name . '" href="' . $direct_link . '">';
    if ($thumb_item->has_thumb()):
      if (($theme->crop_factor > 1) && ($theme->thumb_imgalign == "fit")):
      	if ($thumb_item->thumb_height > $theme->_thumb_size_y):
      		if ($is_portrait):
      			$_max = $theme->_thumb_size_y;
      		else:
	      	  $_max = intval($theme->_thumb_size_x * ($theme->_thumb_size_y / $thumb_item->thumb_height));
	      	endif;
	      else:
	        $_max = $theme->_thumb_size_x;
        endif;
      	$_max = min($thumb_item->thumb_width, $_max);
        $thumb_content .= $thumb_item->thumb_img(array(), $_max);
      else:
        $thumb_content .= $thumb_item->thumb_img();
      endif;
    else:
      $thumb_content .= '<img title="No Image" alt="No Image" src="' . $theme->url("images/missing-img.png") . '"/>';
    endif;
    $thumb_content .= '</a></p>';

    if (($theme->thumb_metamode != "hide") and ($_thumb_descmode == "overlay_bottom")):
      $_thumb_metamode = "merged";
    else:
	    $_thumb_metamode = $theme->thumb_metamode;
    endif;

    if (($_thumb_descmode == "overlay") or ($_thumb_descmode == "overlay_top") or ($_thumb_descmode == "overlay_bottom")):
      $thumb_content .= '<ul class="g-description ';
      if ($_thumb_descmode == "overlay_top"):
        $thumb_content .= 'g-overlay-top';
      endif;
      if ($_thumb_descmode == "overlay_bottom"):
        $thumb_content .= 'g-overlay-bottom';
      endif;
      $thumb_content .= '"><li class="g-title">' . $theme->bb2html(html::purify($child->title), 2) . '</li>';
      if ($_thumb_metamode == "merged"): 
        $thumb_content .= $theme->thumb_info($child);
      endif;
      $thumb_content .= '</ul>';
    endif;

    if (($_thumb_metamode == "default") and ($_thumb_descmode != "overlay_bottom")): 
      $thumb_content .= '<ul class="g-metadata">' . $theme->thumb_info($child) . '</ul>';
    endif;

    if ($_thumb_descmode == "bottom"):
      $thumb_content .= '<ul class="g-description">';
      $thumb_content .= '<li class="g-title">' . $theme->bb2html(html::purify($child->title), 2) . '</li>';
      if ($_thumb_metamode == "merged"): 
        $thumb_content .= $theme->thumb_info($item);
      endif;
      $thumb_content .= '</ul>';
    endif;

    /*
    if ($addcontext):
      $_text = $this->context_menu($item, "#g-item-id-{$item->id} .g-thumbnail");
      $thumb_content .= (stripos($_text, '<li>'))? $_text : null;
    endif;
    */
		try {
	    $view = new View("frame.html");
	    $view->thumb_content = $thumb_content;
	    $content .= $view;
    } catch (Exception $e) {
			$content .= $thumb_content;
    }

    $content .= '</div>';
    $content .= $theme->thumb_bottom($child);
    $content .= '</li>';

    return $content;
    //print $content;
    // End of modified function code.
  }
?>