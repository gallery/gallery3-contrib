<?php defined("SYSPATH") or die("No direct script access.") ?>
<? print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<markers>
<?
$thumb_arr=array();
$items_id=array();;
foreach ($items as $item) {
array_push($items_id, $item->id);
$thumb_arr[$item->id] = $item->thumb_img(array("class" => "g-exif-gps-thumbnail"));
}
$item_coordinates_all = ORM::factory("exif_coordinate")->where("item_id", "IN", $items_id)->find_all();
foreach ($item_coordinates_all as $item_coordinates) {
$str_thumb_html = str_replace("&", "&amp;", $thumb_arr[$item_coordinates->item_id]);
$str_thumb_html = str_replace("\'", "&apos;", $str_thumb_html);
$str_thumb_html = str_replace("<", "&lt;", $str_thumb_html);
$str_thumb_html = str_replace(">", "&gt;", $str_thumb_html);
$str_thumb_html = str_replace("\"", "&quot;", $str_thumb_html);
?>
<marker lat="<?= $item_coordinates->latitude; ?>" lng="<?= $item_coordinates->longitude; ?>" url="<?= url::abs_site("exif_gps/item/$item_coordinates->item_id"); ?>" thumb="<?=$str_thumb_html; ?>" />
<? } ?>
</markers>
