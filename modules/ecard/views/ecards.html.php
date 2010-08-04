<?php defined("SYSPATH") or die("No direct script access.") ?>
<a href="<?= url::site("ecards/form_send/{$item->id}") ?>" id="g-add-ecard"
   class="g-button ui-corner-all ui-icon-left ui-state-default g-dialog-link">
  <span class="ui-icon ui-icon-ecard"></span>
  <?= t("Send an eCard") ?>
</a>
