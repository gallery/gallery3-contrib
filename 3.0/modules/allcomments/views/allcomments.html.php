<?php defined("SYSPATH") or die("No direct script access."); ?>

<h1>All Comments</h1>
<div id="allcomments">
<ul>
  <? foreach ($comments as $comment): ?>
  <div class="allcomments-comment">
   <li id="g-comment-<?= $comment->id ?>">
    <p class="allcomments-author">
     <a href="<?= $comment->item()->url() ?>">
       <?= t("on %date",
        array("date" => gallery::date_time($comment->created),
              "title" => $comment->item()->title)); ?></a><br />
     <a href="<?= $comment->item()->url() ?>">
       <?= $comment->item()->thumb_img(array(), 128) ?></a>
     <a href="<?= $comment->author_url() ?>">
     <?= nl2br(html::purify($comment->author_name())) ?></a>:
     <?= nl2br(html::purify($comment->text)) ?>
    </p>
   </li>
   </div>
  <? endforeach ?>
</ul>

<?php 
if($page > 0)
{
?>
	<a class="g-button ui-icon-right ui-state-default ui-corner-all" href="<?=url::base()?>allcomments/page/<?=$page-1?>">
        <span class="ui-icon ui-icon-seek-prev"></span>
        prev
	</a>
<?}?>
	<a class="g-button ui-icon-right ui-state-default ui-corner-all" href="<?=url::base()?>allcomments/page/<?=$page+1?>">
	<span class="ui-icon ui-icon-seek-next"></span>
	next
	</a>
</div>
