<?php defined("SYSPATH") or die("No direct script access.") ?>

<? if ($theme->item->is_photo()) { ?>
<div class="g-download-fullsize-block">
<a href="<?= url::site("downloadfullsize/send/{$theme->item->id}") ?>"
   title="<?= t("Download Photo") ?>"
  class="g-button ui-icon-left ui-state-default ui-corner-all"><?= t("Download Fullsize Image") ?></a>
</div>
<? } ?>

<? if ($theme->item->is_movie()) { ?>
<div class="g-download-fullsize-block">
<a href="<?= url::site("downloadfullsize/send/{$theme->item->id}") ?>"
   title="<?= t("Download Video") ?>"
  class="g-button ui-icon-left ui-state-default ui-corner-all"><?= t("Download Movie") ?></a>
</div>
<? } ?>

