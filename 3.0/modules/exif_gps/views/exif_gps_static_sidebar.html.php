<?php defined("SYSPATH") or die("No direct script access.") ?>
<img src="http://maps.google.com/maps/api/staticmap?center=<?=$latitude; ?>,<?=$longitude; ?>&zoom=<?= module::get_var("exif_gps", "sidebar_zoom"); ?>&size=205x214&maptype=<?=$sidebar_map_type ?>&markers=color:red|color:red|<?=$latitude; ?>,<?=$longitude; ?>&sensor=false">
