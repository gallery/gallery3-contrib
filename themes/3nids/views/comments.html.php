<?php defined("SYSPATH") or die("No direct script access.") ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <?= $theme->css("yui/reset-fonts-grids.css") ?>
    <?= $theme->css("superfish/css/superfish.css") ?>
    <?= $theme->css("themeroller/ui.base.css") ?>
    <?= $theme->css("gallery.common.css") ?>
    <?= $theme->css("jquery.fancybox.css") ?> 
    <?= $theme->css("screen.css") ?>
    <?= $theme->css("3nids.css") ?>
    <?= $theme->script("jquery.js") ?>
    <?= $theme->script("jquery.form.js") ?>
    <?= $theme->script("jquery-ui.js") ?>
    <?= $theme->script("gallery.common.js") ?>
    <? /* MSG_CANCEL is required by gallery.dialog.js */ ?>
    <script type="text/javascript">
    var MSG_CANCEL = <?= t('Cancel')->for_js() ?>;
    </script>
    <?= $theme->script("gallery.ajax.js") ?>
    <?= $theme->script("gallery.dialog.js") ?>
    <?= $theme->script("superfish/js/superfish.js") ?>
    <?= $theme->script("jquery.localscroll.js") ?>
     <?= $theme->script("jquery.easing.js") ?>
    <?= $theme->script("jquery.fancybox.js") ?>
    <?= $theme->script("ui.init.js") ?>
<?= $theme->head() ?>
</head>
<body class="g-fancy-iframe-body">
	<div class="g-comment-thumb">	<img src="<?=$thumb?>"></div>
	  <a href="<?= url::site("form/add/comments_3nids/{$item_id}") ?>" id="g-admin-comment-button"
	   class="g-button ui-corner-all ui-icon-left ui-state-default right">
	  <span class="ui-icon ui-icon-comment"></span>
	  <?= t("Add a comment") ?>
	</a>
	<div id="g-comment-detail">
	<? if (!$comments->count()): ?>
	<p id="g-NoCommentsYet">
	  <?= t("No comments yet.") ?>
	</p>
	<? endif ?>
	<ul>
	  <? foreach ($comments as $comment): ?>
	  <li id="g-Comment-<?= $comment->id ?>" class="g-comment-box">
	      <?= t('<b>%name</b> <small>%date</small>: ',
		    array("date" => date(module::get_var("gallery", "date_time_format", "Y-M-d H:i:s"), $comment->created),
			  "name" => html::clean($comment->author_name()))); ?>
	    <div class="g-comment">
	      <?= nl2br(html::purify($comment->text)) ?>
	    </div>
	  </li>
	  <? endforeach ?>
	</ul>
	</div>
</body>
</html>
