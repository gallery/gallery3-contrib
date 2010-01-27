<?php defined("SYSPATH") or die("No direct script access.") ?>

<div class="<?php if($item->height > $item->width): ?>vertical<?php endif; ?>">
  <div id="desc">
    <h1><?= html::purify($item->title) ?></h1>
    <p><?= nl2br(html::purify($item->description)) ?></p>
  </div>

  <div class="main">
    <p id="photo" style="padding:0 0px"><strong><?= $item->resize_img(array()) ?></strong></p>
  </div>

  <div id="meta">
    <ul>
      <li class="count">Photo <?= t("%position of %total", array("position" => $position, "total" => $sibling_count)) ?></li>
      <?php if($item->captured): ?><li class="date"><?= strftime("%e %B %Y", $item->captured); ?></li><?php endif; ?>
      <li class="tags"><$MTEntryKeywords$></li>
    </ul>
    <? if( access::can("edit", $item) ): ?>
    <ul id="actions">
      <li><a class="g-dialog-link" href="<?= url::site("form/edit/{$item->type}s/$item->id") ?>"><?= t($item->type == 'movie' ? "Edit movie" : "Edit photo") ?></a></li>
      <li><a class="g-dialog-link" href="<?= url::site("move/browse/$item->id") ?>"><?= t("Move to another album") ?></a></li>
      <? if( $item->is_photo() && graphics::can("rotate") ): ?>
      <li><a class="g-ajax-link" href="<?= url::site("quick/rotate/$item->id/ccw?csrf=$csrf&from_id=$theme_item->id") ?>" ajax_handler="function() { location.reload(); }"><?= t("Rotate 90° counter clockwise") ?></a></li>
      <li><a class="g-ajax-link" href="<?= url::site("quick/rotate/$item->id/cw?csrf=$csrf&from_id=$theme_item->id") ?>" ajax_handler="function() { location.reload(); }"><?= t("Rotate 90° clockwise") ?></a></li>
      <? endif; ?>
      <li><a class="g-dialog-link" href="<?= url::site("quick/form_delete/$item->id?csrf=$csrf&from_id=$theme_item->id") ?>"><?= t($item->type == 'movie' ? "Delete this movie": "Delete this photo") ?></a></li>
    </ul>
    <? endif; ?>
  </div>

  <div class="main"></div>

  <div id="prevnext">
    <?php if($previous_item): ?>
    <div id="prev">
      <span class="thumb"><em style="background-image: url('<?= $previous_item->thumb_url() ?>');"><a href="<?= $previous_item->url() ?>" accesskey="z"><strong>Previous: </strong><?= html::purify($previous_item->title) ?></a></em></span>
    </div>
    <?php endif; ?>

    <?php if($next_item): ?>
    <div id="next">
      <span class="thumb"><em style="background-image: url('<?= $next_item->thumb_url() ?>');"><a href="<?= $next_item->url() ?>" accesskey="z"><strong>Next: </strong><?= html::purify($next_item->title) ?></a></em></span>
    </div>
    <?php endif; ?>
  </div>
</div>

  <?= $theme->photo_bottom() ?>
