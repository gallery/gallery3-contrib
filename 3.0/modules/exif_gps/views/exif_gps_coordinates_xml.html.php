<?php defined("SYSPATH") or die("No direct script access.") ?>
<? print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<markers>
<? foreach ($items as $item) { ?>
<? $item_coordinates = ORM::factory("exif_coordinate")->where("item_id", "=", $item->id)->find(); ?>
<? $str_thumb_html = str_replace("&", "&amp;", $item->thumb_img(array("class" => "g-exif-gps-thumbnail"))); ?>
<? $str_thumb_html = str_replace("\'", "&apos;", $str_thumb_html); ?>
<? $str_thumb_html = str_replace("<", "&lt;", $str_thumb_html); ?>
<? $str_thumb_html = str_replace(">", "&gt;", $str_thumb_html); ?>
<? $str_thumb_html = str_replace("\"", "&quot;", $str_thumb_html); ?>
  <marker lat="<?= $item_coordinates->latitude; ?>" lng="<?= $item_coordinates->longitude; ?>" url="<?= url::abs_site("exif_gps/item/{$item->id}"); ?>" thumb="<?=$str_thumb_html; ?>" />
<? } ?>
</markers>
