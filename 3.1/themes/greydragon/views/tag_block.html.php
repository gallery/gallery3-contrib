<?php defined("SYSPATH") or die("No direct script access.") ?>
<script language="text/javascript" type="text/javascript">
  $("#g-add-tag-form").ready(function() {
    var url = $("#g-tag-cloud").attr("title") + "/autocomplete";
    $("#g-add-tag-form input:text").autocomplete(
      url, {
        max: 30,
        multiple: true,
        multipleSeparator: ',',
        cacheLength: 1}
    );

    $("#g-add-tag-form").ajaxForm({
      dataType: "json",
      success: function(data) {
        if (data.result == "success") {
          $("#g-tag-cloud").html(data.cloud);
        }
        $("#g-add-tag-form").resetForm();
      }
    });
  });
</script>
<div id="g-tag-cloud" title="<?= url::site("tags") ?>">
  <?= $cloud ?>
</div>
<?= $form ?>