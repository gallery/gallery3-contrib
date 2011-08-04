<?php defined("SYSPATH") or die("No direct script access.") ?>
<? if (module::get_var("embedlinks", "HTMLCode")) { ?>
<a href="<?= url::site("embedlinks/showhtml/{$item->id}") ?>" title="<?= t("HTML Links") ?>"
  class="g-dialog-link g-button ui-icon-left ui-state-default ui-corner-all">
  <span class="ui-icon ui-icon-info"></span>
  <?= t("Show HTML Code") ?>
</a><br />
<? } ?>

<? if (module::get_var("embedlinks", "BBCode")) { ?>
<a href="<?= url::site("embedlinks/showbbcode/{$item->id}") ?>" title="<?= t("BBCode Links") ?>"
  class="g-dialog-link g-button ui-icon-left ui-state-default ui-corner-all">
  <span class="ui-icon ui-icon-info"></span>
  <?= t("Show BBCode") ?>
</a>
<? } ?>

<? if (module::get_var("embedlinks", "FullURL")) { ?>
<a href="<?= url::site("embedlinks/showfullurl/{$item->id}") ?>" title="<?= t("URLs") ?>"
  class="g-dialog-link g-button ui-icon-left ui-state-default ui-corner-all">
  <span class="ui-icon ui-icon-info"></span>
  <?= t("Show URLs") ?>
</a>
<? } ?>
