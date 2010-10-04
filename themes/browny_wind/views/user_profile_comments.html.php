<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-comment-detail">
<ul>
  <? foreach ($comments as $comment): ?>
  <li id="g-comment-<?= $comment->id ?>">
    <p class="g-author">
      <?= $comment->item()->thumb_img(array(), 65) ?>
      <?= t("on %date for %title ",
            array("date" => gallery::date_time($comment->created),
                  "title" => $comment->item()->title)); ?>
      <a href="<?= $comment->item()->url() ?>">
      </a>
    </p>
    <hr />
    <div>
      <?= nl2br(html::purify($comment->text)) ?>
    </div>
  </li>
  <? endforeach ?>
</ul>
</div>
