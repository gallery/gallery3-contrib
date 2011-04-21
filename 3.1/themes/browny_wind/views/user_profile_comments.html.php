<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-comment-detail">
<ul>
  <? foreach ($comments as $comment): ?>
  <li id="g-comment-<?= $comment->id ?>">
    <p class="g-author">
      <a href="<?= $comment->item()->url() ?>">
        <?= $comment->item()->thumb_img(array(), 70) ?>
      </a>
      <?= t("<i><strong>on</strong> %date <strong>for</strong> %title</i> ",
            array("date" => gallery::date_time($comment->created),
                  "title" => $comment->item()->title)); ?>
    </p>
    <hr />
    <div>
      <?= nl2br(html::purify($comment->text)) ?>
    </div>
  </li>
  <? endforeach ?>
</ul>
</div>
