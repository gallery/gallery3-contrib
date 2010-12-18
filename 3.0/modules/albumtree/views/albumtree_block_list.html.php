<?php defined("SYSPATH") or die("No direct script access.") ?>
<style type="text/css">
  ul.treealbumnav {
    height: 225px;
    width: 190px;
    overflow: auto;
    border: 1px solid #666;
    padding: 2px;
  }
</style>

<ul class="treealbumnav">
  <? // We'll keep track of the list of items that we want to display in a stack ?>
  <? $stack = array(array(0, $root)) ?>

  <? // While there are still items to show, pick the next one and show it ?>
  <? while ($stack): ?>
  <? list($level, $album) = array_pop($stack) ?>
  <li>
    <a href="/index.php/items/<?= $album->id ?>"><?= str_repeat("&nbsp;&nbsp;", $level) ?><?= $album->title ?>
  </li>

  <? // Then take all of that album's children and put them next on the stack. ?>
  <? $tmp = array(); ?>
  <? foreach ($album->viewable()->children(null, null, array(array("type", "=", "album"))) as $child): ?>
  <? $tmp[] = array($level + 1, $child) ?>
  <? endforeach ?>

  <? // Since we'll pull them off the stack in the opposite order that we put them on, ?>
  <? // and the order that we put them on is the order in which we want to display them, ?>
  <? // We need to reverse the order of the children on the stack ?>
  <? if ($tmp): ?>
  <? $stack = array_merge($stack, array_reverse($tmp)) ?>
  <? endif ?>
  <? endwhile ?>
  </ul>
</div>

