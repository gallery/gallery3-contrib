<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
  $("#g-user-cloud-form").ready(function() {
    var url = $("#g-user-cloud").attr("ref") + "/autocomplete";
    $("#g-user-cloud-form input:text").autocomplete(
      url, {
        max: 30,
        multiple: false,
        cacheLength: 1
      }
    );
  });
</script>
<div id="g-user-cloud" ref="<?= url::site("photoannotation") ?>">
  <?= $cloud ?>
</div>
<?= $form ?>