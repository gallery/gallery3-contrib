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
<div id="g-album-header">
  <?= $theme->album_top() ?>
  <h1><?= $theme->bb2html(html::purify($item->title), 1) ?></h1>
</div>

<?= $theme->add_paginator("top"); ?>

<? if (($theme->photo_descmode == "top") and ($item->description)): ?>
  <div id="g-info"><div class="g-description"><?= $theme->bb2html(html::purify($item->description), 1) ?></div></div>
<? endif; ?>

<ul id="g-album-grid">
<? if (count($children)): ?>
  <? foreach ($children as $i => $child): ?>
    <?= $theme->get_thumb_element($child, TRUE) ?>
  <? endforeach ?>
<? else: ?>
  <? if ($user->admin || access::can("add", $item)): ?>
  <? $addurl = url::site("uploader/index/$item->id") ?>
  <li><?= t("There aren't any photos here yet! <a %attrs>Add some</a>.",
            array("attrs" => html::mark_clean("href=\"$addurl\" class=\"g-dialog-link\""))) ?></li>
  <? else: ?>
  <li><?= t("There aren't any photos here yet!") ?></li>
  <? endif; ?>
<? endif; ?>
</ul>
<?= $theme->album_bottom() ?>

<? if (($theme->photo_descmode == "bottom") and ($item->description)): ?>
  <div id="g-info"><div class="g-description"><?= $theme->bb2html(html::purify($item->description), 1) ?></div></div>
<? endif; ?>

<?= $theme->add_paginator("bottom"); ?>
