<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title><?= html::chars(__($title)) ?></title>
  <?=  html::stylesheet("css/reset-fonts-grids.css") ?>
  <?=  html::stylesheet("css/g3_client.css") ?>
  <?=  html::stylesheet("css/jquery-ui.css") ?>
  <!--[if lt IE 8]>
  <link rel="stylesheet" type="text/css" href="<?= url::file("css/fix-ie.css") ?>"
        media="screen,print,projection" />
  <![endif]-->
  <style type="text/css">
    .copyright { font-size: 0.9em; text-transform: uppercase; color: #557d10; }
  </style>
  <?= html::script("js/jquery.js") ?>
  <?= html::script("js/jquery.form.js") ?>
  <?= html::script("js/jquery-ui.js") ?>
  <?= html::script("js/g3_client.js") ?>
  <script type="text/javascript">
    $(document).ready(function () {
      $("#body div").gallery3_client();
    });
  </script>
</head>
<body>
  <div id="doc4" class="yui-t5">
    <div id="header" class="ui-helper-clearfix">
      <div>&nbsp;</div>
    </div>
    <div id="body">
      <div id="left">
        <ul id="album_tree"><?= $album_tree ?></ul>
      </div>
      <div id="center">
        <?= $detail ?>
      </div>
    </div>
    <div id="footer" class="ui-helper-clearfix">
      <p class="copyright">
      <?= __('Rendered in {execution_time} seconds, using {memory_usage} of memory')?><br />
      Copyright ©2009–2010 Gallery Team
      </p>
    </div>
  </div>
</body>
</html>