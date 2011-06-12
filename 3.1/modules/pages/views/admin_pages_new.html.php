<?php defined("SYSPATH") or die("No direct script access.") ?>
<body>
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
