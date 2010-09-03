<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  var parent_path = "";
  var current_path = "tags";
  var resource_type = "album";
</script>
<ul id="wc-thumb-grid" class="ui-helper-clearfix">
  <? foreach ($resources as $child): ?>
  <li class="wc-thumb-grid-cell" ref="<?= $child->path ?>">
      <a class="wc-child-link wc-image-block" href="#">
        <? if ($child->has_thumb): ?>
        <img src="<?= $child->thumb_url ?>" title="<?= $child->title ?>" />
        <? else: ?>
        <span><?= $child->title ?></span>
        <? endif ?>
      </a>
  </li>
  <? endforeach ?>
</ul>

