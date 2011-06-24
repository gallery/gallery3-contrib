<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="wc-tag-cloud">
  <h3>Tags</h3>
  <ul>
    <? foreach ($tags as $tag): ?>
    <li class="size<?=(int)(($tag->count / $max_count) * 7) ?>">
      <span><?= $tag->count ?> photos are tagged with </span>
      <a href="<?= url::site("g3_client/tagged_album/{$tag->name}") ?>"><?= $tag->name ?></a>
    </li>
    <? endforeach ?>
  </ul>
</div>
