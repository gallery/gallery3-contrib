<?php defined("SYSPATH") or die("No direct script access.") ?>
<li class="ui-icon-left" ref="<?= $element->path ?>">
  <span class="<?= count($element->children) > 0 ? "ui-icon ui-icon-minus" : "ui-icon-none" ?>" >&nbsp;</span>
  <span class="tree-title"><?= $element->title ?></span>
  <ul class="ui-helper-clearfix tree-chidren">
    <? foreach ($element->children as $child): ?>
      <li ref="<?= $child->path ?>" class="ui-icon-left">
        <span class="<?= $child->has_children ? "ui-icon ui-icon-plus" : "ui-icon-none" ?>">&nbsp;</span>
        <span class="tree-title"><?= $child->title ?></span>
      </li>
    <? endforeach ?>
  </ul>
</li>

