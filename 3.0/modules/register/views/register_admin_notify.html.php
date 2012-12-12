<?php defined("SYSPATH") or die("No direct script access.") ?>
<html>
  <head>
    <title><?= ($subject_prefix.$subject) ?></title>
  </head>
  <body>
    <h2><?= $subject ?></h2>
    <p>
      <?= t("There's a new pending user registration from %name.<br/>You can access the site by <a href=\"%site_url\">clicking this link</a>, after which you can review and approve this request.",
        array("name" => $user->full_name ? $user->full_name : $user->name,
              "locale" => $locale,
              "site_url" => html::mark_clean($admin_register_url))) ?>
    </p>
  </body>
</html>
