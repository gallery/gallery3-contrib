<?php defined("SYSPATH") or die("No direct script access.") ?>
<? if (ecard::can_ecard()): ?>
<a href="<?= url::site("form/add/ecards/{$item->id}") ?>#ecard-form" id="g-add-ecard"
   class="g-button ui-corner-all ui-icon-left ui-state-default">
  <span class="ui-icon ui-icon-ecard"></span>
  <?= t("Send an eCard") ?>
</a>
<? endif ?>

<div id="g-ecard-detail">
  <a name="ecard-form" id="g-ecard-form-anchor"></a>
</div>
