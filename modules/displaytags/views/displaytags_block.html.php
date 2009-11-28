<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-display-tags-block">
  <? for ($counter=0; $counter<count($tags); $counter++) { ?>
    <? if ($counter < count($tags)-1) { ?>
      <a href="<?= url::site("tags/$tags[$counter]") ?>"><?= html::clean($tags[$counter]->name) ?></a>,
    <? } else {?>
      <a href="<?= url::site("tags/$tags[$counter]") ?>"><?= html::clean($tags[$counter]->name) ?></a>
    <? } ?>
  <? } ?>
</div>
