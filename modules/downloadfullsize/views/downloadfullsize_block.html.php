<?php defined("SYSPATH") or die("No direct script access.") ?>

<? if ($theme->item->is_photo()) { ?>
<div class="gDownloadFullsizeBlock">
<a href="<?= url::site("downloadfullsize/send/$theme->item") ?>"
   title="<?= t("Download Photo") ?>"
  class="gButtonLink ui-icon-left ui-state-default ui-corner-all"><?= t("Download Fullsize Image") ?></a>
</div>
<? } ?>

<? if ($theme->item->is_movie()) { ?>
<div class="gDownloadFullsizeBlock">
<a href="<?= url::site("downloadfullsize/send/$theme->item") ?>"
   title="<?= t("Download Video") ?>"
  class="gButtonLink ui-icon-left ui-state-default ui-corner-all"><?= t("Download Video") ?></a>
</div>
<? } ?>

