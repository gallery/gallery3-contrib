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
<div id="g-item-box">
<img src="<?=$item_url?>" id="g-item-img">
<?= $theme->context_menu($item, "#g-item-id-{$item->id}") ?>

</div>
</body>
</html>
