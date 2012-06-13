<?php defined("SYSPATH") or die("No direct script access.") ?>
<? if (count($items) == 0): ?>
  <center><?=t("This user hasn't uploaded anything yet."); ?></center>
<? else: ?>
<script>
$(document).ready(function() { setTimeout(LoadCarousel, 100); });
function LoadCarousel() {
  $(".main .jCarouselLite").jCarouselLite({
    btnNext: ".next",
    btnPrev: ".prev",
    visible: 4,
    circular: false
  });
}
</script>
<div id="jCarouselLite" class="cEnd" style="width: 570px;">
  <div class="carousel main">
    <a href="#" class="prev">&nbsp</a>
    <div class="jCarouselLite">
      <ul>
        <? foreach ($items as $photo): ?>
          <li class="g-item g-photo">
            <a href="<?= $photo->url() ?>" title="<?= html::purify($photo->title)->for_html_attr() ?>">
              <img <?= photo::img_dimensions($photo->thumb_width, $photo->thumb_height, 100) ?>
              src="<?= $photo->thumb_url() ?>" alt="<?= html::purify($photo->title)->for_html_attr() ?>" />
            </a>
          </li>
        <? endforeach ?>
      </ul>
    </div>
    <a href="#" class="next">&nbsp</a>
    <div class="clear"></div>   
  </div>
</div>
<br />
<div style="width: 510px; text-align: right;"><a href="<?=$str_view_more_url; ?>"><?=$str_view_more_title; ?> >></a></div>
<? endif; ?>
