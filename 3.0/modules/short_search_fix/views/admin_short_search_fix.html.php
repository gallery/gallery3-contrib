<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-block">
  <h1> <?= t("Short search fix settings") ?> </h1>
  <p>
    <?= t("This module works by padding every search term with a prefix.  Since MySQL system variables typically set the minimum search term to 4 characters, the default 2-character prefix makes all 2-letter searches valid.") ?>
  </p>
  <div class="g-block-content">
    <?= $form ?>
  </div>
</div>
