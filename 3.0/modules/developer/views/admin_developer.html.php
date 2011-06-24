<?php defined("SYSPATH") or die("No direct script access.") ?>
<?= html::script("modules/developer/js/developer.js") ?>
<script type="text/javascript">
$("#g-developer-form").ready(function() {
  ajaxify_developer_form("#g-developer-form form",  module_success);
});
</script>
<div id="g-developer-admin">
  <h2><?= $title ?></h2>
  <div id="g-developer-form" >
    <?= $developer_content ?>
  </div>
</div>
