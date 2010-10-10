<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-photoannotation">
  <h1><?= t("Photo annotation tags maintanance") ?></h1>
  <h3><?= t("Notes:") ?></h3>
  <p><?= t("When deleting a tag this might leave orphaned tag annotations.") ?>
  <br /><br /><?= t("<a href=\"%url\">Back to photo annotation settings</a>", array("url" => url::site("admin/photoannotation/"))) ?>
  <br /><?= t("<a href=\"%url\">Convert existing tag annotations to user annotations</a>", array("url" => url::site("admin/photoannotation/converter/"))) ?></p>
  <? if ($dodeletion): ?>
  <h3><?= t("Maintanance results") ?></h3>
  <p><?= t("%user_orphanes_deleted annotations without a user have been deleted.", array("user_orphanes_deleted" => $user_orphanes_deleted)) ?>
  <br><?= t("%tag_orpanes_deleted annotations without a tag have been deleted.", array("tag_orpanes_deleted" => $tag_orpanes_deleted)) ?>
  <br><?= t("%item_orphanes_deleted annotations without a photo have been deleted.", array("item_orphanes_deleted" => $item_orphanes_deleted)) ?>
  </p>
  <?= t("<a href=\"%url\" class=\"submit ui-state-default ui-corner-all\" style=\"padding: 5px;\">Back</a>", array("url" => url::site("admin/photoannotation/"))) ?>
  <? else: ?>
  <h3><?= t("Orphaned annotations") ?></h3>
  <p><?= t("%user_orphanes_count annotations without a user found.", array("user_orphanes_count" => $user_orphanes_count)) ?>
  <br><?= t("%tag_orpanes_count annotations without a tag found.", array("tag_orpanes_count" => $tag_orpanes_count)) ?>
  <br><?= t("%item_orphanes_count annotations without a photo found.", array("item_orphanes_count" => $item_orphanes_count)) ?>
  </p>
  <? if ($user_orphanes_count == 0 && $tag_orpanes_count == 0 && $item_orphanes_count == 0): ?>
  <?= t("<a href=\"%url\" class=\"submit ui-state-default ui-corner-all\" style=\"padding: 5px;\">Back</a>", array("url" => url::site("admin/photoannotation/"))) ?>
  <? else: ?>
  <?= t("<a href=\"%url\" class=\"submit ui-state-default ui-corner-all\" style=\"padding: 5px;\">Remove all</a>", array("url" => url::site("admin/photoannotation/tagsmaintanance/true"))) ?>
  <? endif ?>
  <? endif ?>
</div>
