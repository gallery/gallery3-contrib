<?php defined("SYSPATH") or die("No direct script access.") ?>
<select onchange="window.location='<?= url::site("items/__ID__")?>'.replace('__ID__', this.value)">
<? // We'll keep track of the list of items that we want to display in a stack ?>
<? $stack = array(array(0, $root)) ?>

<? // While there are still items to show, pick the next one and show it ?>
<? while ($stack): ?>
<? list($level, $album) = array_pop($stack) ?>
<option value="<?= $album->id ?>"><?= str_repeat("&nbsp;&nbsp;", $level) ?><?= $album->title ?></option>

<? // Then take all of that album's children and put them next on the stack. ?>
<? $tmp = array(); ?>
<? foreach ($album->viewable()->children(null, null, array(array("type", "=", "album"))) as $child): ?>
<?   $tmp[] = array($level + 1, $child) ?>
<? endforeach ?>

<? // Since we'll pull them off the stack in the opposite order that we put them on, ?>
<? // and the order that we put them on is the order in which we want to display them, ?>
<? // We need to reverse the order of the children on the stack ?>
<? if ($tmp): ?>
<?   $stack = array_merge($stack, array_reverse($tmp)) ?>
<? endif ?>
<? endwhile ?>
</select>
