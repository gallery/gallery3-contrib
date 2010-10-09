<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Grey Dragon Theme - a custom theme for Gallery 3
 * This theme was designed and built by Serguei Dosyukov,
 * whose blog you will find at http://blog.dragonsoft.us/
 * Copyright (C) 2009-2010 Serguei Dosyukov
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<? if ($theme->desc_allowbbcode): ?>
  <? $_description = $theme->bb2html($item->description, 1); ?>
<? else: ?>
  <? $_description = nl2br(html::purify($item->description)); ?>
<? endif; ?>

<? if ($theme->is_photometa_visible): ?>
<?   $_description .= '<ul class="g-metadata">' . $theme->thumb_info($item) . '</ul>'; ?>
<? endif; ?>

<div id="g-item">
  <? $_title = $theme->bb2html(html::purify($item->title), 1); ?>
  <div id="g-info">
    <h1><?= $_title ?></h1>
  </div>
  <?= $theme->add_paginator("top"); ?>
  <?= $theme->photo_top() ?>
  <? if (($theme->photo_descmode == "top") and ($_description)): ?>
    <div id="g-info"><div class="g-description"><?= $_description ?></div></div>
  <? endif; ?>
  <div id="g-photo">
    <?= $theme->resize_top($item) ?>
    <? if (access::can("view_full", $item)): ?>
    <? $_url = $item->file_url() . '" class="g-sb-preview" '; ?>
    <? else: ?>
    <?  $_url = '#"'; ?>
    <? endif; ?>
    <? $_resizewidth = $item->resize_width; ?>
    <? $siblings = $item->parent()->children(); ?>
    <? $siblings_count = count($siblings) - 1; ?>
    <div class="g-resize" style="margin-left: -<?= $_resizewidth / 2; ?>px; ">         
      <? for ($i = 0; (($i <= $siblings_count) and ($siblings[$i]->rand_key != $item->rand_key)); $i++):
          ?><a title="<?= $theme->bb2html(html::purify($siblings[$i]->title), 1) ?>" class="g-sb-preview" style="display: none;" href="<?= $siblings[$i]->file_url() ?>">&nbsp;</a><?
          $siblings_index = $i + 2;
         endfor; ?>
      <a href="<?= $_url ?> title="<?= $_title ?>">
      <?= $item->resize_img(array("id" => "g-photo-id-{$item->id}", "class" => "g-resize", "alt" => $_title)) ?>
      </a>
      <? for ($i = $siblings_index; $i <= $siblings_count; $i++):
          ?><a title="<?= $theme->bb2html(html::purify($siblings[$i]->title), 1) ?>" class="g-sb-preview" style="display: none;" href="<?= $siblings[$i]->file_url() ?>">&nbsp;</a><?
         endfor; ?>
      <? if (($theme->photo_descmode == "overlay") and ($_description)): ?>
        <span class="g-more">More</span>
        <span class="g-description" style="width: <?= $_resizewidth - 20; ?>px;" >
          <strong><?= $_title ?></strong>
          <?= $_description ?>
        </span>
      <? endif ?>
    </div>
    <?= $theme->resize_bottom($item) ?>
  </div>
  <? if (($theme->photo_descmode == "bottom") and ($_description)): ?>
    <div id="g-info"><div class="g-description"><?= $_description ?></div></div>
  <? endif; ?>
  <?= $theme->add_paginator("bottom"); ?>
  <?= $theme->photo_bottom() ?>
</div>
