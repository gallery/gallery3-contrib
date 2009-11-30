<?php defined("SYSPATH") or die("No direct script access.") ?>
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
  if (isset($_pagelist)) {
    unset($_pagelist);
  }

  switch ($page_type) {
    case "collection":
      $current_page = $page;
   	  $total_pages = $max_pages;
      // Prepare page url list
      for ($i = 1; $i <= $total_pages; $i++) {
        $_pagelist[$i] = url::site(url::merge(array("page" => $i)));
      }
      break;
    case "item":
      $current_page = $position; 
      $total_pages = $total;
      $siblings = $item->parent()->children();
      for ($i = 1; $i <= $total; $i++) {
        $_pagelist[$i] = $siblings[$i-1]->url();
      }
      break;
    default:
      $current_page = 1;
      $total_pages = 1;
      $_pagelist[1] = url::site();
      break;
  }

  if ($total_pages <= 1) {
    $pagination_msg = "&nbsp;";
  } else {
    $pagination_msg = t("Page:") . ' ';
    if ($total_pages < 13) {
      for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
          $pagination_msg .= '<span>' . t($i) . '</span>';
        } else {
          $pagination_msg .= '<span><a href="' . $_pagelist[$i] . '" title="Page ' . t($i) . '">' . t($i) . '</a></span>';
        }
        if ($i < $total_pages) { $pagination_msg .= '&middot;'; };
      }
    } elseif ($current_page < 9) {
      for ($i = 1; $i <= 10; $i++) {
        if ($i == $current_page) { 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        } else {
          $pagination_msg .= '<span><a href="' . $_pagelist[$i] . '" title="Page ' . t($i) . '">' . t($i) . '</a></span>';
        }
        if ($i < 10) { $pagination_msg .= '&middot;'; };
      }
      
      $pagination_msg .= '&hellip;';
      $pagination_msg .= '<span><a href="' . $_pagelist[$total_pages - 1] . '" title="Page ' . t($total_pages - 1) . '">' . t($total_pages - 1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $_pagelist[$total_pages] . '" title="Page ' . t($total_pages) . '">' . t($total_pages) . '</a></span>';

    } elseif ($current_page > $total_pages - 8) {

      $pagination_msg .= '<span><a href="' . $_pagelist[1] . '" title="Page ' . t(1) . '">' . t(1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $_pagelist[2] . '" title="Page ' . t(2) . '">' . t(2) . '</a></span>';
      $pagination_msg .= '&hellip;';

      for ($i = $total_pages - 9; $i <= $total_pages; $i++) {
        if ($i == $current_page) { 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        } else {
          $pagination_msg .= '<span><a href="' . $_pagelist[$i] . '" title="Page ' . t($i) . '">' . t($i) . '</a></span>';
        }
        if ($i < $total_pages) { $pagination_msg .= '&middot;'; };
      }

    } else {

      $pagination_msg .= '<span><a href="' . $_pagelist[1] . '" title="Page ' . t(1) . '">' . t(1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $_pagelist[2] . '" title="Page ' . t(2) . '">' . t(2) . '</a></span>';
      $pagination_msg .= '&hellip;';

      for ($i = $current_page - 5; $i <= $current_page + 5; $i++) {
        if ($i == $current_page) { 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        } else {
          $pagination_msg .= '<span><a href="' . $_pagelist[$i] . '" title="Page ' . t($i) . '">' . t($i) . '</a></span>';
        }
        if ($i < $current_page + 5) { $pagination_msg .= '&middot;'; };
      }

      $pagination_msg .= '&hellip;';
      $pagination_msg .= '<span><a href="' . $_pagelist[$total_pages - 1] . '" title="Page ' . t($total_pages - 1) . '">' . t($total_pages - 1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $_pagelist[$total_pages] . '" title="Page ' . t($total_pages) . '">' . t($total_pages) . '</a></span>';
    }
  }
?>

<ul class="g-paginator">
  <li class="g-pagination"><?= $pagination_msg ?></li>   

  <li class="g-navigation">
  <? if ($current_page > 1): ?>
    <a href="<?= $_pagelist[1] ?>" title="<?= t("first") ?>"><span class="ui-icon ui-icon-first">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-first-d">&nbsp;</span>
  <? endif ?>

  <? if (isset($previous_page_url)): ?>
    <a href="<?= $previous_page_url ?>" title="<?= t("previous") ?>"><span class="ui-icon ui-icon-prev">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-prev-d">&nbsp;</span>
  <? endif ?>

  <? if (isset($next_page_url)): ?>
    <a href="<?= $next_page_url ?>" class="ui-right" title="<?= t("next") ?>"><span class="ui-icon ui-icon-next">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-next-d">&nbsp;</span>
  <? endif ?>

  <? if ($current_page < $total_pages): ?>
      <a href="<?= $_pagelist[$total_pages] ?>" class="ui-right" title="<?= t("last") ?>"><span class="ui-icon ui-icon-last">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-last-d">&nbsp;</span>
  <? endif ?>
  </li>
</ul>