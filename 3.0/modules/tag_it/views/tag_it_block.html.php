<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
  $("#g-tag-it-add-tag-form").ready(function() {
    $("#g-tag-it-add-tag-form input:text").autocomplete(
      "<?= url::site("tags/autocomplete") ?>", {
        max: 30,
        multiple: true,
        multipleSeparator: ',',
        cacheLength: 1,
        selectFirst: false,
      }
    );
    $("#g-tag-it-add-tag-form").ajaxForm({
      dataType: "json",
      success: function(data) {
        $("#g-tag-it-add-tag-form").resetForm();
        $("#g-tag-it-tags-container").load("<?= url::site("tag_it/tags_for/{$item->id}") ?>");
      }
    });
  });
</script>
<div class="g-tag-it-block">
  <a href="<?= $item->url() ?>">
   <?= $item->thumb_img(array("class" => "g-thumbnail"), 180) ?>
  </a>
  <p id="g-tag-it-tags-container"></p>
  <?= $form ?>
</div>
