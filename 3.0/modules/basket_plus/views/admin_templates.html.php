<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-configure">
  <h1> <?= t("Configure texts for the order process.") ?> </h1>
  <p>
    <?= t("Use this page to configure the texts that the customer sees during the order process. Additionally, you can modify settings used only in the html emails.<br>
By using variables, the settings can be personalised. Please read the documentation for more information about variables you can use.") ?>
  </p>
  <?= $form ?>
</div>