<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-twitter" class="g-block">
  <h1> <?= t("Share Gallery Items on Twitter") ?> </h1>
  <? if (!$is_registered): ?>
    <p><?= t("Register your Gallery with Twitter at
      <a href=\"%twitter_apps_reg\" target=\"_blank\">dev.twitter.com/apps/new</a>.",
        array("twitter_apps_reg" => "http://dev.twitter.com/apps/new")) ?></p>
    <ul>
      <li><?= t("Application Name: My Awesome Gallery") ?></li>
      <li><?= t("Description: Summary of your Gallery web site") ?></li>
      <li><?= t("Application Web Site: %app_web_site", array("app_web_site" => url::abs_site())) ?></li>
      <li><?= t("Organization: Your name or company") ?></li>
      <li><?= t("Application Type: Browser") ?></li>
      <li><?= t("Callback URL: %callback_url", array("callback_url" => url::abs_site())) ?></li>
      <li><?= t("Default Access Type: Read &amp; Write") ?></li>
    </ul>
    <p><?= t("Then enter the provided OAuth consumer key and secret here.") ?></p>
    <? else: ?>
    <p><?= t("Update your Gallery's
      <a href=\"%twitter_apps\" target=\"_blank\">Twitter application settings</a>, if necessary.",
        array("twitter_apps" => "http://dev.twitter.com/apps")) ?></p>
    <? if (!module::is_active("bitly")): ?>
    <p><?= t("Install and activate the <a href=\"%bitly_module_url\">bit.ly module</a> to shorten
      Gallery URLs in tweets.", array("bitly_module_url" => "http://codex.gallery2.org/Gallery3:Modules:bitly")) ?></p>
    <? endif; ?>
    <? endif; ?>
  <div class="g-block-content">
    <?= $form ?>
  </div>
</div>
