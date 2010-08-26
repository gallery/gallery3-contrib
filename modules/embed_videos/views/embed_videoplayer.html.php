<?php defined("SYSPATH") or die("No direct script access.") ?>
<iframe class="youtube-player" type="text/html" width="640" height="385" src="http://www.youtube.com/embed/<?= substr($item->name, 0, strrpos($item->name, '.')); ?>" frameborder="0">
</iframe>
