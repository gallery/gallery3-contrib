<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  var parent_path = "<?= $parent_path ?>";
  var current_path = "<?= $resource->path ?>";
  var resource_type = "movie";
</script>
<div id="movie">
  <a id="movie-link" href="<?= $resource->url ?>" title="<?= $resource->title ?>"
   style="display: block; height: <?= $resource->size->height ?>px; width: <?= $resource->size->width ?>px;" />
</div>
<script type="text/javascript">
  flowplayer(
    "movie-link",
    {
      src: "<?= url::file("lib/flowplayer.swf") ?>",
      wmode: "transparent"
    },
    {
      plugins: {
        h264streaming: {
          url: "<?= url::file("lib/flowplayer.h264streaming.swf") ?>"
        },
        controls: {
          autoHide: 'always',
          hideDelay: 2000
        }
      }
    }
  )
</script>

