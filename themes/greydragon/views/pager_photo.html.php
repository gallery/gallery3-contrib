<?php defined("SYSPATH") or die("No direct script access.") ?>

<?
  if ($sibling_count <= 1) {
    $pagination_msg = "&nbsp;";
  }
  else
  {
    $siblings = $item->parent()->children();
    $pagination_msg = t("Photo:") . ' ';
    if ($sibling_count < 13) {

      for ($i = 1; $i <= $sibling_count; $i++) {
        if ($i == $position) {
          $pagination_msg .= '<span>' . t($i) . '</span>';
        } else {
					
          $pagination_msg .= '<span><a href="' . $siblings[$i-1]->url() . '" title="Photo ' . t($i) . '">' . t($i) . '</a></span>';
        }
        if ($i < $sibling_count) { $pagination_msg .= '&middot;'; };
      }

    } elseif ($position < 9) {

      for ($i = 1; $i <= 10; $i++) {
        if ($i == $position) { 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        } else {
          $pagination_msg .= '<span><a href="' . $siblings[$i-1]->url() . '" title="Photo ' . t($i) . '">' . t($i) . '</a></span>';
        }
        if ($i < 10) { $pagination_msg .= '&middot;'; };
      }
      
      $pagination_msg .= '&hellip;';
      $pagination_msg .= '<span><a href="' . $siblings[$sibling_count - 2]->url() . '" title="Photo ' . t($sibling_count - 1) . '">' . t($sibling_count - 1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $siblings[$sibling_count - 1]->url() . '" title="Photo ' . t($sibling_count) . '">' . t($sibling_count) . '</a></span>';

    } elseif ($position > $sibling_count - 8) {

      $pagination_msg .= '<span><a href="' . $siblings[0]->url() . '" title="Photo ' . t(1) . '">' . t(1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $siblings[1]->url() . '" title="Photo ' . t(2) . '">' . t(2) . '</a></span>';
      $pagination_msg .= '&hellip;';

      for ($i = $sibling_count - 9; $i <= $sibling_count; $i++) {
        if ($i == $position) { 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        } else {
          $pagination_msg .= '<span><a href="' . $siblings[$i - 1]->url() . '" title="Photo ' . t($i) . '">' . t($i) . '</a></span>';
        }
        if ($i < $sibling_count) { $pagination_msg .= '&middot;'; };
      }

    } else {

      $pagination_msg .= '<span><a href="' . $siblings[0]->url() . '" title="Photo ' . t(1) . '">' . t(1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $siblings[1]->url() . '" title="Photo ' . t(2) . '">' . t(2) . '</a></span>';
      $pagination_msg .= '&hellip;';

      for ($i = $position - 5; $i <= $position + 5; $i++) {
        if ($i == $position) { 
          $pagination_msg .= '<span>' . t($i) . '</span>';
        } else {
          $pagination_msg .= '<span><a href="' . $siblings[$i - 1]->url() . '" title="Photo ' . t($i) . '">' . t($i) . '</a></span>';
        }
        if ($i < $position + 5) { $pagination_msg .= '&middot;'; };
      }

      $pagination_msg .= '&hellip;';
      $pagination_msg .= '<span><a href="' . $siblings[$sibling_count - 2]->url() . '" title="Photo ' . t($sibling_count - 1) . '">' . t($sibling_count - 1) . '</a></span>';
      $pagination_msg .= '&middot;';
      $pagination_msg .= '<span><a href="' . $siblings[$sibling_count - 1]->url() . '" title="Photo ' . t($sibling_count) . '">' . t($sibling_count) . '</a></span>';
    }
  }
?>

<ul class="g-pager">
<li class="g-pagination"><?= $pagination_msg; ?></li>
<li class="g-navigation">
  <? if ($position > 1): ?>
    <a href="<?= $siblings[0]->url() ?>" title="<?= t("first") ?>"><span class="ui-icon ui-icon-first">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-first-d">&nbsp;</span>
  <? endif ?>

  <? if ($previous_item): ?>
  <a href="<?= $previous_item->url() ?>" title="<?= t("previous") ?>"><span class="ui-icon ui-icon-prev">&nbsp;</span></a>
  <? else: ?>
  <span class="ui-icon ui-icon-prev-d">&nbsp;</span>
  <? endif; ?>

  <? if ($next_item): ?>
  <a href="<?= $next_item->url() ?>" class="ui-right" title="<?= t("next") ?>"><span class="ui-icon ui-icon-next">&nbsp;</span></a>
  <? else: ?>
  <span class="ui-icon ui-icon-next-d">&nbsp;</span>
  <? endif ?>

  <? if (($sibling_count > 1) && ($sibling_count > $position)): ?>
    <a href="<?= $siblings[$sibling_count-1]->url() ?>" class="ui-right" title="<?= t("last") ?>"><span class="ui-icon ui-icon-last">&nbsp;</span></a>
  <? else: ?>
    <span class="ui-icon ui-icon-last-d">&nbsp;</span>
  <? endif ?>
</li>
</ul>