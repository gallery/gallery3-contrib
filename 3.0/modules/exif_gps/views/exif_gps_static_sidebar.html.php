<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  $map_api_key = "";
  if (module::get_var("exif_gps", "googlemap_api_key", "") != "") {
    $map_api_key = "&key=" . module::get_var("exif_gps", "googlemap_api_key");
  }
?>
<img src="//maps.google.com/maps/api/staticmap?center=<?=$latitude; ?>,<?=$longitude; ?>&zoom=<?= module::get_var("exif_gps", "sidebar_zoom"); ?>&size=205x214&maptype=<?=$sidebar_map_type ?>&markers=color:red|color:red|<?=$latitude; ?>,<?=$longitude; ?><?= $map_api_key; ?>&sensor=false">
