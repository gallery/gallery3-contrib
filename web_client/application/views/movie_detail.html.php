<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="movie">
  <a id="movie-link" href="<?= $resource->url ?>" title="<?= $resource->title ?>" />
</div>
<script type="text/javascript">
  flowplayer(
    "movie-link",
    {
      src: "http://sandbox.timalmdal.com/g3_client/lib/flowplayer.swf",
      wmode: "transparent"
    },
    {
      plugins: {
        h264streaming: {
          url: "http://sandbox.timalmdal.com/g3_client/lib/flowplayer.h264streaming.swf"
        },
        controls: {
          autoHide: 'always',
          hideDelay: 2000
        }
      }
    }
  )
</script>

