<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul id="thumb-grid" class="ui-helper-clearfix">
  <? foreach ($resource->children as $child): ?>
  <li class="thumb-grid-cell" ref="<?= $child->path ?>">
  <a class="child-link" href="#">
  <? if ($child->has_thumb): ?>
    <img src="<?= $child->thumb_url ?>" title="<?= $child->title ?>" />
  <? else: ?>
    <span><?= $child->title ?></span>
  <? endif ?>
  </a>
  </li>
  <? endforeach ?>
</ul>

