<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-display-tags-block" id="g-display-tags-block">
  <? $not_first = 0; ?>
  <? foreach ($tags as $tag): ?>
  <?= ($not_first++) ? "," : "" ?>
    <a href="<?= $tag->url() ?>"><?= html::clean($tag->name) ?></a>
  <? endforeach ?>
</div>

<script type="text/javascript">
  $("#g-add-tag-form").ready(function() {
    var url = $("#g-tag-cloud").attr("ref") + "/autocomplete";
    $("#g-add-tag-form input:text").autocomplete(
      url, {
        max: 30,
        multiple: true,
        multipleSeparator: ',',
        cacheLength: 1
      }
    );
    $("#g-add-tag-form").ajaxForm({
      dataType: "json",
      success: function(data) {
        if (data.result == "success") {
          var originalhtml = document.getElementById('g-display-tags-block').innerHTML;
          $("#g-display-tags-block").html(originalhtml + ", " + document.getElementById('g-add-tag-form').name.value);
        }
        $("#g-add-tag-form").resetForm();
      }
    });
  });
</script>

<?=tag::get_add_form($theme->item()); ?>