<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="gAdminConfigure">
<SCRIPT language="JavaScript">function so(){document.paypalKey.submit();}</SCRIPT>
  <h1> <?= t("Paypal Encryption Wizard - Step 2") ?> </h1>
  <p>
    <?= t("Open your paypal account on a seperate window and navigate to My account / profile / Selling Preferences - Encrypted Payment Settings.") ?>
    <?= t("From this page press the download button to donwload paypals public certificate. Then paste the documents contents into the edit box below.") ?>
  </p>
  <?= $form ?>
  <a href="<?= url::site("admin/configure") ?>"
    class="left gButtonLink ui-state-default ui-corner-all ui-icon-left">
      <span class="ui-icon ui-icon-arrow-1-w"></span><?= t("Cancel") ?></a>

  <a href="<?= url::site("javascript: so();") ?>"
    class="right gButtonLink ui-state-default ui-corner-all ui-icon-right">
      <span class="ui-icon ui-icon-arrow-1-e"></span><?= t("Next") ?></a>
</div>