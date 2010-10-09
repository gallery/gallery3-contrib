<?php defined("SYSPATH") or die("No direct script access.") ?>
<html>
  <head>
    <title><?= t("User registration confirmation") ?> </title>
  </head>
  <body>
    <h2><?= t("User registration confirmation") ?> </h2>
    <p>
      <?= t("Hello, %name,", array("name" => $user->full_name ? $user->full_name : $user->name)) ?>
    </p>
    <p>
  <?= t("We received a request to to create a user with this email.  If you made this request, you can confirm it by <a href=\"%confirm_url\">clicking this link</a>.  If you didn't request this password reset, it's ok to ignore this mail.",
        array("site_url" => html::mark_clean(url::base(false, "http")),
              "confirm_url" => $confirm_url)) ?>
    </p>
  </body>
</html>
