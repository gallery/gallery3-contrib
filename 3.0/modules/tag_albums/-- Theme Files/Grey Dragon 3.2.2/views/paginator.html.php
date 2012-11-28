<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Grey Dragon Theme - a custom theme for Gallery 3
 * This theme was designed and built by Serguei Dosyukov, whose blog you will find at http://blog.dragonsoft.us
 * Copyright (C) 2009-2012 Serguei Dosyukov
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to
 * the Free Software Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<?
// This is a generic paginator for album, photo and movie pages.  Depending on the page type,
// there are different sets of variables available.  With this data, you can make a paginator
// that lets you say "You're viewing photo 5 of 35", or "You're viewing photos 10 - 18 of 37"
// for album views.
//
// Available variables for all page types:
//   $page_type               - "collection", "item", or "other"
//   $page_subtype            - "album", "movie", "photo", "tag", etc.
//   $previous_page_url       - the url to the previous page, if there is one
//   $next_page_url           - the url to the next page, if there is one
//   $total                   - the total number of photos in this album
//
// Available for the "collection" page types:
//   $page                    - what page number we're on
//   $max_pages               - the maximum page number
//   $page_size               - the page size
//   $first_page_url          - the url to the first page, or null if we're on the first page
//   $last_page_url           - the url to the last page, or null if we're on the last page
//   $first_visible_position  - the position number of the first visible photo on this page
//   $last_visible_position   - the position number of the last visible photo on this page
//
// Available for "item" page types:
//   $position                - the position number of this photo
//
?>

<?
  $_pagelist = array();

  // rWatcher Mod
  if (isset($theme->dynamic_siblings)):
    $current_page = $position;
    $i = 1;
    $total_pages = count($theme->dynamic_siblings);
      foreach ($theme->dynamic_siblings as $one_sibling):
        if ($page_type == "item") {
          $_pagelist[$i] = url::site("tag_albums/show/" . $one_sibling->id . "/" . $tag_id . "/" . $album_id . "/" . urlencode($one_sibling->name));
        } elseif ($page_type == "") {
        }
        $i++;
      endforeach;
  else:
  // End rWatcher Mod.

  switch ($page_type) {
    case "collection":
      if (isset($item)):
        $parent = $item->parent();
      endif;
      $current_page = $page;
      $total_pages = $max_pages;
      // Prepare page url list
      for ($i = 1; $i <= $total_pages; $i++):
        $_pagelist[$i] = url::site(url::merge(array("page" => $i)));
      endfor;
      break;
    case "item":
      if (isset($item)):
        $parent = $item->parent();
      endif;

      if (isset($position)):
        $current_page = $position; 
      else:
        $current_page = 1;
      endif;

      $total_pages = $total;
      if (isset($parent)):
        $siblings = $parent->children();
        for ($i = 1; $i <= $total; $i++):
          $_pagelist[$i] = $siblings[$i-1]->url();
        endfor;
      endif;
      break;
    default:
      $current_page = 1;
      $total_pages = 1;
      $_pagelist[1] = url::site();
      break;
  }

// rWatcher Mod
  endif;
