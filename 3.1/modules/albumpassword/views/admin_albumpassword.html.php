<?php defined("SYSPATH") or die("No direct script access.") ?>
  <h2>
    <?= t("Album Password Admin") ?>
  </h2>
  <br />
  <div class="g-block">
    <?= t("If this box is checked, protected albums will only be hidden.  Anyone with the URL to either the album or it's contents will be able to access it without a password.") ?><br /><br />
    <?= $albumpassword_form ?>
  </div>
