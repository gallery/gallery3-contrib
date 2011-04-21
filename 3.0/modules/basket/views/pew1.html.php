<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="gAdminConfigure">
<SCRIPT language="JavaScript">function so(){document.generateKeys.submit();}</SCRIPT>
  <h1> <?= t("Paypal Encryption Wizard - Step 1") ?> </h1>
  <p>
    <?= t("The first step is to fill in some details about yourself. These details are used to create a set of encryption keys that will be used to communicate with paypal.") ?>
  </p>
  <?= $form ?>
  <a href="<?= url::site("admin/configure") ?>"
    class="left gButtonLink ui-state-default ui-corner-all ui-icon-left">
      <span class="ui-icon ui-icon-arrow-1-w"></span><?= t("Cancel") ?></a>

  <a href="<?= url::site("javascript: so();") ?>"
    class="right gButtonLink ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Next") ?></a>
</div>