// End rWatcher Mod.

  if ($total_pages <= 1):
    $pagination_msg = "&nbsp;";
  else:
    $pagination_msg = t("Page:") . ' ';
    if ($total_pages < 13):
      for ($i = 1; $i <= $total_pages; $i++):
        if ($i == $current_page):
          $pagination_msg .= '<span>' . t($i) . '</span>';
        else:                                                                
          $pagination_msg .= '<span><a href="' . $_pagelist[$i] . '" title="' . t("Page") . ' ' . t($i) . '">' . t($i) . '</a></span>';
        endif;
        if ($i < $total_pages):
          $pagination_msg .= '&middot;'; 
        endif;
      endfor;
    elseif ($current_page < 9):
      for ($i = 1; $i <= 10; $i++):
        if ($i == $current_page): 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        else:
          $pagination_msg .= '<span><a href="' . $_pagelist[$i] . '" title="' . t("Page") . ' ' . t($i) . '">' . t($i) . '</a></span>';
        endif;
        if ($i < 10):
          $pagination_msg .= '&middot;'; 
        endif;
      endfor;
      
      $pagination_msg .= '&hellip;';
      $pagination_msg .= '<span><a href="' . $_pagelist[$total_pages - 1] . '" title="' . t("Page") . ' ' . t($total_pages - 1) . '">' . t($total_pages - 1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $_pagelist[$total_pages] . '" title="' . t("Page") . ' ' . t($total_pages) . '">' . t($total_pages) . '</a></span>';

    elseif ($current_page > $total_pages - 8):
      $pagination_msg .= '<span><a href="' . $_pagelist[1] . '" title="' . t("Page") . ' ' . t(1) . '">' . t(1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $_pagelist[2] . '" title="' . t("Page") . ' ' . t(2) . '">' . t(2) . '</a></span>';
      $pagination_msg .= '&hellip;';

      for ($i = $total_pages - 9; $i <= $total_pages; $i++):
        if ($i == $current_page): 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        else:
          $pagination_msg .= '<span><a href="' . $_pagelist[$i] . '" title="' . t("Page") . ' ' . t($i) . '">' . t($i) . '</a></span>';
        endif;
        if ($i < $total_pages):
          $pagination_msg .= '&middot;';
        endif;
      endfor;

    else:
      $pagination_msg .= '<span><a href="' . $_pagelist[1] . '" title="' . t("Page") . ' ' . t(1) . '">' . t(1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $_pagelist[2] . '" title="' . t("Page") . ' ' . t(2) . '">' . t(2) . '</a></span>';
      $pagination_msg .= '&hellip;';

      for ($i = $current_page - 5; $i <= $current_page + 5; $i++):
        if ($i == $current_page): 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        else:
          $pagination_msg .= '<span><a href="' . $_pagelist[$i] . '" title="' . t("Page") . ' ' . t($i) . '">' . t($i) . '</a></span>';
        endif;
        if ($i < $current_page + 5):
          $pagination_msg .= '&middot;';
        endif;
      endfor;

      $pagination_msg .= '&hellip;';
      $pagination_msg .= '<span><a href="' . $_pagelist[$total_pages - 1] . '" title="' . t("Page") . ' ' . t($total_pages - 1) . '">' . t($total_pages - 1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $_pagelist[$total_pages] . '" title="' . t("Page") . ' ' . t($total_pages) . '">' . t($total_pages) . '</a></span>';
    endif;
  endif; 
?>

<ul class="g-paginator">
  <li class="g-pagination"><?= $pagination_msg ?></li>   
  <li class="g-navigation">
  <? if ($current_page > 1): ?>
    <a title="<?= t("first") ?>" id="g-navi-first" href="<?= $_pagelist[1] ?>"><span class="ui-icon ui-icon-first">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-first-d">&nbsp;</span>
  <? endif ?>

  <? if (isset($previous_page_url)): ?>
    <a title="<?= t("previous") ?>" id="g-navi-prev" href="<?= $previous_page_url ?>"><span class="ui-icon ui-icon-prev">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-prev-d">&nbsp;</span>
  <? endif ?>

  <? // rWatcher Edit:  Use $theme->breadcrumbs instead of $parent to determine parent url. ?>
  <? if (count($theme->breadcrumbs) > 1): ?>
    <? end($theme->breadcrumbs); ?>
    <a title="<?= t("up") ?>" id="g-navi-parent" href="<?= prev($theme->breadcrumbs)->url; ?>"><span class="ui-icon ui-icon-parent">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-parent-d">&nbsp;</span>
  <? endif ?>
  <? // End rWatcher Edit. ?>

  <? if (isset($next_page_url)): ?>
    <a title="<?= t("next") ?>" class="ui-right" id="g-navi-next" href="<?= $next_page_url ?>"><span class="ui-icon ui-icon-next">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-next-d">&nbsp;</span>
  <? endif ?>

  <? if ($current_page < $total_pages): ?>
      <a title="<?= t("last") ?>" class="ui-right" id="g-navi-last" href="<?= $_pagelist[$total_pages] ?>"><span class="ui-icon ui-icon-last">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-last-d">&nbsp;</span>
  <? endif ?>
  </li>
</ul>