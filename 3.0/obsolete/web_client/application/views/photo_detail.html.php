<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  var parent_path = "<?= $parent_path ?>";
  var current_path = "<?= $resource->path ?>";
  var resource_type = "photo";
</script>
<div id="photo">
  <img src="<?= $resource->resize_url ?>" title="<?= $resource->title ?>" />
</div>
<div id="photo-info">
  <h1><?= $resource->title ?></hi>
</div>
