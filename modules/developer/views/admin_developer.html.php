<?php defined("SYSPATH") or die("No direct script access.") ?>
<?= html::script("modules/developer/js/developer.js") ?>
<script>
$("#gDeveloperForm").ready(function() {
  ajaxify_developer_form("#gDeveloperForm form",  module_success);
});
 
</script>
<div id="gDeveloperAdmin">
  <h2><?= $title ?></h2>
  <div id="gDeveloperForm" >
    <?= $developer_content ?>
  </div>
</div>
