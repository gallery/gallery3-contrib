<?php defined("SYSPATH") or die("No direct script access.") ?>
<head>
<link rel="stylesheet" href="<?= url::file("modules/pages_xtra/css/admin_screen.css") ?>" type="text/css" media="screen,print,projection" />
</head>
<body>
 <? // <style type="text/css">  ?>
 <? // Styling code moved to pages_xtra/css/admin_screen.css  ?>
 <? // </style> ?>

<script type="text/javascript">    
  $(function() {
    $("textarea").htmlarea(); // Initialize all TextArea's as jHtmlArea's with default values
  });
</script>
<div class="g-block">
  <h1> <?= $theme->page_title ?> </h1>
  <div class="g-block-content">
    <?=$form ?>
  </div>
</div>
