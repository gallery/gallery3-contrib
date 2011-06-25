<?php defined("SYSPATH") or die("No direct script access.") ?>
  <h2>
    <?= t("Album Password Admin") ?>
  </h2>
  <br />
  <div class="g-block">
    <?= $albumpassword_form ?>
    <?= t("If this box is checked, accessing a protected album/photo/video will automatically log the visitor in with that items password.") ?><br /><br />
  </div>
