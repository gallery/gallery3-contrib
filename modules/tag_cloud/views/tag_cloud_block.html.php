<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  $("document").ready(function() {
    $("#gTag").gallery_tag_cloud({
      movie: "<?= url::file("modules/tag_cloud/lib/tagcloud.swf") ?>"
      <? foreach ($options as $option => $value) : ?>
        , <?= $option ?> : <?= $value ?>
      <? endforeach ?>
    });
  });
</script>
<div id="gTagCloud" title="<?= url::site("tags") ?>">
  <?= $cloud ?>
</div>
<?= $form ?>

