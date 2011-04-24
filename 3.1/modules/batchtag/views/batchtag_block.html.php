<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
  $("#g-batch-tag-form").ready(function() {
    var url = "<?= url::site("tags") ?>" + "/autocomplete";
    $("#g-batch-tag-form input:text").autocomplete(
      url, {
        max: 30,
        multiple: true,
        multipleSeparator: ',',
        cacheLength: 1
      }
    );
  });
</script>
<?= $batch_tag_form ?>
