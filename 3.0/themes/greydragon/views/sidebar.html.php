<?php defined("SYSPATH") or die("No direct script access.") ?>

<?= $theme->sidebar_top() ?>
<div class="g-toolbar">&nbsp;</div>
<? if (($theme->page_subtype == "album") or ($theme->page_subtype == "photo") or ($theme->page_subtype == "movie") or ($theme->item())): ?>
<?= $theme->sidebar_blocks() ?>
<? endif; ?>
<?= $theme->sidebar_bottom() ?>
