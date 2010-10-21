<?php defined("SYSPATH") or die("No direct script access.") ?>
<a href="<?= url::site("ecard/form_send/{$item->id}") ?>" id="g-send-ecard"
   class="g-dialog-link g-button ui-state-default ui-corner-all">
  <span class="ui-icon-ecard"></span>
  <?= t("Send as eCard") ?>
</a>
