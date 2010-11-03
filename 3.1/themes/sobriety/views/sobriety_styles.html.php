<?php defined("SYSPATH") or die("No direct script access.") ?>
    <?
    /*
     *
     */
    ?>
    <style type="text/css">
      <? $THUMB_SIZE = (int)module::get_var("gallery", "thumb_size"); $RESIZE_SIZE = (int)module::get_var("gallery", "resize_size"); ?>
      .g-item, .g-item > div, .g-item > a {
        width: <?= $THUMB_SIZE + 30 ?>px;
        height: <?= $THUMB_SIZE + 30 ?>px;
        line-height: <?= $THUMB_SIZE + 30 ?>px;
      }
      .g-item.g-album >  a {
        background-position: <?= $THUMB_SIZE - 2 ?>px 2px;
      }
      .g-item > h2 {
        width: <?= $THUMB_SIZE ?>px;
      }

      <? if( $item ): ?>
      #g-item #g-photo {
        margin-left: <?= -1*(int)($theme->item()->resize_width/2 + 10) ?>px;
      }
      #g-item #g-movie {
        width: <?= (int)$theme->item()->width ?>px;
        margin-left: <?= -1*(int)($theme->item()->width/2) ?>px;
      }
      <? endif ?>

      #g-item .g-paginator li.g-first,
      #g-item .g-paginator li.g-text-right {
        width: <?= $THUMB_SIZE + 30 ?>px;
        height: <?= $THUMB_SIZE + 30 ?>px;
      }
      #g-item .g-paginator li.g-first {
        right: <?= (int)($RESIZE_SIZE/2 + 10 + 10 + $THUMB_SIZE + 30) ?>px;
      }
      #g-item .g-paginator li.g-text-right {
        left: <?= (int)($RESIZE_SIZE/2 + 10 + 10) ?>px;
      }

      <? if( $item ): ?>
      #g-item .g-paginator li.g-first a:hover span,
      #g-item .g-paginator li.g-text-right a:hover span {
        width: <?= (int)($theme->item()->resize_width/2) ?>px;
      }
      #g-item .g-paginator li.g-first a:hover span {
        left: <?= ($THUMB_SIZE + 30 + 10 + 10) + (int)($RESIZE_SIZE/2 - $theme->item()->resize_width/2) ?>px;
      }
      <? endif ?>

      #g-item .g-paginator li.g-text-right a:hover span {
        left: <?= -1*(int)($RESIZE_SIZE/2 + 10 + 10) ?>px;
      }
      #g-item .g-block {
        width: <?= $RESIZE_SIZE + 10 + 10 ?>px;
      }

      <? if ($theme->page_subtype != "album"): ?>
      #g-sidebar {
        display: none;
      }
      <? endif ?>

      <? if( $item && !$item->is_album() ): ?>
        <? if($previous_item): ?>
        #g-item .g-paginator li.g-first a {
          background-image: url("<?= $previous_item->thumb_url() ?>");
        }
        <? else: ?>
        #g-item .g-paginator li.g-first {
          display: none;
        }
        <? endif ?>
        <? if($next_item): ?>
        #g-item .g-paginator li.g-text-right a {
          background-image: url("<?= $next_item->thumb_url() ?>");
        }
        <? else: ?>
        #g-item .g-paginator li.g-text-right {
          display: none;
        }
        <? endif ?>
      <? endif ?>
    </style>
