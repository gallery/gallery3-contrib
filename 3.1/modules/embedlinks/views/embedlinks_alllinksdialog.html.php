<?php defined("SYSPATH") or die("No direct script access.") ?>
<script> 
  $(function() {
    $( "#tabs" ).tabs();
  });
</script> 
<h1 style="display: none;"><?= t("Link to this page") ?></h1>
<div id="tabs" style="height: 300px"> 
  <ul>
    <li><a class="g-menu-link" href="<?= url::site("embedlinks/showhtml/{$item_id}") ?>" title="<?= t("HTML Links") ?>"><?= t("HTML Links") ?></a></li>
    <li><a class="g-menu-link" href="<?= url::site("embedlinks/showbbcode/{$item_id}") ?>" title="<?= t("BBCode Links") ?>"><?= t("BBCode Links") ?></a></li>
    <li><a class="g-menu-link" href="<?= url::site("embedlinks/showfullurl/{$item_id}") ?>" title="<?= t("URLs") ?>"><?= t("URLs") ?></a></li>
  </ul> 
</div>
