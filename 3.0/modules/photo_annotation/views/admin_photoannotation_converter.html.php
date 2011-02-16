<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-photoannotation">
  <h1><?= t("Photo annotation converter") ?></h1>
  <h3><?= t("Notes:") ?></h3>
  <p><?= t("Here you can convert existing annotations with tags associated with them to annotations with users.") ?><br /><br />
  <?= t("Please be aware that if a photo has already the same user associated with an annotation this annotation will be updated instead of a new one being created. If a photo has more than one annotation associated with the specified tag only one area will be converted and all other annotations with this tag will be removed.") ?>
  <br /><?= t("<a href=\"%url\">Back to photo annotation settings</a>", array("url" => url::site("admin/photoannotation/"))) ?>
  <br /><?= t("<a href=\"%url\">Check for orphaned annotations</a>", array("url" => url::site("admin/photoannotation/tagsmaintanance/"))) ?></p>
  <?= $form ?>
</div>
