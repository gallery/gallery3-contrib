<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-block">
  <img src="<?= url::file("modules/addthis/images/addthis_logo.png") ?>" alt="Add This logo" class="g-right"/>
  <h1> <?= t("Add This Social bookmarking") ?> </h1>
  <div class="g-block-content">
    <p>
      <?= t("A collection of all the services and destinations available through AddThis. Some are for sharing and bookmarking, others are 'utilities' like printing and translation.<br/>
AddThis uses services to provide an intelligent, optimized sharing menu that is designed to offer the right options at the right time and maximize distribution of your content - everywhere.") ?>
    </p>
    <ul>
      <li class="g-module-status g-success">
        <?= t("You're ready to share your content!") ?>
      </li>
    </ul>
    <p>
      <?= t("You don't need an account with Add This, but if you <a href=\"%signup_url\">register with Add This</a> and enter your addthis username in the <a href=\"%advanced_settings_url\">Advanced Settings</a> page you can get Analytics.  Example data below.",
          array("signup_url" => "http://www.addthis.com/register",
                "advanced_settings_url" => html::mark_clean(url::site("admin/advanced_settings")))) ?>
    </p>
    <center><img src="http://cache.addthiscdn.com/www/q0039/style/images/dashboard/bkg-myaccount-sample.jpg"></center>
  </div>
</div>
