<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>   
  @import "<?= url::file("modules/tag_cloud_html5/css/admin_tag_cloud_html5.css"); ?>";
</style>
<div id="g-tag-cloud-html5-admin">
  <h2>
    <?= t("Tag cloud HTML5 settings") ?>
  </h2>
  <p>
    <?= t("This module uses TagCanvas, a non-flash, HTML5-compliant jQuery plugin.  It also uses <a href=\"http://excanvas.sourceforge.net/\">excanvas</a> to maintain compatibility with pre-9.0 Internet Explorer.") ?>
  </p>
  <p>
    <?= t("More details on the options below are given at TagCanvas's homepage <a href='http://www.goat1000.com/tagcanvas.php'>here</a>.") ?>
  </p>
  <?= $form ?>
</div>
