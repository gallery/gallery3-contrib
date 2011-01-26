<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-bitly" class="g-block">
  <h1> <?= t("bit.ly URL shortening") ?> </h1>
  <p>
  <?= t("bit.ly is a URL shortening service.  In order to use it, you need a <a href=\"%bitly_url\">bit.ly account</a> which will provide an <a href=\"%api_key_url\">API Key</a>, which is also free.",
        array("api_key_url" => "http://bit.ly/a/your_api_key",
              "bitly_url" => "http://bit.ly")) ?>
  </p>
  <div class="g-block-content">
    <? if (!empty($g3_url)): ?>
    <div class="g-module-status g-success">
      <?= t("Your bit.ly login and API key are valid. Your Gallery URLs can now be shortened! This is the shortened URL for this Gallery <a href=\"%g3_url\">%g3_url</a>", array('g3_url' => $g3_url)) ?>
    </div>
    <? endif ?>

    <?= $form ?>
  </div>
</div>
