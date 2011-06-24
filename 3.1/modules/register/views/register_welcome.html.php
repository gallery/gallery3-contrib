<?php defined("SYSPATH") or die("No direct script access.") ?>
<html>
  <head>
    <title><?= t("Welcome to Gallery3") ?> </title>
  </head>
  <body>
    <h2><?= t("Welcome") ?> </h2>
    <p>
      <?= t("Hello, %name,", array("name" => $user->full_name ? $user->full_name : $user->name)) ?>
    </p>
    <p>
  <?= t("The user account you requested as been created.<br/>You can access the site by <a href=\"%site_url\">clicking this link</a> and you will be prompted to set your password at this point.",
        array("site_url" => html::mark_clean($site_url))) ?>
    </p>
  </body>
</html>
