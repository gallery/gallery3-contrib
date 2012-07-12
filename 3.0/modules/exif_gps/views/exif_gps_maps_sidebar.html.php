<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul>
  <? if (isset($album_items) && ($album_items > 0)): ?>
  <li><a href="<?=url::site("exif_gps/map/album/" . $album_id) ?>"><?=t("Map this album"); ?></a></li>
  <? endif ?>
  <? if (isset($user_items) && ($user_items > 0)): ?>
  <li><a href="<?=url::site("exif_gps/map/user/" . $user_id) ?>"><?=t("Map"); ?> <?=$user_name; ?><?=t("'s photos"); ?></a></li>
  <? endif ?>
</ul>
