<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  $("document").ready(function() {
    $("#g-tag").gallery_tag_cloud({
      movie: "<?= url::file("modules/tag_cloud/lib/tagcloud.swf") ?>"
      <? foreach ($options as $option => $value) : ?>
        , <?= $option ?> : "<?= $value ?>"
      <? endforeach ?>
    });
  });
</script>
<div id="g-tag-cloud" title="<?= url::site("tags") ?>">
  <?= $cloud ?>
</div>
<?= $form ?>

