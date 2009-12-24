<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-display-tags-block">
  <? $not_first = 0; ?>
  <? foreach ($tags as $tag): ?>
  <?= ($not_first++) ? "," : "" ?>
  <a href="<?= url::site("tags/{$tag->name}") ?>"><?= html::clean($tag->name) ?></a>
  <? endforeach ?>
</div>
