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
    $("#g-user-cloud-form").ajaxForm({
      dataType: "json",
      success: function(data) {
        if (data.result == "success") {
          $("#g-user-cloud").html(data.cloud);
        }
        $("#g-add-user-form").resetForm();
      }
    });
  });
</script>
<div id="g-user-cloud" ref="<?= url::site("photoannotation") ?>">
  <?= $cloud ?>
</div>
<?= $form ?>